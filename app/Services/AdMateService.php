<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdMateService
{
    /**
     * Fetch banners from AdMate API via Tor proxy.
     * Cached for 1 hour to ensure fast page loads.
     */
    public static function getBanners()
    {
        return Cache::remember('admate_banners_data', 3600, function () {
            try {
                $proxy = config('crawler.proxy', 'socks5h://127.0.0.1:9050');
                $url = 'http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion/api/get-banner/s4bSEp2XFUpCAA4o/type/468-60/count/10/' . window_hostname_placeholder();

                // Append domain if possible, but for server-side we'll just use the base URL
                $baseUrl = 'http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion/api/get-banner/s4bSEp2XFUpCAA4o/type/468-60/count/10';
                
                $response = Http::withOptions([
                    'proxy' => $proxy,
                    'curl' => [
                        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5_HOSTNAME,
                    ],
                    'timeout' => 20,
                    'verify' => false,
                ])->get($baseUrl);

                if ($response->successful()) {
                    $data = $response->json();
                    return is_array($data) ? $data : [];
                }
            } catch (\Exception $e) {
                Log::warning('[AdMate] Could not fetch external ads: ' . $e->getMessage());
            }

            return [];
        });
    }
}
