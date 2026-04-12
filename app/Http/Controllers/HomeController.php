<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Enum\AdPlacement;
use App\Models\Advertisement;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\AdTrackingController;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $this->trackVisitor($request);
        $data = $this->getHomeData();
        return view('home', $data);
    }

    private function trackVisitor(Request $request)
    {
        $sessionId = $request->session()->getId();
        $ipAddress = $request->ip();

        $visitor = \App\Models\Visitor::firstOrNew(['session_id' => $sessionId]);
        if (!$visitor->exists) {
            $visitor->ip_address = $ipAddress;
            $visitor->views = 1;
        } else {
            $visitor->views++;
        }
        $visitor->last_active_at = now();
        $visitor->save();
    }

    public function directory(): View
    {
        $data = $this->getHomeData();
        // The directory view might expect 'links' variable which is paginated links.
        // The index method already provides that.
        return view('directory', $data);
    }

    private function getHomeData()
    {
        // Only show links from registered users (user_id is NOT null)
        $links = Link::active()
            ->online()
            ->whereNotNull('user_id')
            ->with('user')
            ->latest()
            ->paginate(20);

        $categories = Category::cases();

        $headerAds = $this->randomAds(
            Advertisement::active()->byPlacement(AdPlacement::HEADER)
        );

        $sidebarAds = $this->randomAds(
            Advertisement::active()->byPlacement(AdPlacement::SIDEBAR)
        );

        $sponsoredLinks = $this->randomAds(
            Advertisement::active()->where('ad_type', 'sponsored')
        );

        $stats = [
            'total_links' => Link::active()->whereNotNull('user_id')->count(),
            'online_links' => Link::active()->whereNotNull('user_id')->where('uptime_status', 'online')->count(),
            'categories' => count($categories),
            'indexed_count' => \App\Models\CrawlContent::count(),
            'total_users' => User::count(),
            'live_viewers' => \App\Models\Visitor::where('last_active_at', '>=', now()->subMinutes(5))->count(),
            'total_views' => \App\Models\Visitor::sum('views') ?? 0,
        ];

        $recentlyAddedLinks = Link::active()
            ->online()
            ->whereNotNull('user_id')
            ->with(['user'])
            ->latest()
            ->take(5)
            ->get();

        $recentlyRegisteredUser = User::latest()
            ->first();

        // Track impressions
        AdTrackingController::trackImpressions($headerAds);
        AdTrackingController::trackImpressions($sidebarAds);
        AdTrackingController::trackImpressions($sponsoredLinks);

        return compact(
            'links',
            'categories',
            'headerAds',
            'sidebarAds',
            'sponsoredLinks',
            'stats',
            'recentlyAddedLinks',
            'recentlyRegisteredUser'
        );
    }

    /**
     * Fetch ads in a random order without using ORDER BY RAND().
     * Retrieves IDs via a lightweight indexed query, shuffles in PHP,
     * then fetches the full records — no temp disk files created in MariaDB.
     */
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
