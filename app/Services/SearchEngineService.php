<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * SearchEngineService
 *
 * A full-featured backend search engine implementing:
 *  - Tokenization with Unicode support
 *  - Stop-word removal (English + domain-specific)
 *  - Porter-inspired stemming (simplified English)
 *  - Inverted index lookups via `search_index` table
 *  - TF-IDF relevance scoring
 *  - Weighted field scoring: title > tags > description > body
 *  - Exact phrase, full-word, and partial-match tiers
 *  - False-positive prevention via word-boundary enforcement
 *  - Keyword highlighting with context windows
 *  - Related search suggestion generation
 */
class SearchEngineService
{
    // ── Field weight constants ────────────────────────────────────────────
    private const WEIGHT_TITLE       = 10.0;
    private const WEIGHT_TAG         = 5.0;
    private const WEIGHT_DESCRIPTION = 2.0;
    private const WEIGHT_BODY        = 1.0;

    // ── Match-tier bonus multipliers ─────────────────────────────────────
    private const BONUS_EXACT_PHRASE = 3.0;   // entire query matches a field
    private const BONUS_FULL_WORD    = 1.5;   // token matches as a whole word
    private const BONUS_PARTIAL      = 0.3;   // token appears as a substring

    // ── Corpus / IDF cache TTL ────────────────────────────────────────────
    private const IDF_CACHE_TTL = 3600; // 1 hour

    // ── Minimum token length ──────────────────────────────────────────────
    private const MIN_TOKEN_LEN = 2;

    // ─────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Perform a full intelligent search and return a scored, sorted collection.
     *
     * Returns an array of Link model objects with an additional `search_score`
     * attribute attached, ready for pagination.
     *
     * @param  string  $rawQuery   Raw user input
     * @param  array   $options    ['category' => string, 'uptime' => string]
     * @param  int     $perPage
     * @return array{
     *     links: \Illuminate\Pagination\LengthAwarePaginator,
     *     tokens: string[],
     *     stems:  string[],
     *     interpretation: array,
     *     search_time_ms: float,
     * }
     */
    public function search(string $rawQuery, array $options = [], int $perPage = 15): array
    {
        $startTime = microtime(true);

        $interpretation = $this->interpret($rawQuery);
        $tokens         = $interpretation['tokens'];
        $stems          = $interpretation['stems'];
        $effectiveQuery = $interpretation['effective_query'];

        if (empty($tokens)) {
            return $this->emptyResult($interpretation, $startTime);
        }

        // ── Step 1: Candidate retrieval from inverted index ───────────────
        $candidateIds = $this->retrieveCandidates($stems, $tokens);

        if ($candidateIds->isEmpty()) {
            return $this->emptyResult($interpretation, $startTime);
        }

        // ── Step 2: Load candidates with related data ─────────────────────
        $baseQuery = Link::active()
            ->whereIn('links.id', $candidateIds->keys())
            ->with(['latestCrawlLog', 'crawlContent']);

        // Apply filters
        if (!empty($options['category']) && $options['category'] !== 'all') {
            $baseQuery->where('links.category', $options['category']);
        }
        if (!empty($options['uptime']) && $options['uptime'] !== 'all') {
            $baseQuery->where('links.uptime_status', $options['uptime']);
        }

        $candidates = $baseQuery->get();

        // ── Step 3: Score each candidate ──────────────────────────────────
        $scored = $candidates->map(function (Link $link) use ($tokens, $stems, $effectiveQuery, $candidateIds) {
            $score = $this->scoreDocument($link, $tokens, $stems, $effectiveQuery, $candidateIds);
            $link->search_score = $score;
            return $link;
        });

        // ── Step 4: Filter out zero-score results (false positives) ────────
        $scored = $scored->filter(fn($link) => $link->search_score > 0);

        // ── Step 5: Sort by score descending ──────────────────────────────
        $sorted = $scored->sortByDesc('search_score')->values();

        // ── Step 6: Paginate in PHP (after scoring) ───────────────────────
        $page    = request()->get('page', 1);
        $total   = $sorted->count();
        $items   = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // ── Step 7: Enrich results with highlights and snippets ────────────
        $paginator->getCollection()->each(function (Link $link) use ($tokens) {
            $link->highlighted_title       = $this->highlight($link->title, $tokens, 120, true);
            $link->highlighted_description = $this->highlight($link->description, $tokens, 240);
            $link->snippet_content         = $this->buildSnippet(
                $link->crawlContent?->body_text,
                $tokens
            );
        });

        $searchTimeMs = round((microtime(true) - $startTime) * 1000, 1);

        return [
            'links'          => $paginator,
            'tokens'         => $tokens,
            'stems'          => $stems,
            'interpretation' => $interpretation,
            'search_time_ms' => $searchTimeMs,
        ];
    }

    /**
     * Interpret a raw query into tokens, stems, and metadata.
     */
    public function interpret(string $rawQuery): array
    {
        $original = trim($rawQuery);
        $isExact  = str_starts_with($original, '"') && str_ends_with($original, '"');
        $cleaned  = $isExact ? trim($original, '"') : $original;

        [$filters, $cleaned] = $this->extractInlineFilters($cleaned);

        $tokens  = $this->tokenize($cleaned);
        $stems   = array_map(fn($t) => $this->stem($t), $tokens);

        // Spell correction (optional, based on index corpus)
        $corrected = $isExact ? null : $this->correctTokens($tokens);

        $effectiveQuery = $corrected ?? $cleaned;

        return [
            'original'       => $original,
            'effective_query'=> $effectiveQuery,
            'corrected'      => ($corrected && strtolower($corrected) !== strtolower($cleaned)) ? $corrected : null,
            'tokens'         => $tokens,
            'stems'          => $stems,
            'is_exact'       => $isExact,
            'filters'        => $filters,
            'intent'         => $this->detectIntent($tokens, $filters),
        ];
    }

    /**
     * Index (or re-index) a single Link into the inverted index.
     * Call this on link create/update.
     */
    public function indexLink(Link $link): void
    {
        $link->loadMissing('crawlContent');

        $fields = [
            'title'       => $link->title ?? '',
            'tags'        => $link->tags ?? '',
            'description' => $link->description ?? '',
            'body'        => $link->crawlContent?->body_text ?? '',
        ];

        // Tokenize and stem each field
        $termCounts = []; // ['stem' => ['title' => n, 'tag' => n, ...]]

        foreach ($fields as $field => $text) {
            $column = match ($field) {
                'title'       => 'title_count',
                'tags'        => 'tag_count',
                'description' => 'description_count',
                'body'        => 'body_count',
            };

            $tokens = $this->tokenize($text);
            foreach ($tokens as $token) {
                $stem = $this->stem($token);
                if (!isset($termCounts[$stem])) {
                    $termCounts[$stem] = [
                        'title_count'       => 0,
                        'tag_count'         => 0,
                        'description_count' => 0,
                        'body_count'        => 0,
                    ];
                }
                $termCounts[$stem][$column]++;
            }
        }

        // Upsert each term into search_index
        DB::transaction(function () use ($link, $termCounts) {
            // Remove old entries for this link
            DB::table('search_index')->where('link_id', $link->id)->delete();

            $rows = [];
            $now  = now();
            foreach ($termCounts as $term => $counts) {
                $rows[] = array_merge([
                    'link_id'    => $link->id,
                    'term'       => $term,
                    'tf_idf_score' => 0, // computed separately
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $counts);
            }

            if (!empty($rows)) {
                // Chunk inserts to avoid hitting MySQL's max packet size
                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('search_index')->insert($chunk);
                }
            }
        });

        // Mark IDF cache as stale
        Cache::forget('search_idf_map');
    }

    /**
     * Remove a link from the inverted index.
     */
    public function removeFromIndex(int $linkId): void
    {
        DB::table('search_index')->where('link_id', $linkId)->delete();
        Cache::forget('search_idf_map');
    }

    /**
     * Highlight tokens within text using <mark> tags.
     * Enforces word-boundary matching to prevent "ai" matching "main".
     */
    public function highlight(?string $text, array $tokens, int $maxLen = 240, bool $enforceWordBoundary = false): string
    {
        if (empty($text) || empty($tokens)) {
            return e(Str::limit($text ?? '', $maxLen));
        }

        $window  = $this->extractRelevantWindow($text, $tokens, $maxLen);
        $escaped = e($window);

        foreach ($tokens as $token) {
            if (mb_strlen($token) < self::MIN_TOKEN_LEN) {
                continue;
            }
            $q = preg_quote($token, '/');
            // Word-boundary pattern (works for multibyte via \b equivalent)
            $pattern = $enforceWordBoundary
                ? '/(?<!\pL)(' . $q . ')(?!\pL)/iu'
                : '/(' . $q . ')/iu';
            $escaped = preg_replace($pattern, '<mark class="kw-hl">$1</mark>', $escaped);
        }

        return $escaped;
    }

    /**
     * Build a context-aware snippet from body text containing token hits.
     */
    public function buildSnippet(?string $bodyText, array $tokens, int $snippetLen = 130, int $maxSnippets = 2): string
    {
        if (empty($bodyText) || empty($tokens)) {
            return '';
        }

        $lower      = mb_strtolower($bodyText);
        $snippets   = [];
        $usedRanges = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) < 3) {
                continue;
            }
            $pos = mb_strpos($lower, mb_strtolower($token));
            if ($pos === false) {
                continue;
            }

            // Avoid overlapping snippets
            $overlaps = false;
            foreach ($usedRanges as [$s, $e]) {
                if ($pos >= $s && $pos <= $e) {
                    $overlaps = true;
                    break;
                }
            }
            if ($overlaps) {
                continue;
            }

            $start = max(0, $pos - (int)($snippetLen / 2));
            // Snap to word boundary
            if ($start > 0) {
                $spacePos = mb_strpos($bodyText, ' ', $start);
                if ($spacePos !== false && $spacePos < $pos) {
                    $start = $spacePos + 1;
                }
            }

            $snip   = mb_substr($bodyText, $start, $snippetLen);
            $prefix = $start > 0 ? '…' : '';
            $suffix = ($start + $snippetLen) < mb_strlen($bodyText) ? '…' : '';
            $text   = $prefix . trim($snip) . $suffix;

            $snippets[]   = $this->highlight($text, $tokens, $snippetLen + 20);
            $usedRanges[] = [$start, $start + $snippetLen];

            if (count($snippets) >= $maxSnippets) {
                break;
            }
        }

        return implode(' ', $snippets);
    }

    /**
     * Generate related search suggestions from the current result set.
     */
    public function relatedSuggestions(string $query, Collection $links): array
    {
        $tokens  = $this->tokenize($query);
        $primary = $tokens[0] ?? $query;

        $termFreq = [];
        foreach ($links->take(30) as $link) {
            $text = ($link->title ?? '') . ' ' . ($link->description ?? '') . ' ' . ($link->tags ?? '');
            foreach ($this->tokenize($text) as $word) {
                if (mb_strlen($word) < 4) {
                    continue;
                }
                $lower = mb_strtolower($word);
                if (in_array($lower, array_map('mb_strtolower', $tokens), true)) {
                    continue;
                }
                $termFreq[$word] = ($termFreq[$word] ?? 0) + 1;
            }
        }

        arsort($termFreq);
        $topTerms    = array_slice(array_keys($termFreq), 0, 12);
        $suggestions = [];

        foreach ($topTerms as $term) {
            $phrase = trim($primary . ' ' . $term);
            if ($phrase !== $query && !in_array($phrase, $suggestions, true)) {
                $suggestions[] = $phrase;
                if (count($suggestions) >= 6) {
                    break;
                }
            }
        }

        return array_slice($suggestions, 0, 6);
    }

    // ─────────────────────────────────────────────────────────────────────
    // CANDIDATE RETRIEVAL
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Retrieve candidate link IDs from the inverted index.
     *
     * Returns a Collection keyed by link_id with the sum of raw index scores.
     */
    private function retrieveCandidates(array $stems, array $tokens): Collection
    {
        if (empty($stems)) {
            return collect();
        }

        // Fetch index rows matching any stem
        $rows = DB::table('search_index')
            ->whereIn('term', $stems)
            ->select('link_id', 'term', 'title_count', 'tag_count', 'description_count', 'body_count')
            ->get();

        if ($rows->isEmpty()) {
            // Fallback: partial stem matching (prefix search on term)
            $rows = DB::table('search_index')
                ->where(function ($q) use ($stems) {
                    foreach ($stems as $stem) {
                        $q->orWhere('term', 'LIKE', $stem . '%');
                    }
                })
                ->select('link_id', 'term', 'title_count', 'tag_count', 'description_count', 'body_count')
                ->get();
        }

        // Aggregate raw scores per link
        return $rows->groupBy('link_id')->map(function ($termRows) {
            return $termRows->sum(function ($row) {
                return ($row->title_count       * self::WEIGHT_TITLE)
                      + ($row->tag_count         * self::WEIGHT_TAG)
                      + ($row->description_count * self::WEIGHT_DESCRIPTION)
                      + ($row->body_count        * self::WEIGHT_BODY);
            });
        });
    }

    // ─────────────────────────────────────────────────────────────────────
    // SCORING
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Score a single document against the query.
     *
     * Combines:
     *  1. TF-IDF weighted field score from inverted index
     *  2. Match-tier bonuses (exact phrase > full word > partial)
     *  3. False-positive suppression (tokens must be whole words)
     */
    private function scoreDocument(
        Link $link,
        array $tokens,
        array $stems,
        string $effectiveQuery,
        Collection $indexScores
    ): float {
        $baseScore = $indexScores->get($link->id, 0.0);

        // Gather all searchable text per field
        $title       = mb_strtolower($link->title ?? '');
        $tags        = mb_strtolower($link->tags ?? '');
        $description = mb_strtolower($link->description ?? '');
        $body        = mb_strtolower($link->crawlContent?->body_text ?? '');

        $queryLower = mb_strtolower($effectiveQuery);
        $bonus      = 0.0;

        // ── Exact phrase bonus ─────────────────────────────────────────
        if (str_contains($title, $queryLower)) {
            $bonus += self::WEIGHT_TITLE * self::BONUS_EXACT_PHRASE;
        }
        if (!empty($tags) && str_contains($tags, $queryLower)) {
            $bonus += self::WEIGHT_TAG * self::BONUS_EXACT_PHRASE;
        }

        // ── Per-token scoring with word-boundary enforcement ──────────
        $tokenMatchCount = 0;
        foreach ($tokens as $token) {
            $tok = mb_strtolower($token);

            // Full-word match (word boundary) — no false positives
            $isFullWordTitle = $this->isFullWordMatch($title, $tok);
            $isFullWordTag   = $this->isFullWordMatch($tags, $tok);
            $isFullWordDesc  = $this->isFullWordMatch($description, $tok);
            $isFullWordBody  = $this->isFullWordMatch($body, $tok);

            if ($isFullWordTitle) {
                $bonus += self::WEIGHT_TITLE * self::BONUS_FULL_WORD;
                $tokenMatchCount++;
            } elseif (str_contains($title, $tok) && mb_strlen($tok) >= 4) {
                // Partial match only for tokens >= 4 chars to reduce noise
                $bonus += self::WEIGHT_TITLE * self::BONUS_PARTIAL;
                $tokenMatchCount++;
            }

            if ($isFullWordTag) {
                $bonus += self::WEIGHT_TAG * self::BONUS_FULL_WORD;
                $tokenMatchCount++;
            }

            if ($isFullWordDesc) {
                $bonus += self::WEIGHT_DESCRIPTION * self::BONUS_FULL_WORD;
                $tokenMatchCount++;
            } elseif (str_contains($description, $tok) && mb_strlen($tok) >= 4) {
                $bonus += self::WEIGHT_DESCRIPTION * self::BONUS_PARTIAL;
                $tokenMatchCount++;
            }

            if ($isFullWordBody) {
                $bonus += self::WEIGHT_BODY * self::BONUS_FULL_WORD;
                $tokenMatchCount++;
            }
        }

        // ── False-positive suppression ─────────────────────────────────
        // If none of the tokens appear as whole words in any field,
        // AND the base index score is zero → hard zero (discard result)
        if ($tokenMatchCount === 0 && $baseScore === 0.0) {
            return 0.0;
        }

        // IDF boost using cached IDF map
        $idfBoost = $this->computeIdfBoost($stems);

        return round(($baseScore + $bonus) * $idfBoost, 4);
    }

    /**
     * Check if $needle appears as a whole word (not inside another word) in $haystack.
     *
     * This prevents "ai" from matching "main" or "train".
     */
    private function isFullWordMatch(string $haystack, string $needle): bool
    {
        if (empty($haystack) || empty($needle)) {
            return false;
        }

        // Use a Unicode-aware word-boundary pattern
        $pattern = '/(?<!\pL)' . preg_quote($needle, '/') . '(?!\pL)/iu';
        return (bool) preg_match($pattern, $haystack);
    }

    /**
     * Compute IDF boost for a set of stems.
     * Returns a multiplier (>= 1.0) based on term rarity.
     */
    private function computeIdfBoost(array $stems): float
    {
        if (empty($stems)) {
            return 1.0;
        }

        $idfMap    = $this->getIdfMap();
        $totalBoost = 0.0;

        foreach ($stems as $stem) {
            $idf        = $idfMap[$stem] ?? 1.0;
            $totalBoost += $idf;
        }

        // Normalise: average IDF across all stems, minimum 1.0
        return max(1.0, $totalBoost / count($stems));
    }

    /**
     * Get cached IDF values for all terms.
     */
    private function getIdfMap(): array
    {
        return Cache::remember('search_idf_map', self::IDF_CACHE_TTL, function () {
            $totalDocs = Link::active()->count();
            if ($totalDocs === 0) {
                return [];
            }

            // Compute IDF from current search_index data
            $termDocFreqs = DB::table('search_index')
                ->selectRaw('term, COUNT(DISTINCT link_id) as doc_freq')
                ->groupBy('term')
                ->pluck('doc_freq', 'term');

            $idfMap = [];
            foreach ($termDocFreqs as $term => $docFreq) {
                // IDF = log((N + 1) / (df + 1)) + 1  [smoothed]
                $idfMap[$term] = log(($totalDocs + 1) / ($docFreq + 1)) + 1;
            }

            return $idfMap;
        });
    }

    // ─────────────────────────────────────────────────────────────────────
    // TOKENIZATION & STEMMING
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Tokenize text into normalized, stop-word-filtered lowercase tokens.
     */
    public function tokenize(string $text): array
    {
        // Normalize: lowercase, remove URLs, then strip non-word chars (keep hyphens)
        $text  = mb_strtolower($text);
        $text  = preg_replace('/https?:\/\/\S+/u', ' ', $text);
        $text  = preg_replace('/[^\p{L}\p{N}\s\-]/u', ' ', $text);

        $words = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $stop  = $this->stopWords();

        $tokens = [];
        foreach ($words as $word) {
            // Strip leading/trailing hyphens
            $word = trim($word, '-');
            if (mb_strlen($word) < self::MIN_TOKEN_LEN) {
                continue;
            }
            if (in_array($word, $stop, true)) {
                continue;
            }
            $tokens[] = $word;
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Apply a simplified Porter-inspired stemmer.
     *
     * Handles the most common English suffix patterns. Not a full
     * implementation — designed for recall without sacrificing precision.
     */
    public function stem(string $word): string
    {
        $w = mb_strtolower($word);

        // Short words — do not stem
        if (mb_strlen($w) <= 3) {
            return $w;
        }

        // Step 1a: plurals / -es / -ed / -ing
        $w = $this->stemStep1a($w);
        // Step 1b: -ing / -ed tenses
        $w = $this->stemStep1b($w);
        // Step 1c: -y → -i
        $w = $this->stemStep1c($w);
        // Step 2: common derivational suffixes
        $w = $this->stemStep2($w);
        // Step 3: further suffixes
        $w = $this->stemStep3($w);
        // Step 4: remaining common suffixes
        $w = $this->stemStep4($w);

        return $w;
    }

    // ─────────────────────────────────────────────────────────────────────
    // STEMMER STEPS (simplified Porter)
    // ─────────────────────────────────────────────────────────────────────

    private function stemStep1a(string $w): string
    {
        if (str_ends_with($w, 'sses')) return mb_substr($w, 0, -2);
        if (str_ends_with($w, 'ies'))  return mb_substr($w, 0, -2);
        if (str_ends_with($w, 'ss'))   return $w;
        if (str_ends_with($w, 's') && mb_strlen($w) > 4)   return mb_substr($w, 0, -1);
        return $w;
    }

    private function stemStep1b(string $w): string
    {
        if (str_ends_with($w, 'eed')) {
            $stem = mb_substr($w, 0, -3);
            return $this->numVowelGroups($stem) > 0 ? $stem . 'ee' : $w;
        }
        foreach (['ing', 'ed'] as $suffix) {
            if (str_ends_with($w, $suffix)) {
                $stem = mb_substr($w, 0, -mb_strlen($suffix));
                if ($this->containsVowel($stem)) {
                    // Step 1b sub-rules
                    foreach (['at', 'bl', 'iz'] as $s2) {
                        if (str_ends_with($stem, $s2)) return $stem . 'e';
                    }
                    if ($this->endsDoubleConsonant($stem) && !in_array(mb_substr($stem, -1), ['l','s','z'])) {
                        return mb_substr($stem, 0, -1);
                    }
                    if ($this->numVowelGroups($stem) === 1 && $this->endsCVC($stem)) {
                        return $stem . 'e';
                    }
                    return $stem;
                }
            }
        }
        return $w;
    }

    private function stemStep1c(string $w): string
    {
        if (str_ends_with($w, 'y') && $this->containsVowel(mb_substr($w, 0, -1))) {
            return mb_substr($w, 0, -1) . 'i';
        }
        return $w;
    }

    private function stemStep2(string $w): string
    {
        $suffixes = [
            'ational' => 'ate', 'tional' => 'tion', 'enci' => 'ence',
            'anci'    => 'ance', 'izer' => 'ize', 'abli' => 'able',
            'alli'    => 'al',  'entli' => 'ent', 'eli'  => 'e',
            'ousli'   => 'ous', 'ization' => 'ize', 'ation' => 'ate',
            'ator'    => 'ate', 'alism' => 'al', 'iveness' => 'ive',
            'fulness' => 'ful', 'ousness' => 'ous', 'aliti' => 'al',
            'iviti'   => 'ive', 'biliti' => 'ble',
        ];
        foreach ($suffixes as $suffix => $replacement) {
            if (str_ends_with($w, $suffix)) {
                $stem = mb_substr($w, 0, -mb_strlen($suffix));
                if ($this->numVowelGroups($stem) > 0) {
                    return $stem . $replacement;
                }
            }
        }
        return $w;
    }

    private function stemStep3(string $w): string
    {
        $suffixes = [
            'icate' => 'ic', 'ative' => '', 'alize' => 'al',
            'iciti' => 'ic', 'ical'  => 'ic', 'ful'  => '', 'ness' => '',
        ];
        foreach ($suffixes as $suffix => $replacement) {
            if (str_ends_with($w, $suffix)) {
                $stem = mb_substr($w, 0, -mb_strlen($suffix));
                if ($this->numVowelGroups($stem) > 0) {
                    return $stem . $replacement;
                }
            }
        }
        return $w;
    }

    private function stemStep4(string $w): string
    {
        $suffixes = [
            'al','ance','ence','er','ic','able','ible','ant','ement',
            'ment','ent','ion','ou','ism','ate','iti','ous','ive','ize',
        ];
        foreach ($suffixes as $suffix) {
            if (str_ends_with($w, $suffix)) {
                $stem = mb_substr($w, 0, -mb_strlen($suffix));
                $m    = $this->numVowelGroups($stem);
                if ($suffix === 'ion') {
                    if ($m > 1 && in_array(mb_substr($stem, -1), ['s','t'])) {
                        return $stem;
                    }
                } elseif ($m > 1) {
                    return $stem;
                }
            }
        }
        return $w;
    }

    // ─────────────────────────────────────────────────────────────────────
    // STEMMER HELPERS
    // ─────────────────────────────────────────────────────────────────────

    private function containsVowel(string $w): bool
    {
        return (bool) preg_match('/[aeiou]/i', $w);
    }

    private function numVowelGroups(string $w): int
    {
        preg_match_all('/[bcdfghjklmnpqrstvwxyz]*[aeiou]+[bcdfghjklmnpqrstvwxyz]*/i', $w, $m);
        return count($m[0] ?? []);
    }

    private function endsDoubleConsonant(string $w): bool
    {
        $len = mb_strlen($w);
        if ($len < 2) return false;
        $last = mb_substr($w, -1);
        $prev = mb_substr($w, -2, 1);
        return $last === $prev && !in_array($last, ['a','e','i','o','u']);
    }

    private function endsCVC(string $w): bool
    {
        // consonant–vowel–consonant, and final consonant is not w, x, y
        if (mb_strlen($w) < 3) return false;
        $c = mb_substr($w, -1);
        $v = mb_substr($w, -2, 1);
        $b = mb_substr($w, -3, 1);
        $vowels = ['a','e','i','o','u'];
        return !in_array($c, $vowels)
            && in_array($v, $vowels)
            && !in_array($b, $vowels)
            && !in_array($c, ['w','x','y']);
    }

    // ─────────────────────────────────────────────────────────────────────
    // SPELL CORRECTION (Levenshtein against index corpus)
    // ─────────────────────────────────────────────────────────────────────

    private function correctTokens(array $tokens): ?string
    {
        if (empty($tokens)) {
            return null;
        }

        $corpus  = $this->buildCorpus();
        $changed = false;
        $result  = [];

        foreach ($tokens as $token) {
            if (mb_strlen($token) < 4) {
                $result[] = $token;
                continue;
            }

            $best = $this->closestCorpusMatch($token, $corpus);
            if ($best !== null && mb_strtolower($best) !== mb_strtolower($token)) {
                $result[] = $best;
                $changed  = true;
            } else {
                $result[] = $token;
            }
        }

        return $changed ? implode(' ', $result) : null;
    }

    private function closestCorpusMatch(string $token, array $corpus): ?string
    {
        $lower    = mb_strtolower($token);
        $best     = null;
        $bestDist = PHP_INT_MAX;

        if (in_array($lower, $corpus, true)) {
            return null; // already correct
        }

        foreach ($corpus as $term) {
            if (abs(mb_strlen($term) - mb_strlen($lower)) > 2) {
                continue;
            }
            $dist = levenshtein($lower, $term);
            if ($dist > 0 && $dist <= 2 && $dist < $bestDist) {
                $bestDist = $dist;
                $best     = $term;
            }
        }

        return $best;
    }

    private static ?array $corpusCache = null;

    private function buildCorpus(): array
    {
        if (self::$corpusCache !== null) {
            return self::$corpusCache;
        }

        $terms = DB::table('search_index')
            ->selectRaw('DISTINCT term')
            ->pluck('term')
            ->toArray();

        self::$corpusCache = $terms;
        return $terms;
    }

    // ─────────────────────────────────────────────────────────────────────
    // QUERY INTELLIGENCE
    // ─────────────────────────────────────────────────────────────────────

    private function extractInlineFilters(string $query): array
    {
        $filters = [];
        $pattern = '/\b(category|site|type|status)\b:(\S+)/i';

        $cleaned = preg_replace_callback($pattern, function (array $m) use (&$filters): string {
            $filters[strtolower($m[1])] = $m[2];
            return '';
        }, $query);

        return [$filters, trim($cleaned ?? $query)];
    }

    private function detectIntent(array $tokens, array $filters): string
    {
        if (!empty($filters['category'])) {
            return 'Filtered by category · ' . $filters['category'];
        }

        $onionKeywords = ['onion','tor','hidden','darknet','darkweb'];
        foreach ($tokens as $tok) {
            if (in_array($tok, $onionKeywords, true)) {
                return 'Hidden service lookup';
            }
        }

        return match (true) {
            count($tokens) === 1 => 'Single-term node search',
            count($tokens) >= 4  => 'Phrase-contextual search',
            default              => 'Multi-term node search',
        };
    }

    // ─────────────────────────────────────────────────────────────────────
    // TEXT HELPERS
    // ─────────────────────────────────────────────────────────────────────

    private function extractRelevantWindow(string $text, array $tokens, int $maxLen): string
    {
        if (mb_strlen($text) <= $maxLen) {
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
            return mb_substr($text, 0, $maxLen) . '…';
        }

        $start = max(0, $pos - 40);
        $snip  = mb_substr($text, $start, $maxLen);
        return ($start > 0 ? '…' : '') . $snip . '…';
    }

    private function emptyResult(array $interpretation, float $startTime): array
    {
        return [
            'links'          => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1, ['path' => request()->url()]),
            'tokens'         => $interpretation['tokens'] ?? [],
            'stems'          => $interpretation['stems'] ?? [],
            'interpretation' => $interpretation,
            'search_time_ms' => round((microtime(true) - $startTime) * 1000, 1),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // STOP WORDS
    // ─────────────────────────────────────────────────────────────────────

    private function stopWords(): array
    {
        return [
            // Articles / prepositions / conjunctions
            'a','an','the','and','or','but','in','on','at','to','for','of',
            'with','by','from','is','are','was','be','as','it','its','this',
            'that','i','you','we','they','he','she','not','no','if','so',
            'do','did','has','have','had','can','will','would','could',
            'should','may','might','than','then','when','where','who',
            'which','what','how','all','any','each','few','more','most',
            'other','some','such','up','out','about','into','over',
            // Domain-specific noise
            'via','see','find','search','browse','link','links','site',
            'url','web','page','onion','http','https','www','com','org',
            'net','io','co','me','info','biz',
        ];
    }
}
