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
        
        // Get all ads for this user
        $ads = Advertisement::where('user_id', $user->id)
            ->with(['stats'])
            ->latest()
            ->get();
            
        $adIds = $ads->pluck('id');
        
        // General stats for the dashboard
        $statsData = AdStat::whereIn('advertisement_id', $adIds)
            ->selectRaw('SUM(impressions) as total_impressions, SUM(clicks) as total_clicks')
            ->first();
            
        $totalImpressions = $statsData->total_impressions ?? 0;
        $totalClicks = $statsData->total_clicks ?? 0;
        $ctr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
        
        // Daily chart data (last 30 days)
        $chartData = AdStat::whereIn('advertisement_id', $adIds)
            ->where('date', '>=', now()->subDays(30))
            ->select('date')
            ->selectRaw('SUM(impressions) as impressions, SUM(clicks) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $labels = $chartData->pluck('date')->map(fn($d) => $d->format('M d'));
        $impressions = $chartData->pluck('impressions');
        $clicks = $chartData->pluck('clicks');
        
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
