<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BtcRateController extends Controller
{
    /**
     * Fetch the current BTC/USD rate.
     * Cached for 60 seconds to avoid hammering the external API.
     */
    public function rate(): JsonResponse
    {
        $rate = Cache::remember('btc_usd_rate', 60, function () {
            try {
                // Primary: CoinGecko (free, no auth)
                $response = Http::timeout(8)->get(
                    'https://api.coingecko.com/api/v3/simple/price',
                    ['ids' => 'bitcoin', 'vs_currencies' => 'usd']
                );

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['bitcoin']['usd'] ?? null;
                }
            } catch (\Throwable) {
                // fall through to backup
            }

            try {
                // Backup: Blockchain.info
                $response = Http::timeout(8)->get('https://blockchain.info/ticker');
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['USD']['last'] ?? null;
                }
            } catch (\Throwable) {
                // fall through
            }

            return null;
        });

        if ($rate === null) {
            return response()->json(['error' => 'Rate unavailable'], 503);
        }

        return response()->json([
            'usd' => (float) $rate,
            'cached_at' => now()->toIso8601String(),
        ]);
    }
}
