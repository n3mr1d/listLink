<?php

namespace App\Jobs;

use App\Models\CrawledEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailCrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;
    public array $backoff = [15, 45];

    public function __construct(
        public readonly string $url,
        public readonly string $jobId,
        public readonly bool   $useProxy = false,
    ) {
        $this->onQueue('email-crawler');
    }

    /**
     * Prevent the same URL from being double-processed.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('email-crawl-' . md5($this->url)))
                ->releaseAfter(120)
                ->expireAfter(300),
        ];
    }

    public function handle(): void
    {
        Log::info("[EmailCrawler] Crawling: {$this->url}");

        $startTime = microtime(true);

        try {
            $html = $this->fetchPage($this->url);

            if ($html === null) {
                Log::warning("[EmailCrawler] Failed to fetch: {$this->url}");
                return;
            }

            $pageTitle = $this->extractTitle($html);
            $emails    = $this->extractEmails($html);

            $created = 0;
            $dupes   = 0;

            foreach ($emails as $email) {
                if (! CrawledEmail::isValidEmail($email)) {
                    continue;
                }

                $result = CrawledEmail::upsertEmail(
                    email:      $email,
                    sourceUrl:  $this->url,
                    pageTitle:  $pageTitle,
                    sourceType: 'auto_crawl',
                    jobId:      $this->jobId,
                );

                $result['created'] ? $created++ : $dupes++;
            }

            $elapsed = round((microtime(true) - $startTime) * 1000);
            Log::info("[EmailCrawler] ✓ {$this->url} — " . count($emails) . " raw, {$created} new, {$dupes} dupes ({$elapsed}ms)");

        } catch (\Exception $e) {
            Log::error("[EmailCrawler] ✗ {$this->url}: " . Str::limit($e->getMessage(), 300));
            throw $e;
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────

    private function fetchPage(string $url): ?string
    {
        $options = [
            'timeout'         => 25,
            'connect_timeout' => 10,
            'verify'          => false,
            'allow_redirects' => ['max' => 5],
        ];

        if ($this->useProxy) {
            $options['proxy'] = config('crawler.proxy', 'socks5h://127.0.0.1:9050');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withOptions($options)
                ->withHeaders(['User-Agent' => config('crawler.user_agent', 'Mozilla/5.0 HiddenLine EmailBot/1.0')])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $contentType = $response->header('Content-Type') ?? '';
            if (stripos($contentType, 'text/') === false && stripos($contentType, 'html') === false) {
                return null;
            }

            $body = $response->body();
            // Limit to 3MB to keep it snappy
            return strlen($body) > 3_145_728 ? substr($body, 0, 3_145_728) : $body;

        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Extract all email addresses from raw HTML + text.
     * Strategy: scan both raw text AND href="mailto:..." attributes.
     */
    private function extractEmails(string $html): array
    {
        $emails = [];

        // 1) mailto: links — most reliable source
        preg_match_all('/href=["\']mailto:([^"\'?\s]+)/i', $html, $mailtoMatches);
        foreach ($mailtoMatches[1] as $e) {
            $emails[] = strtolower(trim(urldecode($e)));
        }

        // 2) Strip scripts/styles, then scan visible text
        $clean = preg_replace('/<(script|style|noscript)[^>]*>.*?<\/\1>/si', '', $html);
        $text  = html_entity_decode(strip_tags($clean), ENT_QUOTES, 'UTF-8');

        // RFC 5322 simplified regex — covers 99% of real-world emails
        $pattern = '/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,12}/';
        preg_match_all($pattern, $text, $textMatches);
        foreach ($textMatches[0] as $e) {
            $emails[] = strtolower(trim($e));
        }

        // Also scan raw HTML (catches obfuscated emails in data attributes)
        preg_match_all($pattern, $html, $htmlMatches);
        foreach ($htmlMatches[0] as $e) {
            $emails[] = strtolower(trim($e));
        }

        return array_values(array_unique($emails));
    }

    private function extractTitle(string $html): ?string
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m)) {
            return trim(strip_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')));
        }
        return null;
    }
}
