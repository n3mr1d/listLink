<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\UptimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UptimeController extends Controller
{
    public function check(Request $request, int $id)
    {
        $link = Link::where('status', 'active')->findOrFail($id);

        // Cache check — prevent spam (5 minute cooldown per link)
        $cacheKey = "uptime_check_{$link->id}";
        if (Cache::has($cacheKey)) {
            return redirect()->route('link.show', $link->slug)
                ->with('info', 'This link was checked recently. Showing cached result.');
        }

        // Perform the uptime check via Tor proxy
        $status = 'unknown';
        $responseTime = null;

        try {
            $start = microtime(true);
            $response = Http::withOptions([
                'proxy' => 'socks5h://127.0.0.1:9050',
                'timeout' => 15,
                'connect_timeout' => 10,
                'allow_redirects' => false, // SSRF prevention
            ])->head($link->url);

            $responseTime = (int) round((microtime(true) - $start) * 1000);

            if ($response->successful() || $response->isRedirect()) {
                $status = 'online';
            } else {
                $status = 'offline';
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $status = 'timeout';
            $responseTime = 15000;
        } catch (\Exception $e) {
            $status = 'offline';
            $responseTime = null;
        }

        // Log the check (IP hashed for privacy)
        UptimeLog::create([
            'link_id' => $link->id,
            'checked_by_ip_hash' => hash('sha256', $request->ip() . config('app.key')),
            'status' => $status,
            'response_time_ms' => $responseTime,
            'checked_at' => now(),
        ]);

        // Update link status
        $link->update([
            'uptime_status' => $status,
            'last_check' => now(),
            'check_count' => $link->check_count + 1,
        ]);

        // Cache for 5 minutes to prevent repeated checks
        Cache::put($cacheKey, $status, 300);

        $statusLabel = match ($status) {
            'online' => 'Online',
            'offline' => 'Offline',
            'timeout' => 'Timeout (no response within 15s)',
            default => 'Unknown',
        };

        return redirect()->route('link.show', $link->slug)
            ->with('check_result', "Status check complete: {$statusLabel}");
    }
}
