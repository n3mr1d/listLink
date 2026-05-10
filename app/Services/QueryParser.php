<?php

namespace App\Services;

/**
 * QueryParser — Advanced query language parser.
 *
 * Supports:
 *  - Boolean operators: AND, OR, NOT, -word
 *  - Exact phrases: "multi word"
 *  - Field scoping: title:vpn url:onion category:forum status:online
 *  - Wildcard: crypt* tor*
 *  - Date ranges: after:2024-01-01 before:2025-01-01
 *  - Numeric filters: likes:>5
 */
class QueryParser
{
    public function parse(string $raw): array
    {
        $result = [
            'original'  => $raw,
            'must'      => [],   // AND terms
            'should'    => [],   // OR terms
            'must_not'  => [],   // NOT/-word terms
            'phrases'   => [],   // "exact phrases"
            'wildcards' => [],   // crypt*
            'fields'    => [],   // title:x, url:x, category:x, status:x
            'filters'   => [],   // after:, before:, likes:
            'plain'     => '',   // remaining plain text
            'mode'      => 'standard', // standard|boolean|phrase|wildcard
        ];

        $tokens = [];
        $remaining = $raw;

        // Extract quoted phrases
        preg_match_all('/"([^"]+)"/', $remaining, $phraseMatches);
        foreach ($phraseMatches[1] as $phrase) {
            $result['phrases'][] = $phrase;
        }
        $remaining = preg_replace('/"[^"]+"/', '', $remaining);

        // Extract NOT / -word operators
        preg_match_all('/(?:NOT\s+|-)\s*(\S+)/i', $remaining, $notMatches);
        foreach ($notMatches[1] as $notWord) {
            $result['must_not'][] = strtolower($notWord);
        }
        $remaining = preg_replace('/(?:NOT\s+|-)\s*\S+/i', '', $remaining);

        // Extract field-scoped terms
        $fieldPattern = '/\b(title|url|category|status|site|type):([\S]+)/i';
        preg_match_all($fieldPattern, $remaining, $fieldMatches, PREG_SET_ORDER);
        foreach ($fieldMatches as $m) {
            $result['fields'][strtolower($m[1])][] = $m[2];
        }
        $remaining = preg_replace($fieldPattern, '', $remaining);

        // Extract numeric/date filters
        $filterPattern = '/\b(after|before|likes|rating):([\S]+)/i';
        preg_match_all($filterPattern, $remaining, $filterMatches, PREG_SET_ORDER);
        foreach ($filterMatches as $m) {
            $key = strtolower($m[1]);
            $val = $m[2];
            if (preg_match('/^([><=]+)(\d+)$/', $val, $opMatch)) {
                $result['filters'][$key] = ['op' => $opMatch[1], 'val' => (int)$opMatch[2]];
            } else {
                $result['filters'][$key] = $val;
            }
        }
        $remaining = preg_replace($filterPattern, '', $remaining);

        // Extract wildcard terms
        preg_match_all('/\b(\w+)\*/i', $remaining, $wildcardMatches);
        foreach ($wildcardMatches[1] as $wc) {
            $result['wildcards'][] = strtolower($wc);
        }
        $remaining = preg_replace('/\b\w+\*/i', '', $remaining);

        // Detect AND/OR boolean mode
        if (preg_match('/\bAND\b|\bOR\b/i', $remaining)) {
            $result['mode'] = 'boolean';
            $parts = preg_split('/\s+(AND|OR)\s+/i', $remaining, -1, PREG_SPLIT_DELIM_CAPTURE);
            $op = 'must';
            foreach ($parts as $part) {
                $part = trim($part);
                if (strtoupper($part) === 'AND') { $op = 'must'; continue; }
                if (strtoupper($part) === 'OR')  { $op = 'should'; continue; }
                if ($part !== '') {
                    $result[$op][] = strtolower($part);
                }
            }
        } else {
            $remaining = trim($remaining);
            if ($remaining !== '') {
                $result['plain'] = $remaining;
                $words = preg_split('/\s+/', $remaining, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($words as $w) {
                    $result['must'][] = strtolower($w);
                }
            }
        }

        // Set mode flags
        if (!empty($result['phrases'])) $result['mode'] = 'phrase';
        if (!empty($result['wildcards'])) $result['mode'] = 'wildcard';
        if (!empty($result['fields']) || !empty($result['filters'])) $result['mode'] = 'advanced';

        return $result;
    }

    /**
     * Build a MySQL FULLTEXT BOOLEAN MODE query string from parsed result.
     */
    public function toFulltextBoolean(array $parsed): string
    {
        $parts = [];

        foreach ($parsed['must'] as $term) {
            if (mb_strlen($term) >= 3) {
                $parts[] = '+' . $this->escapeFt($term);
            }
        }
        foreach ($parsed['must_not'] as $term) {
            $parts[] = '-' . $this->escapeFt($term);
        }
        foreach ($parsed['should'] as $term) {
            $parts[] = $this->escapeFt($term);
        }
        foreach ($parsed['phrases'] as $phrase) {
            $parts[] = '"' . $phrase . '"';
        }
        foreach ($parsed['wildcards'] as $wc) {
            $parts[] = $wc . '*';
        }

        return implode(' ', $parts);
    }

    private function escapeFt(string $term): string
    {
        return preg_replace('/[+\-><()\~*"@]/', '', $term);
    }
}
