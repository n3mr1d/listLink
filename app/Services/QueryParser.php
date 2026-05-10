<?php

namespace App\Services;

/**
 * QueryParser — Advanced Google-like query language parser.
 *
 * Supports:
 *  - Plain multi-keyword:  ai website
 *  - Exact phrase:         "exact keyword"
 *  - Boolean OR:           ai OR website
 *  - Exclusion:            -spam  / NOT spam
 *  - Field scoping:        title:vpn url:onion category:forum status:online
 *  - Wildcard:             crypt* tor*
 *  - Date ranges:          after:2024-01-01 before:2025-01-01
 *  - Numeric filters:      likes:>5
 */
class QueryParser
{
    public function parse(string $raw): array
    {
        $result = [
            'original'     => $raw,
            'must'         => [],   // AND / plain terms — ALL must match
            'should'       => [],   // OR terms — ANY may match
            'must_not'     => [],   // NOT / -word — NONE should match
            'phrases'      => [],   // "exact phrases"
            'wildcards'    => [],   // crypt*
            'fields'       => [],   // title:x, url:x, category:x, status:x
            'filters'      => [],   // after:, before:, likes:
            'plain'        => '',   // remaining plain text
            'mode'         => 'standard', // standard|boolean|phrase|wildcard|advanced
            'has_or'       => false,
            'has_not'      => false,
            'has_phrase'   => false,
        ];

        $remaining = $raw;

        // ── 1. Extract quoted exact phrases ──────────────────────────────
        preg_match_all('/"([^"]+)"/', $remaining, $phraseMatches);
        foreach ($phraseMatches[1] as $phrase) {
            $phrase = trim($phrase);
            if ($phrase !== '') {
                $result['phrases'][] = $phrase;
                $result['has_phrase'] = true;
            }
        }
        $remaining = preg_replace('/"[^"]+"/', '', $remaining);

        // ── 2. Extract NOT / -word exclusions ────────────────────────────
        preg_match_all('/(?:NOT\s+|(?<!\w)-)(\S+)/i', $remaining, $notMatches);
        foreach ($notMatches[1] as $notWord) {
            $notWord = trim($notWord);
            if ($notWord !== '') {
                $result['must_not'][] = strtolower($notWord);
                $result['has_not'] = true;
            }
        }
        $remaining = preg_replace('/(?:NOT\s+|(?<!\w)-)\S+/i', '', $remaining);

        // ── 3. Extract field-scoped terms ─────────────────────────────────
        $fieldPattern = '/\b(title|url|category|status|site|type):([\S]+)/i';
        preg_match_all($fieldPattern, $remaining, $fieldMatches, PREG_SET_ORDER);
        foreach ($fieldMatches as $m) {
            $result['fields'][strtolower($m[1])][] = strtolower($m[2]);
        }
        $remaining = preg_replace($fieldPattern, '', $remaining);

        // ── 4. Extract numeric/date filters ──────────────────────────────
        $filterPattern = '/\b(after|before|likes|rating):([\S]+)/i';
        preg_match_all($filterPattern, $remaining, $filterMatches, PREG_SET_ORDER);
        foreach ($filterMatches as $m) {
            $key = strtolower($m[1]);
            $val = $m[2];
            if (preg_match('/^([><!=]+)(\d+)$/', $val, $opMatch)) {
                $result['filters'][$key] = ['op' => $opMatch[1], 'val' => (int)$opMatch[2]];
            } else {
                $result['filters'][$key] = $val;
            }
        }
        $remaining = preg_replace($filterPattern, '', $remaining);

        // ── 5. Extract wildcard terms ─────────────────────────────────────
        preg_match_all('/\b(\w{2,})\*/i', $remaining, $wildcardMatches);
        foreach ($wildcardMatches[1] as $wc) {
            $result['wildcards'][] = strtolower($wc);
        }
        $remaining = preg_replace('/\b\w+\*/i', '', $remaining);

        // ── 6. Detect AND/OR boolean mode ────────────────────────────────
        $remaining = trim($remaining);
        if (preg_match('/\bOR\b/i', $remaining)) {
            $result['mode'] = 'boolean';
            $result['has_or'] = true;

            // Split on OR first to separate OR groups
            $orParts = preg_split('/\s+OR\s+/i', $remaining, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($orParts as $idx => $part) {
                $part = trim($part);
                if ($part === '') continue;

                // Within each OR part, further split on AND
                $andParts = preg_split('/\s+AND\s+/i', $part, -1, PREG_SPLIT_NO_EMPTY);
                if (count($andParts) > 1) {
                    // All words in this AND group go to must
                    foreach ($andParts as $ap) {
                        $ap = trim($ap);
                        if ($ap !== '') $result['must'][] = strtolower($ap);
                    }
                } else {
                    // Single OR term
                    $words = preg_split('/\s+/', $part, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($words as $w) {
                        $result['should'][] = strtolower($w);
                    }
                }
            }
        } else {
            // All remaining plain words are must-match (AND mode)
            $remaining = preg_replace('/\bAND\b/i', '', $remaining);
            $remaining = trim($remaining);
            if ($remaining !== '') {
                $result['plain'] = $remaining;
                $words = preg_split('/\s+/', $remaining, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($words as $w) {
                    $result['must'][] = strtolower($w);
                }
            }
        }

        // ── 7. Set mode flags (priority: advanced > phrase > wildcard > boolean) ──
        if (!empty($result['fields']) || !empty($result['filters'])) {
            $result['mode'] = 'advanced';
        } elseif (!empty($result['phrases'])) {
            $result['mode'] = empty($result['must']) && empty($result['should']) ? 'phrase' : 'phrase';
        } elseif (!empty($result['wildcards'])) {
            $result['mode'] = 'wildcard';
        }

        return $result;
    }

    /**
     * Build a MySQL FULLTEXT BOOLEAN MODE string from a parsed result.
     * Used as a first-pass DB filter (recall phase).
     */
    public function toFulltextBoolean(array $parsed): string
    {
        $parts = [];

        // Must terms (AND) — prefixed with +
        foreach ($parsed['must'] as $term) {
            $escaped = $this->escapeFt($term);
            if ($escaped !== '' && mb_strlen($escaped) >= 3) {
                $parts[] = '+' . $escaped;
            }
        }

        // Should terms (OR) — no prefix
        foreach ($parsed['should'] as $term) {
            $escaped = $this->escapeFt($term);
            if ($escaped !== '' && mb_strlen($escaped) >= 3) {
                $parts[] = $escaped;
            }
        }

        // Must-not terms — prefixed with -
        foreach ($parsed['must_not'] as $term) {
            $escaped = $this->escapeFt($term);
            if ($escaped !== '') {
                $parts[] = '-' . $escaped;
            }
        }

        // Exact phrases — wrapped in double quotes
        foreach ($parsed['phrases'] as $phrase) {
            $parts[] = '"' . $phrase . '"';
        }

        // Wildcards — suffixed with *
        foreach ($parsed['wildcards'] as $wc) {
            $escaped = $this->escapeFt($wc);
            if ($escaped !== '' && mb_strlen($escaped) >= 3) {
                $parts[] = $escaped . '*';
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Get all positive terms (must + should + phrases flattened) for scoring.
     */
    public function getPositiveTerms(array $parsed): array
    {
        $terms = array_merge($parsed['must'], $parsed['should']);
        foreach ($parsed['phrases'] as $phrase) {
            $words = preg_split('/\s+/', $phrase, -1, PREG_SPLIT_NO_EMPTY);
            $terms = array_merge($terms, array_map('strtolower', $words));
            $terms[] = strtolower($phrase); // full phrase as a term too
        }
        foreach ($parsed['wildcards'] as $wc) {
            $terms[] = $wc;
        }
        return array_values(array_unique($terms));
    }

    private function escapeFt(string $term): string
    {
        return preg_replace('/[+\-><()~*"@]/', '', $term);
    }
}
