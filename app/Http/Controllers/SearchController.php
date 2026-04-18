<?php

namespace App\Http\Controllers;

use App\Enum\AdPlacement;
use App\Enum\Category;
use App\Enum\UptimeStatus;
use App\Models\Advertisement;
use App\Models\CrawlContent;
use App\Models\Link;
use App\Models\User;
use App\Services\SearchEngineService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\AdTrackingController;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $this->trackVisitor($request);

        $headerAds = $this->randomAds(
            Advertisement::active()->byPlacement(AdPlacement::HEADER)
        );

        $sponsoredLinks = $this->orderedSponsoredAds(
            Advertisement::active()->where('ad_type', 'sponsored')
        );

        // Track impressions for sponsors shown on every search
        AdTrackingController::trackImpressions($sponsoredLinks);

        $query          = trim($request->get('q', ''));
        $categoryFilter = $request->get('category', 'all');
        $uptimeFilter   = $request->get('uptime', 'all');
        $sortBy         = $request->get('sort', 'relevance');

        $links              = null;
        $searchTime         = null;
        $categoryBreakdown  = [];
        $interpretation     = null;
        $correctedQuery     = null;
        $searchTokens       = [];
        $relatedSuggestions = [];
        $sidebarAds         = collect();

        if (strlen($query) >= 2) {
            // ── Intelligent search engine ──────────────────────────────────
            $engine = new SearchEngineService();

            $result = $engine->search($query, [
                'category' => $categoryFilter,
                'uptime'   => $uptimeFilter,
            ], 15);

            $links         = $result['links'];
            $searchTokens  = $result['tokens'];
            $searchTime    = $result['search_time_ms'];
            $interpretation = $result['interpretation'];
            $correctedQuery = $interpretation['corrected'];

            // Sort override (user explicitly chose something other than relevance)
            // Note: for non-relevance sorts we fall back to a DB query so the
            // paginator results are replaced entirely.
            if ($sortBy !== 'relevance') {
                $links = $this->applyAlternativeSort(
                    $sortBy,
                    $interpretation['effective_query'],
                    $categoryFilter,
                    $uptimeFilter,
                    $engine
                );
            }

            // Related searches
            $relatedSuggestions = $engine->relatedSuggestions(
                $interpretation['effective_query'],
                $links->getCollection()
            );

            // Category breakdown (for sidebar filter chips)
            $categoryBreakdown = $this->buildCategoryBreakdown(
                $interpretation['effective_query'],
                $categoryFilter,
                $engine
            );

            // Sidebar ads
            $sidebarAds = $this->randomAds(
                Advertisement::active()->byPlacement(AdPlacement::SIDEBAR)
            );
        }

        $stats = [
            'total_links'   => Link::active()->count(),
            'online_links'  => Link::active()->where('uptime_status', UptimeStatus::ONLINE)->count(),
            'categories'    => count(Category::cases()),
            'indexed_count' => CrawlContent::count(),
            'total_users'   => User::count(),
            'live_viewers'  => \App\Models\Visitor::where('last_active_at', '>=', now()->subMinutes(5))->count(),
            'total_views'   => \App\Models\Visitor::count(),
        ];

        $recentlyAddedLinks = Link::active()
            ->online()
            ->with(['user'])
            ->latest()
            ->take(4)
            ->get();

        $recentlyRegisteredUser = User::latest()->first();
        $categories = Category::cases();

        return view('search', compact(
            'links',
            'query',
            'categoryFilter',
            'uptimeFilter',
            'sortBy',
            'categories',
            'searchTime',
            'stats',
            'recentlyAddedLinks',
            'recentlyRegisteredUser',
            'headerAds',
            'sponsoredLinks',
            'sidebarAds',
            'categoryBreakdown',
            'interpretation',
            'correctedQuery',
            'searchTokens',
            'relatedSuggestions',
        ));
    }

    // ─────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Apply a non-relevance sort over the same candidate set.
     * We re-run the candidate retrieval but sort using the DB field.
     */
    private function applyAlternativeSort(
        string $sortBy,
        string $effectiveQuery,
        string $categoryFilter,
        string $uptimeFilter,
        SearchEngineService $engine
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $interpretation = $engine->interpret($effectiveQuery);
        $stems  = $interpretation['stems'];
        $tokens = $interpretation['tokens'];

        if (empty($stems)) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1, ['path' => request()->url()]);
        }

        // Get candidate IDs from index
        $candidateIds = \Illuminate\Support\Facades\DB::table('search_index')
            ->whereIn('term', $stems)
            ->orWhere(function ($q) use ($stems) {
                foreach ($stems as $s) {
                    $q->orWhere('term', 'LIKE', $s . '%');
                }
            })
            ->pluck('link_id')
            ->unique();

        $builder = Link::active()
            ->whereIn('links.id', $candidateIds)
            ->with(['latestCrawlLog', 'crawlContent']);

        if ($categoryFilter && $categoryFilter !== 'all') {
            $builder->where('links.category', $categoryFilter);
        }
        if ($uptimeFilter && $uptimeFilter !== 'all') {
            $builder->where('links.uptime_status', $uptimeFilter);
        }

        match ($sortBy) {
            'newest'          => $builder->orderByDesc('links.created_at'),
            'oldest'          => $builder->orderBy('links.created_at'),
            'most_checked'    => $builder->orderByDesc('links.check_count'),
            'recently_checked'=> $builder->orderByDesc('links.last_check'),
            'title_asc'       => $builder->orderBy('links.title'),
            'title_desc'      => $builder->orderByDesc('links.title'),
            default           => $builder->orderByDesc('links.created_at'),
        };

        $paginator = $builder->paginate(15)->withQueryString();

        // Enrich with highlights
        $paginator->getCollection()->each(function (Link $link) use ($tokens, $engine) {
            $link->highlighted_title       = $engine->highlight($link->title, $tokens, 120, true);
            $link->highlighted_description = $engine->highlight($link->description, $tokens, 240);
            $link->snippet_content         = $engine->buildSnippet($link->crawlContent?->body_text, $tokens);
            $link->search_score            = null;
        });

        return $paginator;
    }

    /**
     * Build category breakdown from the current index result set.
     */
    private function buildCategoryBreakdown(
        string $effectiveQuery,
        string $categoryFilter,
        SearchEngineService $engine
    ): array {
        if ($categoryFilter !== 'all') {
            return [];
        }

        $interpretation = $engine->interpret($effectiveQuery);
        $stems = $interpretation['stems'];

        if (empty($stems)) {
            return [];
        }

        $candidateIds = \Illuminate\Support\Facades\DB::table('search_index')
            ->whereIn('term', $stems)
            ->pluck('link_id')
            ->unique();

        return Link::active()
            ->whereIn('id', $candidateIds)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    private function trackVisitor(Request $request): void
    {
        $sessionId = $request->session()->getId();
        $visitor   = \App\Models\Visitor::firstOrNew(['session_id' => $sessionId]);
        if (!$visitor->exists) {
            $visitor->ip_address = $request->ip();
            $visitor->views      = 1;
        }
        $visitor->last_active_at = now();
        $visitor->save();
    }

    private function randomAds($query): \Illuminate\Database\Eloquent\Collection
    {
        $ids = (clone $query)->pluck('id')->toArray();
        if (empty($ids)) {
            return \Illuminate\Database\Eloquent\Collection::make();
        }
        shuffle($ids);
        $records = Advertisement::whereIn('id', $ids)->get()->keyBy('id');
        return \Illuminate\Database\Eloquent\Collection::make(
            array_map(fn($id) => $records[$id], $ids)
        );
    }

    private function orderedSponsoredAds($query): \Illuminate\Database\Eloquent\Collection
    {
        $ads = (clone $query)->get();
        if ($ads->isEmpty()) {
            return $ads;
        }

        $priorities = [
            'elite' => 10, 'pro' => 8, 'premium' => 6,
            'standard' => 4, 'basic' => 2, 'starter' => 0,
        ];

        $highTiers   = $ads->filter(fn($ad) => ($priorities[$ad->package_tier] ?? 0) >= 6);
        $lowTiers    = $ads->filter(fn($ad) => ($priorities[$ad->package_tier] ?? 0) < 6);
        $highSorted  = $highTiers->sortByDesc(fn($ad) => $priorities[$ad->package_tier] ?? 0);
        $lowShuffled = $lowTiers->shuffle();

        return $highSorted->concat($lowShuffled);
    }
}
