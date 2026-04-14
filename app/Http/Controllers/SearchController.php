<?php

namespace App\Http\Controllers;

use App\Enum\AdPlacement;
use App\Enum\Category;
use App\Enum\UptimeStatus;
use App\Models\Advertisement;
use App\Models\CrawlContent;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $this->trackVisitor($request);

        $headerAds = $this->randomAds(
            Advertisement::active()->byPlacement(AdPlacement::HEADER)
        );

        $query = trim($request->get('q', ''));
        $categoryFilter = $request->get('category', 'all');
        $uptimeFilter = $request->get('uptime', 'all');
        $sortBy = $request->get('sort', 'relevance');
        $links = null;
        $searchTime = null;
        $categoryBreakdown = [];

        if (strlen($query) >= 2) {
            $startTime = microtime(true);

            // ─── Optimized Search (inspired by Ahmia-index) ──────────────
            // Use MySQL FULLTEXT for 3+ character queries, boosting title
            // matches over content matches. Falls back to LIKE for short queries.
            $builder = Link::active();

            if (mb_strlen($query) >= 3) {
                // Multi-field boosted FULLTEXT search:
                // 1. FULLTEXT on links.title + links.description
                // 2. JOIN crawl_contents to search body_text/meta/h1
                // 3. Combined relevance scoring (title weighted higher)
                $builder->where(function ($q) use ($query) {
                    $q->whereRaw(
                        'MATCH(links.title, links.description) AGAINST(? IN BOOLEAN MODE)',
                        [$query]
                    )->orWhereHas('crawlContent', function ($sub) use ($query) {
                        $sub->whereRaw(
                            'MATCH(h1, meta_description, body_text) AGAINST(? IN BOOLEAN MODE)',
                            [$query]
                        );
                    })->orWhere('links.url', 'LIKE', "%{$query}%")
                        ->orWhereHas('discoveredLinks', function ($sub) use ($query) {
                            $sub->where('url', 'LIKE', "%{$query}%");
                        });
                });

                // Add relevance scoring for sorting
                if ($sortBy === 'relevance') {
                    $builder->leftJoin('crawl_contents', 'links.id', '=', 'crawl_contents.link_id')
                        ->select('links.*')
                        ->selectRaw(
                            '(
                                COALESCE(MATCH(links.title, links.description) AGAINST(? IN BOOLEAN MODE), 0) * 3
                                + COALESCE(MATCH(crawl_contents.h1, crawl_contents.meta_description, crawl_contents.body_text) AGAINST(? IN BOOLEAN MODE), 0)
                            ) as relevance_score',
                            [$query, $query]
                        )
                        ->orderByDesc('relevance_score');
                }
            } else {
                // Short query: fallback to LIKE-based search
                $builder->search($query);
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

            $links = $builder->with(['user', 'latestCrawlLog'])->paginate(15)->withQueryString();

            $searchTime = round((microtime(true) - $startTime) * 1000, 1); // ms

            // Category breakdown for search results sidebar
            if ($categoryFilter === 'all') {
                $categoryBreakdown = Link::active()
                    ->search($query)
                    ->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray();
            }
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
            'categoryBreakdown',
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
}
