<?php

namespace App\Http\Controllers;

use App\Enum\AdPlacement;
use App\Enum\Category;
use App\Enum\UptimeStatus;
use App\Models\Advertisement;
use App\Models\CrawlContent;
use App\Models\Link;
use App\Models\User;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $query = trim($request->get('q', ''));
        $categoryFilter = $request->get('category', 'all');
        $uptimeFilter = $request->get('uptime', 'all');
        $sortBy = $request->get('sort', 'relevance');
        $links = null;
        $searchTime = null;
        $categoryBreakdown = [];

        // ── Search intelligence (server-side only) ──────────────────────
        $searchService   = new SearchService();
        $interpretation  = null;   // ['original','corrected','tokens','intent','is_exact','filters']
        $correctedQuery  = null;   // corrected query string (if typo detected)
        $searchTokens    = [];     // tokens used for highlighting
        $relatedSuggestions = [];  // related search phrases

        if (strlen($query) >= 2) {
            $startTime = microtime(true);

            // ── Query intelligence ───────────────────────────────────────
            $interpretation = $searchService->interpret($query);
            $correctedQuery = $interpretation['corrected'];   // null if no correction
            $searchTokens   = $interpretation['tokens'];

            // Use corrected query for actual DB search if available
            $effectiveQuery = $correctedQuery ?? $query;

            // ─── Optimized Search (inspired by Ahmia-index) ──────────────
            // Use MySQL FULLTEXT for 3+ character queries, boosting title
            // matches over content matches. Falls back to LIKE for short queries.
            $builder = Link::active();

            if (mb_strlen($effectiveQuery) >= 3) {
                // Multi-field boosted FULLTEXT search:
                // We left join crawl_contents to search body_text/meta/h1
                // and compute relevance in a single pass.
                $builder->leftJoin('crawl_contents', 'links.id', '=', 'crawl_contents.link_id')
                    ->select('links.id', 'links.title', 'links.description', 'links.url', 'links.slug', 'links.uptime_status', 'links.category', 'links.created_at', 'links.last_check', 'links.user_id')
                    ->where(function ($q) use ($effectiveQuery) {
                        $q->whereRaw('MATCH(links.title, links.description) AGAINST(? IN BOOLEAN MODE)', [$effectiveQuery])
                          ->orWhereRaw('MATCH(crawl_contents.h1, crawl_contents.meta_description, crawl_contents.body_text) AGAINST(? IN BOOLEAN MODE)', [$effectiveQuery])
                          ->orWhere('links.url', 'LIKE', "%{$effectiveQuery}%");
                    });

                // Add relevance scoring for sorting
                if ($sortBy === 'relevance') {
                    $builder->selectRaw(
                        '(COALESCE(MATCH(links.title, links.description) AGAINST(? IN BOOLEAN MODE), 0) * 3
                          + COALESCE(MATCH(crawl_contents.h1, crawl_contents.meta_description, crawl_contents.body_text) AGAINST(? IN BOOLEAN MODE), 0)
                        ) as relevance_score',
                        [$effectiveQuery, $effectiveQuery]
                    )->orderByDesc('relevance_score');
                }
            } else {
                // Short query: fallback to LIKE-based search
                $builder->search($effectiveQuery);
            }

            // Category filter
            if ($categoryFilter && $categoryFilter !== 'all') {
                $builder->where('links.category', $categoryFilter);
            }

            // Uptime status filter
            if ($uptimeFilter && $uptimeFilter !== 'all') {
                $builder->where('links.uptime_status', $uptimeFilter);
            }

            // Sorting (if not relevance, which is already handled above)
            if ($sortBy !== 'relevance') {
                switch ($sortBy) {
                    case 'newest':
                        $builder->orderByDesc('links.created_at');
                        break;
                    case 'oldest':
                        $builder->orderBy('links.created_at');
                        break;
                    case 'most_checked':
                        $builder->orderByDesc('links.check_count');
                        break;
                    case 'recently_checked':
                        $builder->orderByDesc('links.last_check');
                        break;
                    case 'title_asc':
                        $builder->orderBy('links.title');
                        break;
                    case 'title_desc':
                        $builder->orderByDesc('links.title');
                        break;
                }
            }

            $links = $builder->with(['user', 'latestCrawlLog', 'crawlContent'])->paginate(15)->withQueryString();

            // Enrich result set with pre-calculated snippets and highlighted descriptions
            // This moves processing away from the Blade render loop.
            $links->getCollection()->each(function ($link) use ($searchService, $searchTokens) {
                $link->snippet_content = ($link->crawlContent && $link->crawlContent->body_text) 
                    ? $searchService->getSnippets($link->crawlContent->body_text, $searchTokens, 120, 1) 
                    : null;
                
                $link->highlighted_description = $link->description 
                    ? $searchService->highlight($link->description, $searchTokens, 220) 
                    : null;
            });

            $searchTime = round((microtime(true) - $startTime) * 1000, 1); // ms

            // Category breakdown for search results sidebar
            if ($categoryFilter === 'all') {
                $categoryBreakdown = Link::active()
                    ->search($effectiveQuery)
                    ->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray();
            }

            // Related suggestions (server-side, derived from result set)
            $relatedSuggestions = $searchService->relatedSuggestions(
                $effectiveQuery,
                $links->getCollection()
            );
        }

        $stats = [
            'total_links' => Link::active()->count(),
            'online_links' => Link::active()->where('uptime_status', UptimeStatus::ONLINE)->count(),
            'categories' => count(Category::cases()),
            'indexed_count' => CrawlContent::count(),
            'total_users' => User::count(),
            'live_viewers' => \App\Models\Visitor::where('last_active_at', '>=', now()->subMinutes(5))->count(),
            'total_views' => \App\Models\Visitor::count(),
        ];

        $recentlyAddedLinks = Link::active()
            ->online()
            ->with(['user'])
            ->latest()
            ->take(4)
            ->get();

        $recentlyRegisteredUser = User::latest()->first();

        $categories = Category::cases();

        $externalAds = \App\Services\AdMateService::getBanners();

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
            'categoryBreakdown',
            'interpretation',
            'correctedQuery',
            'searchTokens',
            'relatedSuggestions',
            'searchService',
            'externalAds'
        ));
    }

    private function trackVisitor(Request $request)
    {
        $sessionId = $request->session()->getId();
        $ipAddress = $request->ip();

        $visitor = \App\Models\Visitor::firstOrNew(['session_id' => $sessionId]);
        if (!$visitor->exists) {
            $visitor->ip_address = $ipAddress;
            $visitor->views = 1;
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

        // Fetch records and preserve the shuffled order
        $records = Advertisement::whereIn('id', $ids)->get()->keyBy('id');

        return \Illuminate\Database\Eloquent\Collection::make(
            array_map(fn($id) => $records[$id], $ids)
        );
    }

    /**
     * Fetch sponsored ads sorted by package priority (Elite > Pro > Premium first,
     * lower tiers shuffled) — same ordering logic as HomeController.
     */
    private function orderedSponsoredAds($query): \Illuminate\Database\Eloquent\Collection
    {
        $ads = (clone $query)->get();

        if ($ads->isEmpty()) {
            return $ads;
        }

        $priorities = [
            'elite'    => 10,
            'pro'      => 8,
            'premium'  => 6,
            'standard' => 4,
            'basic'    => 2,
            'starter'  => 0,
        ];

        $highTiers  = $ads->filter(fn($ad) => ($priorities[$ad->package_tier] ?? 0) >= 6);
        $lowTiers   = $ads->filter(fn($ad) => ($priorities[$ad->package_tier] ?? 0) < 6);
        $highSorted = $highTiers->sortByDesc(fn($ad) => $priorities[$ad->package_tier] ?? 0);
        $lowShuffled = $lowTiers->shuffle();

        return $highSorted->concat($lowShuffled);
    }
}
