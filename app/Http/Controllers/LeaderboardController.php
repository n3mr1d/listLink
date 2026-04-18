<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    /**
     * Display the leaderboard of users who submitted the most active links.
     */
    public function index(): View
    {
        // 1. Top Link Contributors
        $topContributors = User::select('users.id', 'users.username', 'users.created_at')
            ->join('links', 'users.id', '=', 'links.user_id')
            ->where('links.status', \App\Enum\Status::ACTIVE)
            ->where('links.is_duplicate', false)
            ->selectRaw('COUNT(links.id) as links_count')
            ->groupBy('users.id', 'users.username', 'users.created_at')
            ->orderByDesc('links_count')
            ->limit(10)
            ->get();

        // 2. Top Ads by Clicks
        $topAdsByClicks = \App\Models\Advertisement::select('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->join('ad_stats', 'advertisements.id', '=', 'ad_stats.advertisement_id')
            ->selectRaw('SUM(ad_stats.clicks) as total_clicks')
            ->groupBy('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->orderByDesc('total_clicks')
            ->limit(5)
            ->get();

        // 3. Top Ads by Views (Impressions)
        $topAdsByViews = \App\Models\Advertisement::select('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->join('ad_stats', 'advertisements.id', '=', 'ad_stats.advertisement_id')
            ->selectRaw('SUM(ad_stats.impressions) as total_views')
            ->groupBy('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->orderByDesc('total_views')
            ->limit(5)
            ->get();

        // 4. Top Advertisers (Users with most active ads)
        $topAdvertisers = User::select('users.id', 'users.username', 'users.created_at')
            ->join('advertisements', 'users.id', '=', 'advertisements.user_id')
            ->where('advertisements.status', 'active')
            ->selectRaw('COUNT(advertisements.id) as ads_count')
            ->groupBy('users.id', 'users.username', 'users.created_at')
            ->orderByDesc('ads_count')
            ->limit(10)
            ->get();

        return view('leaderboard', compact('topContributors', 'topAdsByClicks', 'topAdsByViews', 'topAdvertisers'));
    }
}
