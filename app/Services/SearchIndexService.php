<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * SearchIndexService
 *
 * Handles bulk (re)indexing of all links into the inverted search index.
 * Designed to be called from an Artisan command.
 */
class SearchIndexService
{
    public function __construct(private SearchEngineService $engine) {}

    /**
     * Rebuild the entire inverted index from scratch.
     *
     * @param  callable|null $progress  Callback($current, $total) for progress reporting
     */
    public function rebuildAll(callable $progress = null): int
    {
        // Clear existing index
        DB::table('search_index')->truncate();
        DB::table('search_idf')->truncate();
        Cache::forget('search_idf_map');

        $links = Link::active()->with('crawlContent')->cursor();
        $total = Link::active()->count();
        $done  = 0;

        foreach ($links as $link) {
            try {
                $this->engine->indexLink($link);
            } catch (\Throwable $e) {
                // Skip problematic links but continue
                logger()->warning("SearchIndex: failed to index link #{$link->id}: " . $e->getMessage());
            }
            $done++;
            if ($progress) {
                $progress($done, $total);
            }
        }

        // Refresh IDF cache after bulk index
        Cache::forget('search_idf_map');

        return $done;
    }

    /**
     * Index only links that are missing from the search_index.
     */
    public function indexMissing(callable $progress = null): int
    {
        $indexed = DB::table('search_index')
            ->selectRaw('DISTINCT link_id')
            ->pluck('link_id')
            ->toArray();

        $links = Link::active()
            ->with('crawlContent')
            ->whereNotIn('id', $indexed)
            ->cursor();

        $total = Link::active()->whereNotIn('id', $indexed)->count();
        $done  = 0;

        foreach ($links as $link) {
            try {
                $this->engine->indexLink($link);
            } catch (\Throwable $e) {
                logger()->warning("SearchIndex: failed to index link #{$link->id}: " . $e->getMessage());
            }
            $done++;
            if ($progress) {
                $progress($done, $total);
            }
        }

        Cache::forget('search_idf_map');

        return $done;
    }
}
