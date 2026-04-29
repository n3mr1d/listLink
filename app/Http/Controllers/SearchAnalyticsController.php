<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SearchAnalyticsController extends Controller
{
    public function trackAndRedirect(Request $request)
    {
        $url = $request->get('url');
        $query = $request->get('q');
        $linkId = $request->get('link_id');

        if (!$url) {
            return redirect()->to('/');
        }

        // Track silently
        if ($query && $linkId && Schema::hasTable('search_analytics')) {
            try {
                DB::table('search_analytics')->updateOrInsert(
                    ['query_term' => $query, 'link_id' => $linkId],
                    [
                        'click_count' => DB::raw('click_count + 1'),
                        'last_clicked_at' => now(),
                        'updated_at' => now()
                    ]
                );
            } catch (\Exception $e) {
                // Ignore tracking errors to ensure user gets to destination
            }
        }

        return redirect()->away($url);
    }

    /**
     * Track a click on a search result via AJAX (optional fallback).
     */
    public function trackClick(Request $request)
    {
        $query = $request->get('q');
        $linkId = $request->get('link_id');

        if (!$query || !$linkId) {
            return response()->json(['status' => 'error'], 400);
        }

        // Handle case where table might not exist yet due to migration issues
        if (!Schema::hasTable('search_analytics')) {
            return response()->json(['status' => 'skipped (no table)']);
        }

        try {
            DB::table('search_analytics')->updateOrInsert(
                ['query_term' => $query, 'link_id' => $linkId],
                [
                    'click_count' => DB::raw('click_count + 1'),
                    'last_clicked_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            // Periodically update CTR (simplified logic)
            DB::table('search_analytics')
                ->where('query_term', $query)
                ->where('link_id', $linkId)
                ->update([
                    'ctr' => DB::raw('click_count / impression_count')
                ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Track impressions for search results (batch).
     */
    public function trackImpressions(Request $request)
    {
        $query = $request->get('q');
        $linkIds = $request->get('link_ids', []);

        if (!$query || empty($linkIds) || !Schema::hasTable('search_analytics')) {
            return response()->json(['status' => 'skipped']);
        }

        foreach ($linkIds as $id) {
            DB::table('search_analytics')->updateOrInsert(
                ['query_term' => $query, 'link_id' => $id],
                [
                    'impression_count' => DB::raw('impression_count + 1'),
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        return response()->json(['status' => 'success']);
    }
}
