<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * URL Normalization & Deduplication Service
 * 
 * Normalizes .onion URLs into a canonical form to detect duplicates like:
 *   - http://example.onion/wiki/index.php/Main_Page
 *   - http://example.onion/
 * 
 * Also provides content-based similarity hashing.
 */
class UrlNormalizer
{
    /**
     * Normalize a URL into its canonical form.
     *
     * Steps:
     * 1. Lowercase scheme and host
     * 2. Remove trailing slashes
     * 3. Remove default ports
     * 4. Remove fragment (#...)
     * 5. Remove tracking/session query parameters
     * 6. Sort remaining query parameters
     * 7. Remove common index filenames (index.html, index.php, default.asp)
     */
    public static function normalize(string $url): string
    {
        $url = trim($url);
        if (empty($url)) return '';

        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['host'])) return $url;

        // 1. Lowercase scheme and host
        $scheme = strtolower($parsed['scheme'] ?? 'http');
        $host = strtolower($parsed['host']);

        // 2. Remove default ports
        $port = $parsed['port'] ?? null;
        if (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443)) {
            $port = null;
        }

        // 3. Normalize path
        $path = $parsed['path'] ?? '/';
        
        // Remove common index files
        $indexFiles = [
            '/index.html', '/index.htm', '/index.php', '/index.asp',
            '/default.html', '/default.htm', '/default.asp',
        ];
        foreach ($indexFiles as $indexFile) {
            if (str_ends_with(strtolower($path), $indexFile)) {
                $path = substr($path, 0, -strlen($indexFile)) ?: '/';
                break;
            }
        }

        // Remove trailing slash (except for root)
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // Resolve .. and . in path
        $path = self::resolvePath($path);

        // 4. Remove fragment (#)
        // Already excluded by not using $parsed['fragment']

        // 5. Filter and sort query parameters
        $query = $parsed['query'] ?? '';
        $query = self::normalizeQuery($query);

        // Build canonical URL
        $canonical = $scheme . '://' . $host;
        if ($port) {
            $canonical .= ':' . $port;
        }
        $canonical .= $path;
        if (!empty($query)) {
            $canonical .= '?' . $query;
        }

        return $canonical;
    }

    /**
     * Generate a content hash for similarity detection.
     * 
     * Uses a stripped, normalized version of the body text to detect when
     * two different URLs serve the same content (e.g., / and /index.php/Main_Page).
     */
    public static function contentHash(string $bodyText): string
    {
        // Strip all whitespace and lowercase for comparison
        $normalized = strtolower(preg_replace('/\s+/', '', $bodyText));
        
        // Use first 10000 chars for hash (enough for similarity, fast to compute)
        $normalized = substr($normalized, 0, 10000);

        return md5($normalized);
    }

    /**
     * Calculate similarity ratio between two texts (0.0 to 1.0).
     * Uses a fast approach: compare word frequency vectors.
     */
    public static function similarity(string $text1, string $text2): float
    {
        if (empty($text1) || empty($text2)) return 0.0;

        // Quick hash check first
        if (self::contentHash($text1) === self::contentHash($text2)) {
            return 1.0;
        }

        // Word frequency comparison (cosine similarity approximation)
        $words1 = array_count_values(str_word_count(strtolower(substr($text1, 0, 5000)), 1));
        $words2 = array_count_values(str_word_count(strtolower(substr($text2, 0, 5000)), 1));

        $allWords = array_unique(array_merge(array_keys($words1), array_keys($words2)));

        $dotProduct = 0;
        $mag1 = 0;
        $mag2 = 0;

        foreach ($allWords as $word) {
            $v1 = $words1[$word] ?? 0;
            $v2 = $words2[$word] ?? 0;
            $dotProduct += $v1 * $v2;
            $mag1 += $v1 * $v1;
            $mag2 += $v2 * $v2;
        }

        $denominator = sqrt($mag1) * sqrt($mag2);
        if ($denominator == 0) return 0.0;

        return round($dotProduct / $denominator, 4);
    }

    /**
     * Check if two URLs point to the same domain.
     */
    public static function sameDomain(string $url1, string $url2): bool
    {
        $host1 = strtolower(parse_url($url1, PHP_URL_HOST) ?? '');
        $host2 = strtolower(parse_url($url2, PHP_URL_HOST) ?? '');
        return $host1 === $host2 && !empty($host1);
    }

    /**
     * Extract the base domain from a URL.
     */
    public static function extractDomain(string $url): string
    {
        return strtolower(parse_url($url, PHP_URL_HOST) ?? '');
    }

    /**
     * Resolve . and .. segments in a path.
     */
    private static function resolvePath(string $path): string
    {
        $segments = explode('/', $path);
        $resolved = [];

        foreach ($segments as $segment) {
            if ($segment === '..') {
                array_pop($resolved);
            } elseif ($segment !== '.' && $segment !== '') {
                $resolved[] = $segment;
            }
        }

        return '/' . implode('/', $resolved);
    }

    /**
     * Normalize query string: remove tracking params, sort remaining.
     */
    private static function normalizeQuery(string $query): string
    {
        if (empty($query)) return '';

        parse_str($query, $params);

        // Remove common tracking/session parameters
        $stripParams = [
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'fbclid', 'gclid', 'ref', 'source', 'PHPSESSID', 'sid', 'jsessionid',
            'token', 'timestamp', '_', 'nocache', 'cb', 'rand',
        ];

        foreach ($stripParams as $param) {
            unset($params[$param]);
            unset($params[strtolower($param)]);
        }

        if (empty($params)) return '';

        // Sort by key for deterministic output 
        ksort($params);

        return http_build_query($params);
    }
}
