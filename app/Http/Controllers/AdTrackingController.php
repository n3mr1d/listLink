<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdStat;
use Illuminate\Http\Request;

class AdTrackingController extends Controller
{
    /**
     * Track a click on an advertisement and redirect to the target URL.
     */
    public function trackClick(int $id)
    {
        $ad = Advertisement::findOrFail($id);

        // Record the click
        $stat = AdStat::firstOrCreate(
            ['advertisement_id' => $ad->id, 'date' => now()->toDateString()],
            ['impressions' => 0, 'clicks' => 0]
        );

        $stat->increment('clicks');

        return redirect()->away($ad->url);
    }

    /**
     * Static helper to track impressions in bulk.
     * Can be called from other controllers.
     */
    public static function trackImpressions($ads)
    {
        if (!$ads || count($ads) === 0) return;

        $date = now()->toDateString();
        $ids = [];
        
        if (is_iterable($ads)) {
            foreach ($ads as $ad) {
                $ids[] = $ad->id;
            }
        } else {
            $ids[] = $ads->id;
        }

        foreach ($ids as $id) {
            $stat = AdStat::firstOrCreate(
                ['advertisement_id' => $id, 'date' => $date],
                ['impressions' => 0, 'clicks' => 0]
            );
            $stat->increment('impressions');
        }
    }
}
