<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Crawl Interval (days)
    |--------------------------------------------------------------------------
    | How many days between automatic re-crawls. Links crawled within this
    | window are considered "fresh" and skipped unless force_recrawl = true.
    */
    'interval_days' => (int) env('CRAWLER_INTERVAL_DAYS', 4),

    /*
    |--------------------------------------------------------------------------
    | Tor/SOCKS Proxy
    |--------------------------------------------------------------------------
    | The SOCKS5 proxy URL used for all crawl requests.
    | Default is the standard Tor SOCKS5 port.
    */
    'proxy' => env('CRAWLER_PROXY', 'socks5h://127.0.0.1:9050'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeouts (seconds)
    |--------------------------------------------------------------------------
    */
    'timeout'         => (int) env('CRAWLER_TIMEOUT', 30),
    'connect_timeout' => (int) env('CRAWLER_CONNECT_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Job Configuration
    |--------------------------------------------------------------------------
    */
    'job_tries'   => (int) env('CRAWLER_JOB_TRIES', 3),
    'job_timeout' => (int) env('CRAWLER_JOB_TIMEOUT', 90),
    'job_backoff' => [10, 30, 60], // seconds between retries (exponential)

    /*
    |--------------------------------------------------------------------------
    | Concurrency & Rate Limiting
    |--------------------------------------------------------------------------
    | max_concurrent: Maximum crawler jobs that can run simultaneously.
    | domain_delay:   Minimum seconds between requests to the same domain.
    */
    'max_concurrent' => (int) env('CRAWLER_MAX_CONCURRENT', 10),
    'domain_delay'   => (int) env('CRAWLER_DOMAIN_DELAY', 2),

    /*
    |--------------------------------------------------------------------------
    | Link Extraction Limits (inspired by Ahmia)
    |--------------------------------------------------------------------------
    | max_links_per_page:   Cap on href links extracted from a single page.
    | max_links_per_domain: Global cap on discovered links per domain.
    | max_url_length:       Skip URLs longer than this.
    | max_content_length:   Max plain-text content to store per page.
    */
    'max_links_per_page'   => (int) env('CRAWLER_MAX_LINKS_PER_PAGE', 5000),
    'max_links_per_domain' => (int) env('CRAWLER_MAX_LINKS_PER_DOMAIN', 50000),
    'max_url_length'       => (int) env('CRAWLER_MAX_URL_LENGTH', 2048),
    'max_content_length'   => (int) env('CRAWLER_MAX_CONTENT_LENGTH', 500000),

    /*
    |--------------------------------------------------------------------------
    | Crawl Depth
    |--------------------------------------------------------------------------
    | If recursive crawling is enabled, how deep the spider should follow links.
    | 0 = only crawl submitted URLs, no recursive discovery.
    | Like Ahmia's DEPTH_LIMIT = 5 setting.
    */
    'depth_limit' => (int) env('CRAWLER_DEPTH_LIMIT', 0),

    /*
    |--------------------------------------------------------------------------
    | Content-Type Whitelist
    |--------------------------------------------------------------------------
    | Only store/index pages that match these content types.
    | Same concept as Ahmia's FilterResponses middleware.
    */
    'allowed_content_types' => [
        'text/html',
        'text/plain',
        'application/xhtml+xml',
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocked File Extensions
    |--------------------------------------------------------------------------
    | Same list as Ahmia's CrawlSpider deny_extensions.
    | Links ending with these are skipped during extraction.
    */
    'blocked_extensions' => [
        '7z', 'apk', 'bin', 'bz2', 'dmg', 'exe', 'gif', 'gz', 'ico', 'iso',
        'jar', 'jpg', 'jpeg', 'mp3', 'mp4', 'm4a', 'ogg', 'pdf', 'png',
        'rar', 'svg', 'tar', 'tgz', 'webm', 'webp', 'xz', 'zip',
    ],

    /*
    |--------------------------------------------------------------------------
    | User-Agent
    |--------------------------------------------------------------------------
    | Identifies the crawler. Matches Ahmia's Tor Browser UA philosophy.
    */
    'user_agent' => env(
        'CRAWLER_USER_AGENT',
        'Mozilla/5.0 (Windows NT 10.0; rv:128.0) Gecko/20100101 Firefox/128.0'
    ),

    /*
    |--------------------------------------------------------------------------
    | Download Size Limit (bytes)
    |--------------------------------------------------------------------------
    | Max response body size. Ahmia uses 5 MB.
    */
    'max_download_size' => (int) env('CRAWLER_MAX_DOWNLOAD_SIZE', 5 * 1024 * 1024),

    /*
    |--------------------------------------------------------------------------
    | Sub-domain Depth Limit
    |--------------------------------------------------------------------------
    | Inspired by Ahmia's SubDomainLimit middleware.
    | Ignore URLs with more than this many dot-separated segments.
    */
    'max_subdomain_depth' => (int) env('CRAWLER_MAX_SUBDOMAIN_DEPTH', 3),

    /*
    |--------------------------------------------------------------------------
    | Batch Size
    |--------------------------------------------------------------------------
    | How many links to dispatch per scheduler run.
    | 0 = no limit, dispatch all eligible.
    */
    'batch_size' => (int) env('CRAWLER_BATCH_SIZE', 0),

    /*
    |--------------------------------------------------------------------------
    | Queue Name
    |--------------------------------------------------------------------------
    | Which Laravel queue to dispatch crawl jobs to.
    */
    'queue' => env('CRAWLER_QUEUE', 'crawler'),
];
