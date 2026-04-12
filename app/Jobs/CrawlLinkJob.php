<?php

namespace App\Jobs;

use App\Models\BlacklistedUrl;
use App\Models\CrawlContent;
use App\Models\CrawlLog;
use App\Models\DiscoveredLink;
use App\Models\Link;
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

        // Mark as actively processing
        $link->update(['crawl_queue_status' => 'processing']);

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

            $response = Http::withOptions([
                'proxy' => $proxy,
                'timeout' => $timeout,
                'connect_timeout' => $connectTimeout,
                'verify' => false,
            ])
                ->withHeaders([
                    'User-Agent' => $userAgent,
                ])
                ->get($link->url);

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

                // ── Extract metadata (like Ahmia's parse_item) ────────────
                $title = $this->extractTitle($html);
                $h1 = $this->extractH1($html);
                $description = $this->extractMetaDescription($html);
                $bodyText = $this->extractBodyText($html);
                $language = $this->detectLanguage($html);

                // ── Extract all hrefs (like Ahmia's LinkExtractor) ────────
                $discoveredUrls = $this->extractLinks($html, $link->url);

                // ── Persist discovered URLs (deduplicated per parent) ─────
                $newDiscovered = 0;
                foreach ($discoveredUrls as $discovered) {
                    $exists = DiscoveredLink::where('parent_url_id', $link->id)
                        ->where('url', $discovered)
                        ->exists();

                    if (!$exists) {
                        DiscoveredLink::create([
                            'parent_url_id' => $link->id,
                            'url' => $discovered,
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
                ];

                // ── Determine Best Available Metadata ──
                $bestTitle = trim($title ?: ($h1 ?: ''));
                $bestDescription = trim($description ?: '');
                
                // If meta description is missing, try a clean snippet of the body text
                if (empty($bestDescription) && !empty($bodyText)) {
                    $bestDescription = Str::limit($bodyText, 300, '...');
                }

                // ── Update Logic ──
                $lowQualityTitles = [
                    'Onion Link', 'New Link', 'Untitled', 'No Title', 'Tor Link', '.Onion Site',
                    'Automatically indexed link from TLaw DW Index.'
                ];
                
                $currentTitle = trim($link->title ?? '');
                $isLowQualityTitle = empty($currentTitle) 
                    || strlen($currentTitle) < 5 
                    || in_array($currentTitle, $lowQualityTitles)
                    || stripos($currentTitle, 'Automatically indexed') !== false;

                if ($isLowQualityTitle && !empty($bestTitle)) {
                    $updateData['title'] = Str::limit($bestTitle, 200, '');
                    Log::info("[Crawler] Link #{$link->id} - Overwriting low-quality title with: '{$bestTitle}'");
                }

                $lowQualityDescriptions = [
                    'No description provided.', 'No description', '...', 
                    'Automatically indexed', 'Automatically indexed link from TLaw DW Index.'
                ];
                $currentDesc = trim($link->description ?? '');
                $isLowQualityDesc = empty($currentDesc) 
                    || strlen($currentDesc) < 15 
                    || in_array($currentDesc, $lowQualityDescriptions)
                    || stripos($currentDesc, 'Automatically indexed') !== false;

                if ($isLowQualityDesc && !empty($bestDescription)) {
                    $updateData['description'] = Str::limit($bestDescription, 1000, '');
                    Log::info("[Crawler] Link #{$link->id} - Overwriting low-quality description with: '" . Str::limit($bestDescription, 40) . "'");
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
                $this->markFailed($link, "HTTP {$response->status()}", $response->status(), $responseTimeMs, $startedAt);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);
            $this->markFailed($link, 'Connection timeout: ' . Str::limit($e->getMessage(), 200), null, $responseTimeMs, $startedAt);

        } catch (\Exception $e) {
            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);
            $this->markFailed($link, Str::limit($e->getMessage(), 500), null, $responseTimeMs, $startedAt);
        }
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

            // Deduplicate
            $normalized = strtolower(rtrim($absolute, '/'));
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
}
