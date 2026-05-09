<?php

namespace App\Jobs;

use App\Models\BlacklistedUrl;
use App\Models\CrawlContent;
use App\Models\CrawlLog;
use App\Models\DiscoveredLink;
use App\Models\Link;
use App\Services\UrlNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CrawlLinkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries;

    /**
     * Timeout in seconds (Tor can be very slow).
     */
    public int $timeout;

    /**
     * Seconds to wait between retry attempts (exponential backoff).
     */
    public array $backoff;

    public int $linkId;

    public function __construct(int $linkId)
    {
        $this->linkId = $linkId;
        $this->tries = config('crawler.job_tries', 3);
        $this->timeout = config('crawler.job_timeout', 90);
        $this->backoff = config('crawler.job_backoff', [10, 30, 60]);

        // Use dedicated crawler queue
        $this->onQueue(config('crawler.queue', 'crawler'));
    }

    /**
     * Prevent duplicate jobs for the same link from running concurrently.
     * Inspired by Ahmia's per-domain proxy pinning — one circuit per link.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("crawl-link-{$this->linkId}"))
                ->releaseAfter(300)    // release lock after 5 min
                ->expireAfter(600),    // expire lock after 10 min
        ];
    }

    public function handle(): void
    {
        $link = Link::find($this->linkId);

        if (!$link) {
            Log::warning("[Crawler] Link #{$this->linkId} not found — skipping.");
            return;
        }

        // Skip if already marked as duplicate
        if ($link->is_duplicate && $link->canonical_id) {
            Log::info("[Crawler] Link #{$this->linkId} is a known duplicate — skipping.");
            $link->update(['crawl_queue_status' => 'completed']);
            return;
        }

        // Mark as actively processing
        $link->update(['crawl_queue_status' => 'processing']);

        // ── Normalize URL and set canonical_url ──────────────────────────
        $canonicalUrl = UrlNormalizer::normalize($link->url);
        if ($canonicalUrl !== $link->canonical_url) {
            $link->update(['canonical_url' => $canonicalUrl]);
        }

        // ── Check if another link with same canonical_url already exists ─
        $existingCanonical = Link::where('canonical_url', $canonicalUrl)
            ->where('id', '!=', $link->id)
            ->where('is_duplicate', false)
            ->orderBy('id', 'asc')
            ->first();

        if ($existingCanonical) {
            // This link is a URL-level duplicate — mark it
            $link->update([
                'is_duplicate' => true,
                'canonical_id' => $existingCanonical->id,
                'crawl_queue_status' => 'completed',
            ]);
            Log::info("[Crawler] Link #{$link->id} ({$link->url}) → duplicate of #{$existingCanonical->id} (canonical: {$canonicalUrl})");
            $this->recordLog($link, 'skipped', null, "URL-level duplicate of Link #{$existingCanonical->id}");
            return;
        }

        // ── Blacklist Check (like Ahmia's FilterBannedDomains) ────────────
        if ($this->isBlacklisted($link)) {
            Log::info("[Crawler] Skipping blacklisted domain: {$link->url}");
            $this->recordLog($link, 'skipped', null, 'Domain is blacklisted');
            $link->update(['crawl_queue_status' => 'completed']);
            return;
        }

        // ── Sub-domain Depth Check (like Ahmia's SubDomainLimit) ─────────
        $host = parse_url($link->url, PHP_URL_HOST) ?? '';
        $maxSubdomainDepth = config('crawler.max_subdomain_depth', 3);
        if (substr_count($host, '.') > $maxSubdomainDepth) {
            Log::info("[Crawler] Skipping — too many subdomains: {$host}");
            $this->recordLog($link, 'skipped', null, 'Too many subdomains');
            $link->update(['crawl_queue_status' => 'completed']);
            return;
        }

        Log::info("[Crawler] Starting crawl for: {$link->url}");

        $startedAt = now();
        $startTime = microtime(true);

        try {
            $proxy = config('crawler.proxy', 'socks5h://127.0.0.1:9050');
            $timeout = config('crawler.timeout', 30);
            $connectTimeout = config('crawler.connect_timeout', 15);
            $userAgent = config('crawler.user_agent');
            $maxSize = config('crawler.max_download_size', 5 * 1024 * 1024);

            // Use raw Guzzle with explicit curl options to suppress default
            // Guzzle headers (Accept-Encoding, Accept, etc.) that cause 503
            // on strict onion servers. This mirrors what curl does by default.
            //
            // Key options:
            // - http_errors:false  → don't throw on 4xx/5xx, handle in-code
            // - CookieJar          → persist session cookies between requests
            //                        (needed for sites with bot-challenge flows)
            $cookieJar = new \GuzzleHttp\Cookie\CookieJar();

            $client = new \GuzzleHttp\Client([
                'proxy' => $proxy,
                'timeout' => $timeout,
                'connect_timeout' => $connectTimeout,
                'verify' => false,
                'http_errors' => false,          // handle 4xx/5xx ourselves
                'cookies' => $cookieJar,     // persist cookies per crawl
                'allow_redirects' => ['max' => 5, 'strict' => false, 'referer' => false, 'track_redirects' => false],
                'decode_content' => false,          // we decode manually below
                'curl' => [
                    CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5_HOSTNAME,
                    CURLOPT_ENCODING => '',       // let curl negotiate encoding
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                ],
                // Full Tor Browser-like header set, in correct order
                'headers' => [
                    'User-Agent' => $userAgent,
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                    'DNT' => '1',
                ],
            ]);

            $guzzleResponse = $client->get($link->url);
            $httpStatus = $guzzleResponse->getStatusCode();
            $rawBody = (string) $guzzleResponse->getBody();

            // Decode gzip/deflate manually if needed
            $encoding = strtolower($guzzleResponse->getHeaderLine('Content-Encoding'));
            try {
                if ($encoding === 'gzip' || $encoding === 'x-gzip') {
                    $decoded = @gzdecode($rawBody);
                    if ($decoded !== false) {
                        $rawBody = $decoded;
                    }
                } elseif ($encoding === 'deflate') {
                    $decoded = @zlib_decode($rawBody);
                    if ($decoded !== false) {
                        $rawBody = $decoded;
                    }
                }
            } catch (\Exception $e) {
                // Ignore decoding errors and fallback to raw uncompressed body
            }

            // ── Soft-503 / bot-challenge detection ───────────────────────────
            // Sites that return 503 with HTML body are reachable but actively
            // blocking our crawler (rate limit, WAF, JS challenge, etc.).
            // Don't mark uptime as offline — the node IS up, just guarded.

            // Wrap into a Laravel-compatible response object for the rest of the logic
            $response = new \Illuminate\Http\Client\Response(
                new \GuzzleHttp\Psr7\Response(
                    $httpStatus,
                    $guzzleResponse->getHeaders(),
                    $rawBody
                )
            );

            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $html = $response->body();

                // ── Content-Type Check (like Ahmia's FilterResponses) ─────
                $contentType = $response->header('Content-Type') ?? 'text/html';
                if (!$this->isAllowedContentType($contentType)) {
                    Log::info("[Crawler] Skipping non-text content: {$contentType}");
                    $this->recordLog($link, 'skipped', $response->status(), "Content-Type not allowed: {$contentType}", $responseTimeMs);
                    return;
                }

                // ── Size Check ────────────────────────────────────────────
                if (strlen($html) > $maxSize) {
                    $html = substr($html, 0, $maxSize);
                    Log::info("[Crawler] Truncated response to {$maxSize} bytes.");
                }

                // ── Block page detection ──────────────────────────────────────────
                if ($this->isBlockPage($html, $response->status())) {
                    Log::warning("[Crawler] Detected block page/waiting room for: {$link->url} - Skipping metadata update.");
                    $link->update([
                        'crawl_status' => 'failed',
                        'crawl_queue_status' => 'completed',
                        'last_crawled_at' => now(),
                        'crawl_count' => $link->crawl_count + 1,
                        'uptime_status' => 'online',
                    ]);
                    $this->recordLog($link, 'failed', $response->status(), "Blocked/Waiting Room (site is reachable)", $responseTimeMs, 0, strlen($html), $startedAt);
                    return;
                }

                // ── Extract metadata (like Ahmia's parse_item) ────────────
                $title = $this->extractTitle($html);
                $h1 = $this->extractH1($html);
                $description = $this->extractMetaDescription($html);
                $bodyText = $this->extractBodyText($html);
                $language = $this->detectLanguage($html);

                // ── Content-based duplicate detection ─────────────────────
                $contentHash = UrlNormalizer::contentHash($bodyText);
                $contentDuplicate = $this->detectContentDuplicate($link, $contentHash, $bodyText);

                if ($contentDuplicate) {
                    $link->update([
                        'is_duplicate' => true,
                        'canonical_id' => $contentDuplicate->id,
                        'content_hash' => $contentHash,
                        'crawl_status' => 'success',
                        'crawl_queue_status' => 'completed',
                        'last_crawled_at' => now(),
                        'crawl_count' => $link->crawl_count + 1,
                        'uptime_status' => 'online',
                    ]);
                    Log::info("[Crawler] Link #{$link->id} has identical content to #{$contentDuplicate->id} — marked as content duplicate.");
                    $this->recordLog($link, 'success', $response->status(), "Content duplicate of Link #{$contentDuplicate->id}", $responseTimeMs, 0, strlen($html), $startedAt);
                    return;
                }

                // ── Extract all hrefs (like Ahmia's LinkExtractor) ────────
                $discoveredUrls = $this->extractLinks($html, $link->url);

                // ── Persist discovered URLs (deduplicated per parent) ─────
                $newDiscovered = 0;
                foreach ($discoveredUrls as $discovered) {
                    // Normalize discovered URL
                    $normalizedDiscovered = UrlNormalizer::normalize($discovered);

                    $exists = DiscoveredLink::where('parent_url_id', $link->id)
                        ->where('url', $normalizedDiscovered)
                        ->exists();

                    if (!$exists) {
                        DiscoveredLink::create([
                            'parent_url_id' => $link->id,
                            'url' => $normalizedDiscovered,
                        ]);
                        $newDiscovered++;
                    }
                }

                // ── Store/update crawl content (like Ahmia's ES index) ────
                $maxContentLen = config('crawler.max_content_length', 500000);
                CrawlContent::updateOrCreate(
                    ['link_id' => $link->id],
                    [
                        'domain' => parse_url($link->url, PHP_URL_HOST),
                        'h1' => Str::limit($h1 ?? '', 500, ''),
                        'meta_description' => Str::limit($description ?? '', 1000, ''),
                        'body_text' => Str::limit($bodyText ?? '', $maxContentLen, ''),
                        'content_type' => $contentType,
                        'content_length' => strlen($html),
                        'language' => $language,
                    ]
                );

                // ── Update link metadata ──────────────────────────────────
                $updateData = [
                    'crawl_status' => 'success',
                    'crawl_queue_status' => 'completed',
                    'last_crawled_at' => now(),
                    'crawl_count' => $link->crawl_count + 1,
                    'force_recrawl' => false,
                    'uptime_status' => 'online',
                    'content_hash' => $contentHash,
                    'canonical_url' => $canonicalUrl,
                ];

                // ── Determine Best Available Metadata ──
                // ALWAYS prefer freshly crawled data over existing stale data
                $bestTitle = $this->determineBestTitle($title, $h1, $bodyText, $link);
                $bestDescription = $this->determineBestDescription($description, $bodyText, $link);

                if (!empty($bestTitle)) {
                    $updateData['title'] = Str::limit($bestTitle, 200, '');
                    Log::info("[Crawler] Link #{$link->id} - Title: '{$bestTitle}'");
                }

                if (!empty($bestDescription)) {
                    $updateData['description'] = Str::limit($bestDescription, 1000, '');
                    Log::info("[Crawler] Link #{$link->id} - Description: '" . Str::limit($bestDescription, 60) . "'");
                }

                $link->update($updateData);
                Log::info("[Crawler] Link #{$link->id} update executed. Status: success.");

                // ── Record success log ────────────────────────────────────
                $this->recordLog(
                    $link,
                    'success',
                    $response->status(),
                    null,
                    $responseTimeMs,
                    count($discoveredUrls),
                    strlen($html),
                    $startedAt
                );

                Log::info("[Crawler] ✓ Crawled {$link->url} — found " . count($discoveredUrls) . " URLs, {$newDiscovered} new.");

            } else {
                // 503/502/403/429 are often used by WAFs (Cloudflare, etc.) or bot challenges.
                // We also treat 404 as online because the server responded.
                $isReachable = in_array($httpStatus, [403, 429, 502, 503]) || ($httpStatus >= 200 && $httpStatus < 500);

                if ($isReachable) {
                    $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);
                    $link->update([
                        'crawl_status' => 'failed',
                        'crawl_queue_status' => 'completed',
                        'last_crawled_at' => now(),
                        'crawl_count' => $link->crawl_count + 1,
                        'force_recrawl' => false,
                        'uptime_status' => 'online',
                    ]);

                    $message = match($httpStatus) {
                        403 => "Access Forbidden / WAF Blocked (403 — site is reachable)",
                        429 => "Rate Limited / WAF Blocked (429 — site is reachable)",
                        502, 503 => "Bot-challenge / WAF blocked ({$httpStatus} — site is reachable)",
                        404 => "Page Not Found (404 — site is reachable)",
                        default => "HTTP {$httpStatus} (site is reachable but crawl failed)",
                    };

                    $this->recordLog($link, 'failed', $httpStatus, $message, $responseTimeMs, 0, strlen($rawBody), $startedAt);
                    Log::warning("[Crawler] ✗ {$link->url} → {$httpStatus} (site UP but crawl failed). Uptime set to online.");
                    return;
                } else {
                    $this->markFailed($link, "HTTP {$httpStatus}", $httpStatus, $responseTimeMs, $startedAt);
                }
            }

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);
            $this->markFailed($link, 'Connection failed: ' . Str::limit($e->getMessage(), 200), null, $responseTimeMs, $startedAt);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);
            $httpStatus = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $this->markFailed($link, "HTTP {$httpStatus}: " . Str::limit($e->getMessage(), 200), $httpStatus, $responseTimeMs, $startedAt);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);
            $this->markFailed($link, 'Connection timeout: ' . Str::limit($e->getMessage(), 200), null, $responseTimeMs, $startedAt);

        } catch (\Exception $e) {
            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);
            $this->markFailed($link, Str::limit($e->getMessage(), 500), null, $responseTimeMs, $startedAt);
        }
    }

    /**
     * Determine the best title for this link.
     * 
     * AGGRESSIVE STRATEGY — Always replace with best available fresh data:
     * 1. Compare <title> and <h1> — pick the longer, more descriptive one
     * 2. Any existing title < 10 chars is always replaced
     * 3. "Automatically indexed" titles are always replaced
     * 4. Even decent existing titles get replaced if fresh data is clearly better
     */
    private function determineBestTitle(?string $crawledTitle, ?string $h1, string $bodyText, Link $link): ?string
    {
        $lowQualityPatterns = [
            'Onion Link',
            'New Link',
            'Untitled',
            'No Title',
            'Tor Link',
            '.Onion Site',
            'Automatically indexed link from TLaw DW Index.',
            'Index of /',
            'Welcome',
            'Home',
            'Main Page',
            'Not Found',
            '404',
            'Forbidden',
            '403',
            'Error',
        ];

        $currentTitle = trim($link->title ?? '');

        // ── Collect all title candidates ──────────────────────────────────
        $candidates = [];

        $freshTitle = trim($crawledTitle ?? '');
        if (!empty($freshTitle) && strlen($freshTitle) >= 3) {
            $candidates[] = ['text' => $freshTitle, 'source' => 'title_tag', 'priority' => 2];
        }

        $freshH1 = trim($h1 ?? '');
        if (!empty($freshH1) && strlen($freshH1) >= 3) {
            $candidates[] = ['text' => $freshH1, 'source' => 'h1_tag', 'priority' => 2];
        }

        // Body text fallback
        if (!empty($bodyText)) {
            $sentences = preg_split('/[.!?\n]+/', $bodyText, 5);
            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if (strlen($sentence) >= 10 && strlen($sentence) <= 150) {
                    $candidates[] = ['text' => $sentence, 'source' => 'body', 'priority' => 1];
                    break;
                }
            }
        }

        // Filter out low-quality candidates
        $candidates = array_filter($candidates, function ($c) use ($lowQualityPatterns) {
            if (in_array($c['text'], $lowQualityPatterns))
                return false;
            if (stripos($c['text'], 'Automatically indexed') !== false)
                return false;
            if (strlen($c['text']) < 3)
                return false;
            return true;
        });

        if (empty($candidates)) {
            return null; // Nothing better found
        }

        // ── Pick the best candidate ───────────────────────────────────────
        // Sort: highest priority first, then longest text wins
        usort($candidates, function ($a, $b) {
            if ($a['priority'] !== $b['priority']) {
                return $b['priority'] - $a['priority'];
            }
            return strlen($b['text']) - strlen($a['text']);
        });

        $best = $candidates[0]['text'];

        // ── Decide whether to replace current title ───────────────────────
        $isCurrentLowQuality = empty($currentTitle)
            || strlen($currentTitle) < 10  // Titles shorter than 10 chars are considered too generic
            || in_array($currentTitle, $lowQualityPatterns)
            || stripos($currentTitle, 'Automatically indexed') !== false;

        // Always replace if current is low quality
        if ($isCurrentLowQuality) {
            Log::info("[Crawler] Link #{$link->id} - Replacing low-quality title '{$currentTitle}' → '{$best}'");
            return $best;
        }

        // Even if current is "ok", replace if fresh data is significantly more descriptive
        if (strlen($best) > strlen($currentTitle) * 1.5 && strlen($best) >= 10) {
            Log::info("[Crawler] Link #{$link->id} - Upgrading title '{$currentTitle}' → '{$best}' (more descriptive)");
            return $best;
        }

        // Replace if the current title equals the domain name (too generic)
        $domain = parse_url($link->url, PHP_URL_HOST) ?? '';
        if (strtolower($currentTitle) === strtolower($domain)) {
            Log::info("[Crawler] Link #{$link->id} - Replacing domain-name title with: '{$best}'");
            return $best;
        }

        return null; // Current title is fine, don't touch it
    }

    /**
     * Determine the best description for this link.
     * 
     * AGGRESSIVE STRATEGY — Always replace stale/template descriptions:
     * 1. "Automatically indexed" is ALWAYS replaced, no questions asked
     * 2. Fresh meta description from crawl always wins over existing
     * 3. Body text snippet used as fallback for low-quality descriptions
     */
    private function determineBestDescription(?string $metaDescription, string $bodyText, Link $link): ?string
    {
        $currentDesc = trim($link->description ?? '');

        // ── Is current description garbage? ───────────────────────────────
        $isCurrentLowQuality = empty($currentDesc)
            || strlen($currentDesc) < 15
            || stripos($currentDesc, 'Automatically indexed') !== false
            || stripos($currentDesc, 'No description') !== false
            || $currentDesc === '...'
            || $currentDesc === 'No description provided.';

        // ── Fresh meta description from crawl ─────────────────────────────
        $freshDesc = trim($metaDescription ?? '');
        $hasFreshDesc = !empty($freshDesc) && strlen($freshDesc) >= 10;

        if ($hasFreshDesc) {
            // Anti-template check: skip if this exact description exists on 5+ other links
            $sameDescCount = Link::where('description', $freshDesc)
                ->where('id', '!=', $link->id)
                ->count();

            if ($sameDescCount >= 5) {
                Log::info("[Crawler] Link #{$link->id} — skipping template description (found on {$sameDescCount} other links).");
                // Fall through to body text
            } else {
                // ALWAYS use fresh meta description if current is low quality
                if ($isCurrentLowQuality) {
                    Log::info("[Crawler] Link #{$link->id} - Replacing garbage description with meta: '" . Str::limit($freshDesc, 60) . "'");
                    return $freshDesc;
                }

                // Even if current is "ok", replace if fresh is significantly longer/better
                if (strlen($freshDesc) > strlen($currentDesc) * 1.3 && strlen($freshDesc) >= 30) {
                    Log::info("[Crawler] Link #{$link->id} - Upgrading description with richer meta: '" . Str::limit($freshDesc, 60) . "'");
                    return $freshDesc;
                }

                // Current description is decent and fresh isn't significantly better
                return null;
            }
        }

        // ── Body text fallback ────────────────────────────────────────────
        if ($isCurrentLowQuality && !empty($bodyText)) {
            $snippet = Str::limit(trim($bodyText), 300, '...');
            if (strlen($snippet) >= 20) {
                Log::info("[Crawler] Link #{$link->id} - Generating description from body text.");
                return $snippet;
            }
        }

        return null;
    }

    /**
     * Detect if another link already has the same content hash.
     * Only flags as duplicate if same domain and high content similarity.
     */
    private function detectContentDuplicate(Link $link, string $contentHash, string $bodyText): ?Link
    {
        // Skip detection if body text is too short (generic pages)
        if (strlen($bodyText) < 200) {
            return null;
        }

        // Find other links with the same content hash on the SAME domain
        $domain = UrlNormalizer::extractDomain($link->url);

        $candidates = Link::where('content_hash', $contentHash)
            ->where('id', '!=', $link->id)
            ->where('is_duplicate', false)
            ->get();

        foreach ($candidates as $candidate) {
            $candidateDomain = UrlNormalizer::extractDomain($candidate->url);

            // Only mark as duplicate if same domain
            if ($candidateDomain !== $domain) {
                continue;
            }

            // Verify with actual content similarity (hash collisions are rare but possible)
            $candidateContent = CrawlContent::where('link_id', $candidate->id)->first();
            if ($candidateContent && !empty($candidateContent->body_text)) {
                $similarity = UrlNormalizer::similarity($bodyText, $candidateContent->body_text);
                if ($similarity >= 0.90) {
                    Log::info("[Crawler] Content similarity {$similarity} between #{$link->id} and #{$candidate->id}");
                    return $candidate;
                }
            } else {
                // No content to compare but hash matches — assume duplicate
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Check if the domain is blacklisted (like Ahmia's BANNED_DOMAINS).
     */
    private function isBlacklisted(Link $link): bool
    {
        $host = parse_url($link->url, PHP_URL_HOST) ?? '';

        return BlacklistedUrl::where(function ($q) use ($host, $link) {
            $q->where('url_pattern', $host)
                ->orWhere('url_pattern', $link->url)
                ->orWhereRaw('? LIKE CONCAT("%", url_pattern, "%")', [$link->url]);
        })->exists();
    }

    /**
     * Check if the content type is allowed (like Ahmia's FilterResponses).
     */
    private function isAllowedContentType(string $contentType): bool
    {
        $allowed = config('crawler.allowed_content_types', ['text/html', 'text/plain']);

        foreach ($allowed as $type) {
            if (stripos($contentType, $type) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract <title> from HTML.
     */
    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m)) {
            return trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')));
        }
        return null;
    }

    /**
     * Extract first <h1> from HTML (like Ahmia's h1 field).
     */
    private function extractH1(string $html): ?string
    {
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/si', $html, $m)) {
            return trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')));
        }
        return null;
    }

    /**
     * Extract meta description.
     */
    private function extractMetaDescription(string $html): ?string
    {
        // name="description" content="..."
        if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/si', $html, $m)) {
            return trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')));
        }
        // content="..." name="description"
        if (preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']description["\'][^>]*>/si', $html, $m)) {
            return trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')));
        }
        return null;
    }

    /**
     * Extract visible text content from HTML (like Ahmia's html2text).
     */
    private function extractBodyText(string $html): string
    {
        // Remove script, style, noscript
        $clean = preg_replace('/<(script|style|noscript)[^>]*>.*?<\/\1>/si', '', $html);

        // Remove all HTML tags
        $text = strip_tags($clean);

        // Decode entities
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Detect page language from <html lang="xx"> or <meta http-equiv="Content-Language">.
     */
    private function detectLanguage(string $html): ?string
    {
        if (preg_match('/<html[^>]+lang=["\']([a-zA-Z\-]+)["\'][^>]*>/i', $html, $m)) {
            return strtolower(substr($m[1], 0, 10));
        }
        if (preg_match('/<meta[^>]+http-equiv=["\']Content-Language["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $html, $m)) {
            return strtolower(substr($m[1], 0, 10));
        }
        return null;
    }

    /**
     * Extract unique, absolute href links from HTML,
     * filtered to exclude binary files (same as Ahmia spider rules).
     */
    private function extractLinks(string $html, string $baseUrl): array
    {
        $base = parse_url($baseUrl);
        $baseScheme = $base['scheme'] ?? 'http';
        $baseHost = $base['host'] ?? '';

        preg_match_all('/<a[^>]+href=["\']([^"\'#?\s][^"\']*)["\'][^>]*>/i', $html, $matches);

        $blockedExtensions = config('crawler.blocked_extensions', []);
        $maxLinks = config('crawler.max_links_per_page', 5000);
        $maxUrlLen = config('crawler.max_url_length', 2048);

        $links = [];
        $seen = [];

        foreach ($matches[1] as $href) {
            $href = trim($href);

            // Skip mailto, javascript, data, fragment-only, etc.
            if (
                empty($href)
                || str_starts_with($href, 'mailto:')
                || str_starts_with($href, 'javascript:')
                || str_starts_with($href, 'data:')
                || str_starts_with($href, 'tel:')
            ) {
                continue;
            }

            // Make absolute URL
            if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
                $absolute = $href;
            } elseif (str_starts_with($href, '//')) {
                $absolute = $baseScheme . ':' . $href;
            } elseif (str_starts_with($href, '/')) {
                $absolute = $baseScheme . '://' . $baseHost . $href;
            } else {
                // Relative path
                $path = rtrim(dirname($base['path'] ?? '/'), '/');
                $absolute = $baseScheme . '://' . $baseHost . $path . '/' . $href;
            }

            // Skip blocked extensions (same list as Ahmia)
            $extPattern = '/\.(' . implode('|', $blockedExtensions) . ')$/i';
            if (preg_match($extPattern, parse_url($absolute, PHP_URL_PATH) ?? '')) {
                continue;
            }

            // Limit URL length
            if (strlen($absolute) > $maxUrlLen) {
                continue;
            }

            // Normalize for deduplication
            $normalized = UrlNormalizer::normalize($absolute);
            if (isset($seen[$normalized])) {
                continue;
            }

            $seen[$normalized] = true;
            $links[] = $absolute;

            // Cap per-page links
            if (count($links) >= $maxLinks) {
                break;
            }
        }

        return $links;
    }

    /**
     * Mark a link as failed and record the crawl log.
     */
    private function markFailed(
        Link $link,
        string $reason,
        ?int $httpStatus = null,
        ?int $responseTimeMs = null,
        $startedAt = null
    ): void {
        $link->update([
            'crawl_status' => 'failed',
            'crawl_queue_status' => 'failed',
            'last_crawled_at' => now(),
            'crawl_count' => $link->crawl_count + 1,
            'force_recrawl' => false,
            'uptime_status' => 'offline',
        ]);

        $this->recordLog(
            $link,
            'failed',
            $httpStatus,
            Str::limit($reason, 1000),
            $responseTimeMs,
            0,
            0,
            $startedAt
        );

        Log::warning("[Crawler] ✗ Failed to crawl {$link->url}: {$reason}");
    }

    /**
     * Record a crawl log entry.
     */
    private function recordLog(
        Link $link,
        string $status,
        ?int $httpStatus = null,
        ?string $errorMessage = null,
        ?int $responseTimeMs = null,
        int $discoveredCount = 0,
        int $contentLength = 0,
        $startedAt = null
    ): void {
        CrawlLog::create([
            'link_id' => $link->id,
            'status' => $status,
            'http_status' => $httpStatus,
            'error_message' => $errorMessage,
            'response_time_ms' => $responseTimeMs,
            'discovered_count' => $discoveredCount,
            'content_length' => $contentLength,
            'started_at' => $startedAt ?? now(),
            'finished_at' => now(),
        ]);
    }
    /**
     * Detect if the HTML content represents a block page or waiting room.
     */
    private function isBlockPage(string $html, int $status): bool
    {
        if ($status === 503 || $status === 403 || $status === 429) {
            return true;
        }

        $blockPatterns = [
            '/<title>.*?(Waiting Room|Blocked|Security Check|Cloudflare|Challenge|Just a moment).*?<\/title>/i',
            '/Checking your browser before accessing/i',
            '/Please enable cookies/i',
            '/Access Denied/i',
            '/Attention Required! | Cloudflare/i',
            '/ddos-guard/i',
            '/sucuri/i',
            '/Checking if the site connection is secure/i',
        ];

        foreach ($blockPatterns as $pattern) {
            if (preg_match($pattern, $html)) {
                return true;
            }
        }

        return false;
    }
}
