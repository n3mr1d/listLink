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
        }
        $visitor->last_active_at = now();
        $visitor->save();
    }

    public function directory(): View
    {
        $data = $this->getHomeData();
        return view('directory', $data);
    }

    public function offline(Request $request): View
    {
        $search = $request->get('q', '');
        $categoryFilter = $request->get('cat', '');

        $query = Link::where('status', 'active')
            ->where('uptime_status', 'offline')
            ->where('is_duplicate', false);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        $links = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::cases();

        $totalOffline = Link::where('status', 'active')->where('uptime_status', 'offline')->count();
        $totalOnline = Link::where('status', 'active')->where('uptime_status', 'online')->count();
        $totalAll = $totalOffline + $totalOnline;
        $offlinePercent = $totalAll > 0 ? round(($totalOffline / $totalAll) * 100, 1) : 0;

        return view('offline', compact(
            'links',
            'categories',
            'search',
            'categoryFilter',
            'totalOffline',
            'totalOnline',
            'offlinePercent'
        ));
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

        $headerAds = $this->getOrderedAds(
            Advertisement::active()->byPlacement(AdPlacement::HEADER)
        );

        $sidebarAds = $this->getOrderedAds(
            Advertisement::active()->byPlacement(AdPlacement::SIDEBAR)
        );

        $sponsoredLinks = $this->getOrderedAds(
            Advertisement::active()->where('ad_type', 'sponsored')
        );

        $stats = [
            'total_links' => Link::active()->whereNotNull('user_id')->count(),
            'online_links' => Link::active()->whereNotNull('user_id')->where('uptime_status', 'online')->count(),
            'categories' => count($categories),
            'indexed_count' => \App\Models\CrawlContent::count(),
            'total_users' => User::count(),
            'live_viewers' => \App\Models\Visitor::where('last_active_at', '>=', now()->subMinutes(5))->count(),
            'total_views' => \App\Models\Visitor::count(),
        ];

        $recentlyAddedLinks = Link::active()
            ->online()

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
            'recentlyRegisteredUser',
            'externalAds' => \App\Services\AdMateService::getBanners()
        );
    }

    /**
     * Fetch ads in a random order without using ORDER BY RAND().
     * Retrieves IDs via a lightweight indexed query, shuffles in PHP,
     * then fetches the full records — no temp disk files created in MariaDB.
     */
    /**
     * Fetch ads sorted by package priority (Elite > Pro > Premium > Others shuffled).
     */
    private function getOrderedAds($query): \Illuminate\Database\Eloquent\Collection
    {
        $ads = (clone $query)->get();

        if ($ads->isEmpty()) {
            return $ads;
        }

        // Define tier priority (higher value = higher priority)
        $priorities = [
            'elite' => 10,
            'pro' => 8,
            'premium' => 6,
            'standard' => 4,
            'basic' => 2,
            'starter' => 0,
        ];

        // Group by priority
        $highTiers = $ads->filter(fn($ad) => ($priorities[$ad->package_tier] ?? 0) >= 6);
        $lowTiers = $ads->filter(fn($ad) => ($priorities[$ad->package_tier] ?? 0) < 6);

        // Sort high tiers by absolute priority
        $highSorted = $highTiers->sortByDesc(fn($ad) => $priorities[$ad->package_tier] ?? 0);

        // Shuffle low tiers to keep it fair for basic ads
        $lowShuffled = $lowTiers->shuffle();

        return $highSorted->concat($lowShuffled);
    }
}
