<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrawlLog;
use App\Models\Link;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Show the master search interface.
     */
    public function index()
    {
        return view('admin.search-master');
    }

    /**
     * Handle AJAX live search requests.
     */
    public function live(Request $request)
    {
        $q = $request->get('q', '');
        $type = $request->get('type', 'links'); // 'links' or 'crawls'

        if (!$q || strlen($q) < 2) {
            return response()->json(['results' => [], 'count' => 0]);
        }

        if ($type === 'links') {
            $results = Link::with('user')
                ->where(function($query) use ($q) {
                    $query->where('url', 'like', "%{$q}%")
                          ->orWhere('title', 'like', "%{$q}%")
                          ->orWhere('description', 'like', "%{$q}%");
                })
                ->latest()
                ->limit(20)
                ->get()
                ->map(function($link) {
                    return [
                        'id' => $link->id,
                        'title' => $link->title ?? 'Untitled',
                        'url' => $link->url,
                        'status' => $link->uptime_status->label(),
                        'crawl_status' => $link->crawl_status,
                        'last_checked' => $link->last_check ? $link->last_check->diffForHumans() : 'Never',
                        'edit_url' => route('admin.links.edit', $link->id),
                        'delete_url' => route('admin.links.delete', $link->id),
                    ];
                });
        } else {
            $results = CrawlLog::with('link')
                ->whereHas('link', function($query) use ($q) {
                    $query->where('url', 'like', "%{$q}%")
                          ->orWhere('title', 'like', "%{$q}%");
                })
                ->orWhere('status', 'like', "%{$q}%")
                ->orWhere('error_message', 'like', "%{$q}%")
                ->latest()
                ->limit(20)
                ->get()
                ->map(function($log) {
                    return [
                        'id' => $log->id,
                        'url' => $log->link->url,
                        'status' => $log->status,
                        'response_time' => $log->response_time_ms ? $log->response_time_ms . 'ms' : '-',
                        'checked_at' => $log->created_at->diffForHumans(),
                        'error' => $log->error_message,
                    ];
                });
        }

        return response()->json([
            'results' => $results,
            'count' => count($results)
        ]);
    }
}
