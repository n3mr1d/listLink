<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Enum\UptimeStatus;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim($request->get('q', ''));
        $categoryFilter = $request->get('category', 'all');
        $uptimeFilter = $request->get('uptime', 'all');
        $sortBy = $request->get('sort', 'relevance');
        $links = null;
        $searchTime = null;
        $categoryBreakdown = [];

        if (strlen($query) >= 2) {
            $startTime = microtime(true);

            // Search ALL active links (both anonymous and registered users)
            $builder = Link::active()->search($query);

            // Category filter
            if ($categoryFilter && $categoryFilter !== 'all') {
                $builder->where('category', $categoryFilter);
            }

            // Uptime status filter
            if ($uptimeFilter && $uptimeFilter !== 'all') {
                $builder->where('uptime_status', $uptimeFilter);
            }

            // Sorting
            switch ($sortBy) {
                case 'newest':
                    $builder->orderByDesc('created_at');
                    break;
                case 'oldest':
                    $builder->orderBy('created_at');
                    break;
                case 'most_checked':
                    $builder->orderByDesc('check_count');
                    break;
                case 'recently_checked':
                    $builder->orderByDesc('last_check');
                    break;
                case 'title_asc':
                    $builder->orderBy('title');
                    break;
                case 'title_desc':
                    $builder->orderByDesc('title');
                    break;
                default: // relevance — no extra ordering, MySQL handles it via LIKE
                    break;
            }

            $links = $builder->with('user')->paginate(15)->withQueryString();

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

        // Stats for widget — count ALL active links (anonymous + registered)
        $totalLinks = Link::active()->count();
        $onlineLinks = Link::active()->where('uptime_status', UptimeStatus::ONLINE)->count();

        // Recent popular searches (we track top categories as "suggestions")
        $popularCategories = Link::active()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->limit(6)
            ->pluck('count', 'category')
            ->toArray();

        $categories = Category::cases();

        return view('search', compact(
            'links',
            'query',
            'categoryFilter',
            'uptimeFilter',
            'sortBy',
            'categories',
            'searchTime',
            'categoryBreakdown',
            'totalLinks',
            'onlineLinks',
            'popularCategories'
        ));
    }
}
