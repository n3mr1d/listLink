<?php

namespace App\Http\Controllers;

use App\Jobs\CrawlLinkJob;
use App\Models\CrawlContent;
use App\Models\CrawlLog;
use App\Models\DiscoveredLink;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrawlerController extends Controller
{
    /**
     * Admin crawler dashboard — stats + link table + recent logs.
     */
    public function index(): View
    {
        $stats = [
            'total'         => Link::count(),
            'never_crawled' => Link::whereNull('last_crawled_at')->count(),
            'success'       => Link::where('crawl_status', 'success')->count(),
            'failed'        => Link::where('crawl_status', 'failed')->count(),
            'pending'       => Link::where('crawl_status', 'pending')->count(),
            'force_queued'  => Link::where('force_recrawl', true)->count(),
            'discovered'    => DiscoveredLink::count(),
            'indexed'       => CrawlContent::count(),
        ];

        // Average response time last 24h
        $stats['avg_response_ms'] = CrawlLog::where('created_at', '>=', now()->subDay())
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');
        $stats['avg_response_ms'] = $stats['avg_response_ms']
            ? round($stats['avg_response_ms'])
            : null;

        // Crawls in last 24h
        $stats['crawls_24h'] = CrawlLog::where('created_at', '>=', now()->subDay())->count();
        $stats['success_24h'] = CrawlLog::where('created_at', '>=', now()->subDay())
            ->where('status', 'success')->count();

        $links = Link::latest('last_crawled_at')->paginate(25)->withQueryString();

        // Recent crawl logs for the activity feed
        $recentLogs = CrawlLog::with('link')
            ->latest()
            ->limit(20)
            ->get();

        // Crawl interval from config
        $crawlInterval = config('crawler.interval_days', 4);

        return view('admin.crawler.index', compact(
            'stats', 'links', 'recentLogs', 'crawlInterval'
        ));
    }

    /**
     * Dispatch crawl jobs for all eligible links (smart mode).
     */
    public function dispatch()
    {
        $interval = now()->subDays(config('crawler.interval_days', 4));

        $links = Link::where(function ($q) use ($interval) {
            $q->whereNull('last_crawled_at')
              ->orWhere('force_recrawl', true)
              ->orWhere('last_crawled_at', '<=', $interval);
        })->get();

        foreach ($links as $link) {
            CrawlLinkJob::dispatch($link->id);
        }

        $count = $links->count();

        return redirect()->route('admin.crawler.index')
            ->with('success', "✓ Dispatched {$count} crawl job(s) (smart mode).");
    }

    /**
     * Force-crawl ALL links regardless of crawl history.
     */
    public function crawlAll()
    {
        $links = Link::all();
        $count = 0;

        foreach ($links as $link) {
            CrawlLinkJob::dispatch($link->id);
            $count++;
        }

        return redirect()->route('admin.crawler.index')
            ->with('success', "✓ Force-crawl dispatched for ALL {$count} link(s).");
    }

    /**
     * Force-crawl a single specific link.
     */
    public function crawlSingle(int $id)
    {
        $link = Link::findOrFail($id);

        $link->update(['force_recrawl' => true]);
        CrawlLinkJob::dispatch($link->id);

        return redirect()->route('admin.crawler.index')
            ->with('success', "✓ Force-crawl dispatched for: {$link->url}");
    }

    /**
     * Reset force_recrawl flag for a single link.
     */
    public function resetForce(int $id)
    {
        Link::findOrFail($id)->update(['force_recrawl' => false]);

        return redirect()->route('admin.crawler.index')
            ->with('success', 'Force-recrawl flag cleared.');
    }

    /**
     * Show all discovered links for a given parent link.
     */
    public function discoveredLinks(int $id): View
    {
        $link      = Link::findOrFail($id);
        $discovered = DiscoveredLink::where('parent_url_id', $id)
            ->latest()
            ->paginate(50);

        return view('admin.crawler.discovered', compact('link', 'discovered'));
    }

    /**
     * Delete all discovered links for a parent.
     */
    public function clearDiscovered(int $id)
    {
        $link = Link::findOrFail($id);
        DiscoveredLink::where('parent_url_id', $id)->delete();

        return redirect()->route('admin.crawler.discovered', $id)
            ->with('success', "Cleared all discovered links for: {$link->url}");
    }

    /**
     * Show crawl logs for a specific link.
     */
    public function crawlLogs(int $id): View
    {
        $link = Link::findOrFail($id);
        $logs = CrawlLog::where('link_id', $id)
            ->latest()
            ->paginate(50);

        // Content preview
        $content = CrawlContent::where('link_id', $id)->first();

        return view('admin.crawler.logs', compact('link', 'logs', 'content'));
    }

    /**
     * Show all crawl logs (global activity).
     */
    public function allLogs(Request $request): View
    {
        $statusFilter = $request->get('status', 'all');

        $query = CrawlLog::with('link')->latest();

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Aggregate stats
        $logStats = [
            'total'    => CrawlLog::count(),
            'success'  => CrawlLog::where('status', 'success')->count(),
            'failed'   => CrawlLog::where('status', 'failed')->count(),
            'skipped'  => CrawlLog::where('status', 'skipped')->count(),
            'timeout'  => CrawlLog::where('status', 'timeout')->count(),
        ];

        return view('admin.crawler.all-logs', compact('logs', 'logStats', 'statusFilter'));
    }
}
