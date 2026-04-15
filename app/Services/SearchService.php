<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

/**
 * SearchService
 *
 * Provides server-side search intelligence:
 *  - Typo/spell correction via Levenshtein distance against an in-index term corpus
 *  - Query interpretation and intent labeling
 *  - Keyword highlighting in text snippets
 *  - Related search suggestions derived from the live index
 */
class SearchService
{
    /**
     * Maximum edit distance considered a "correction" (not a completely different word).
     */
    private const MAX_LEVENSHTEIN = 2;

    /**
     * Minimum word length to attempt spell correction on.
     */
    private const MIN_WORD_LENGTH = 4;

    /**
     * Number of related suggestions to return.
     */
    private const SUGGESTION_COUNT = 6;

    // ──────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Analyse a raw query string and return a structured interpretation bag:
     *
     * [
     *   'original'    => string,           // raw input from user
     *   'corrected'   => string|null,      // corrected query (null = no change needed)
     *   'tokens'      => string[],         // normalised tokens used for search
     *   'intent'      => string,           // human-readable intent label
     *   'is_exact'    => bool,             // phrase in quotes?
     *   'filters'     => array,            // detected in-query filters (category:x, site:x, …)
     * ]
     */
    public function interpret(string $rawQuery): array
    {
        $original  = $rawQuery;
        $isExact   = str_starts_with($rawQuery, '"') && str_ends_with($rawQuery, '"');
        $cleaned   = $isExact ? trim($rawQuery, '"') : $rawQuery;

        // Pull inline filters like category:marketplace or site:.onion
        [$filters, $cleaned] = $this->extractInlineFilters($cleaned);

        $tokens    = $this->tokenize($cleaned);
        $corrected = $isExact ? null : $this->correctTokens($tokens);

        return [
            'original'  => $original,
            'corrected' => $corrected,          // null if identical to original
            'tokens'    => $tokens,
            'intent'    => $this->detectIntent($tokens, $filters),
            'is_exact'  => $isExact,
            'filters'   => $filters,
        ];
    }

    /**
     * Highlight search tokens within a plain-text snippet using <mark> tags.
     *
     * Safe for use inside Blade {!! !!} — input $text is HTML-escaped first.
     */
    public function highlight(?string $text, array $tokens, int $maxLength = 220): string
    {
        if (empty($text)) {
            return '';
        }

        if (empty($tokens)) {
            return e(Str::limit($text, $maxLength));
        }

        // Trim text to the most relevant window around the first hit
        $text = $this->extractRelevantWindow($text, $tokens, $maxLength);

        // HTML-escape before injecting our own markup
        $escaped = e($text);

        foreach ($tokens as $token) {
            if (mb_strlen($token) < 2) {
                continue;
            }
            // Case-insensitive word boundary match
            $pattern  = '/(' . preg_quote($token, '/') . ')/iu';
            $escaped  = preg_replace($pattern, '<mark class="kw-hl">$1</mark>', $escaped);
        }

        return $escaped;
    }

    /**
     * Extract multiple snippets containing the search tokens.
     * Mimics Google's behavioral pattern of showing 1-2 relevant sentences.
     */
    public function getSnippets(?string $text, array $tokens, int $snippetLength = 100, int $maxSnippets = 2): string
    {
        if (empty($text) || empty($tokens)) {
            return '';
        }

        $lowerText = mb_strtolower($text);
        $foundSnippets = [];
        $lastPos = 0;

        foreach ($tokens as $token) {
            if (mb_strlen($token) < 3) continue;

            $pos = mb_strpos($lowerText, mb_strtolower($token), $lastPos);
            if ($pos !== false) {
                $start = max(0, $pos - ($snippetLength / 2));
                // Try to start at a space or beginning
                if ($start > 0) {
                    $spacePos = mb_strpos($text, ' ', $start);
                    if ($spacePos !== false && $spacePos < $pos) {
                        $start = $spacePos + 1;
                    }
                }

                $snip = mb_substr($text, $start, $snippetLength);
                $snip = ($start > 0 ? '...' : '') . trim($snip) . '...';
                
                $foundSnippets[] = $this->highlight($snip, $tokens, $snippetLength + 10);
                
                $lastPos = $pos + mb_strlen($token);
                if (count($foundSnippets) >= $maxSnippets) break;
            }
        }

        return implode(' ', $foundSnippets);
    }

    /**
     * Returns the specific words from the query tokens that were found in the provided text.
     */
    public function getTriggeredWords(?string $text, array $tokens): array
    {
        if (empty($text) || empty($tokens)) return [];
        $lowerText = mb_strtolower($text);
        $found = [];
        foreach ($tokens as $token) {
            if (mb_strpos($lowerText, mb_strtolower($token)) !== false) {
                $found[] = $tok = mb_strtolower($token);
            }
        }
        return array_unique($found);
    }

    /**
     * Generate related search suggestions for a given query.
     *
     * Strategy (purely server-side / DB-driven):
     *  1. Take query tokens and find co-occurring titles in the result set.
     *  2. Extract frequent individual terms from those titles.
     *  3. Build alternative search phrases by pairing the primary term with co-terms.
     *
     * @param  string     $query  The (possibly corrected) query string
     * @param  Collection $links  The current result set (paginated items)
     * @return string[]           Array of suggestion strings
     */
    public function relatedSuggestions(string $query, Collection $links): array
    {
        $tokens    = $this->tokenize($query);
        $primary   = $tokens[0] ?? $query;

        // Collect all title words from top results
        $termFreq = [];
        foreach ($links->take(30) as $link) {
            foreach ($this->tokenize($link->title . ' ' . ($link->description ?? '')) as $word) {
                if (mb_strlen($word) < 4) {
                    continue;
                }
                if (in_array(mb_strtolower($word), array_map('mb_strtolower', $tokens), true)) {
                    continue; // skip words already in query
                }
                $termFreq[$word] = ($termFreq[$word] ?? 0) + 1;
            }
        }

        arsort($termFreq);
        $topTerms = array_slice(array_keys($termFreq), 0, self::SUGGESTION_COUNT * 2);

        $suggestions = [];
        foreach ($topTerms as $term) {
            $phrase = trim($primary . ' ' . $term);
            if ($phrase !== $query && !in_array($phrase, $suggestions, true)) {
                $suggestions[] = $phrase;
                if (count($suggestions) >= self::SUGGESTION_COUNT) {
                    break;
                }
            }
        }

        // Pad with category-based suggestions if not enough
        if (count($suggestions) < 3) {
            $categories = \App\Enum\Category::cases();
            foreach ($categories as $cat) {
                $phrase = $primary . ' ' . strtolower($cat->label());
                if (!in_array($phrase, $suggestions, true)) {
                    $suggestions[] = $phrase;
                    if (count($suggestions) >= self::SUGGESTION_COUNT) {
                        break;
                    }
                }
            }
        }

        return array_slice($suggestions, 0, self::SUGGESTION_COUNT);
    }

    // ──────────────────────────────────────────────────────────────────────
    // SPELL CORRECTION
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Attempt to correct each token against the live index term corpus.
     *
     * Returns the corrected query string, or null if nothing changed.
     */
    private function correctTokens(array $tokens): ?string
    {
        if (empty($tokens)) {
            return null;
        }

        $corpus   = $this->buildCorpus();
        $changed  = false;
        $result   = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) < self::MIN_WORD_LENGTH) {
                $result[] = $token;
                continue;
            }

            $best = $this->closestMatch($token, $corpus);
            if ($best !== null && mb_strtolower($best) !== mb_strtolower($token)) {
                $result[] = $best;
                $changed  = true;
            } else {
                $result[] = $token;
            }
        }

        return $changed ? implode(' ', $result) : null;
    }

    /**
     * Find the closest corpus term for a given input token using Levenshtein distance.
     */
    private function closestMatch(string $token, array $corpus): ?string
    {
        $lower     = mb_strtolower($token);
        $best      = null;
        $bestDist  = PHP_INT_MAX;

        // Exact match — no correction needed
        if (in_array($lower, $corpus, true)) {
            return null;
        }

        foreach ($corpus as $term) {
            // Only compare terms of similar length to avoid nonsensical matches
            if (abs(mb_strlen($term) - mb_strlen($lower)) > self::MAX_LEVENSHTEIN) {
                continue;
            }
            $dist = levenshtein($lower, $term);
            if ($dist > 0 && $dist <= self::MAX_LEVENSHTEIN && $dist < $bestDist) {
                $bestDist = $dist;
                $best     = $term;
            }
        }

        return $best;
    }

    /**
     * Build a pruned set of distinct lowercase terms from the links index.
     *
     * Cached in memory for the duration of the request.
     */
    private static ?array $corpusCache = null;

    private function buildCorpus(): array
    {
        if (self::$corpusCache !== null) {
            return self::$corpusCache;
        }

        // Pull a sample of titles and descriptions to derive the vocabulary
        $rows = Link::active()
            ->selectRaw('title, LEFT(description, 200) as description')
            ->inRandomOrder()
            ->limit(500)
            ->get();

        $terms = [];
        foreach ($rows as $row) {
            foreach ($this->tokenize($row->title . ' ' . ($row->description ?? '')) as $t) {
                if (mb_strlen($t) >= self::MIN_WORD_LENGTH) {
                    $terms[] = mb_strtolower($t);
                }
            }
        }

        self::$corpusCache = array_values(array_unique($terms));
        return self::$corpusCache;
    }

    // ──────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Tokenize a query string into meaningful lowercase words, stripping stop-words.
     */
    private function tokenize(string $text): array
    {
        // Lowercase and strip punctuation (keep hyphens inside words)
        $text   = mb_strtolower(preg_replace('/[^\p{L}\p{N}\s\-]/u', ' ', $text));
        $words  = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $stop   = $this->stopWords();

        return array_values(
            array_filter($words, fn($w) => mb_strlen($w) >= 2 && !in_array($w, $stop, true))
        );
    }

    /**
     * Extract inline search filters from a query string.
     * e.g. "bitcoin category:marketplace" → filters: ['category' => 'marketplace'], cleaned: "bitcoin"
     *
     * @return array [filters, cleanedQuery]
     */
    private function extractInlineFilters(string $query): array
    {
        $filters = [];
        $pattern = '/(\b(?:category|site|type|status)\b):(\S+)/i';

        $cleaned = preg_replace_callback($pattern, function (array $m) use (&$filters): string {
            $filters[strtolower($m[1])] = $m[2];
            return '';
        }, $query);

        return [$filters, trim($cleaned ?? $query)];
    }

    /**
     * Produce a human-readable intent label based on tokens and filters.
     */
    private function detectIntent(array $tokens, array $filters): string
    {
        if (!empty($filters['category'])) {
            return 'Filtered by category · ' . $filters['category'];
        }

        $onionKeywords = ['onion', 'tor', 'hidden', 'darknet', 'dark web'];
        foreach ($tokens as $tok) {
            if (in_array($tok, $onionKeywords, true)) {
                return 'Hidden service lookup';
            }
        }

        if (count($tokens) === 1) {
            return 'Single-term node search';
        }

        if (count($tokens) >= 4) {
            return 'Phrase-contextual search';
        }

        return 'Multi-term node search';
    }

    /**
     * Extract the most relevant window of text around the first token hit.
     */
    private function extractRelevantWindow(string $text, array $tokens, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $lower = mb_strtolower($text);
        $pos   = null;

        foreach ($tokens as $token) {
            $p = mb_strpos($lower, mb_strtolower($token));
            if ($p !== false) {
                $pos = $p;
                break;
            }
        }

        if ($pos === null) {
            return mb_substr($text, 0, $maxLength) . '…';
        }

        $start = max(0, $pos - 40);
        $snip  = mb_substr($text, $start, $maxLength);

        return ($start > 0 ? '…' : '') . $snip . '…';
    }

    /**
     * Common English/Darknet stop-words to exclude from tokenization.
     */
    private function stopWords(): array
    {
        return [
            'a','an','the','and','or','but','in','on','at','to','for',
            'of','with','by','from','is','are','was','be','as','it',
            'its','this','that','i','you','we','they','he','she',
            'not','no','if','so','do','did','has','have','had',
            'can','will','would','could','should','may','might',
            'than','then','when','where','who','which','what','how',
            'all','any','each','few','more','most','other','some','such',
            'via','see','find','search','browse','link','links','site',
            'url','web','page','onion','http','https',
        ];
    }
}
