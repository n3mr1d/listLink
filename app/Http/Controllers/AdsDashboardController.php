<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdsDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all ads for this user - strictly filtered by owner
        $ads = Advertisement::where('user_id', $user->id)
            ->with(['stats'])
            ->latest()
            ->get();
            
        $adIds = $ads->pluck('id');
        
        // General stats for the dashboard
        $totalImpressions = AdStat::whereIn('advertisement_id', $adIds)->sum('impressions');
        $totalClicks = AdStat::whereIn('advertisement_id', $adIds)->sum('clicks');
        $ctr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
        
        // Daily chart data (last 30 days) - ensuring continuous dates
        $stats = AdStat::whereIn('advertisement_id', $adIds)
            ->whereDate('date', '>=', now()->subDays(30))
            ->selectRaw('DATE(date) as date_only, SUM(impressions) as impressions, SUM(clicks) as clicks')
            ->groupBy('date_only')
            ->orderBy('date_only')
            ->get()
            ->keyBy('date_only');

        $labels = [];
        $impressions = [];
        $clicks = [];

        for ($i = 30; $i >= 0; $i--) {
            $currentDate = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('M d');
            
            if (isset($stats[$currentDate])) {
                $impressions[] = $stats[$currentDate]->impressions;
                $clicks[] = $stats[$currentDate]->clicks;
            } else {
                $impressions[] = 0;
                $clicks[] = 0;
            }
        }
        
        // Performance by placement
        $placementStats = Advertisement::where('user_id', $user->id)
            ->join('ad_stats', 'advertisements.id', '=', 'ad_stats.advertisement_id')
            ->select('placement', DB::raw('SUM(impressions) as total_impressions'), DB::raw('SUM(clicks) as total_clicks'))
            ->groupBy('placement')
            ->get();

        return view('user.ads-dashboard', compact(
            'ads', 
            'totalImpressions', 
            'totalClicks', 
            'ctr', 
            'labels', 
            'impressions', 
            'clicks',
            'placementStats'
        ));
    }
}
