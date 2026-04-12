<?php

namespace App\Http\Controllers;

use App\Enum\AdPlacement;
use App\Enum\AdType;
use App\Enum\TicketStatus;
use App\Models\Advertisement;
use App\Models\BlacklistedUrl;
use App\Models\Link;
use App\Models\SupportTicket;
use App\Models\UptimeLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    // ─── Dashboard ───
    public function dashboard(): View
    {
        $stats = [
            'total_links' => Link::count(),
            'active_links' => Link::active()->count(),
            'registered_links' => Link::active()->whereNotNull('user_id')->count(),
            'anonymous_links' => Link::active()->whereNull('user_id')->count(),
            'total_users' => User::count(),
            'pending_ads' => Advertisement::where('status', 'pending')->count(),
            'recent_checks' => UptimeLog::where('checked_at', '>=', now()->subDay())->count(),
        ];

        $recentLinks = Link::latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentLinks'));
    }

    // ─── Links Management (Admin can only delete, no approve/reject) ───
    public function links(Request $request): View
    {
        $filter = $request->get('filter', 'all');
        $query = Link::with('user')->latest();

        if ($filter === 'registered') {
            $query->whereNotNull('user_id');
        } elseif ($filter === 'anonymous') {
            $query->whereNull('user_id');
        }

        $links = $query->paginate(20)->withQueryString();

        return view('admin.links', compact('links', 'filter'));
    }

    public function deleteLink(int $id)
    {
        $link = Link::findOrFail($id);
        $link->delete();

        return redirect()->route('admin.links')
            ->with('success', "Link \"{$link->title}\" deleted.");
    }

    public function editLink(int $id)
    {
        $link = Link::findOrFail($id);
        $categories = \App\Enum\Category::cases();
        $uptimeStatuses = \App\Enum\UptimeStatus::cases();
        return view('admin.links-form', compact('link', 'categories', 'uptimeStatuses'));
    }

    public function updateLink(Request $request, int $id)
    {
        $link = Link::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'uptime_status' => 'required',
            'category' => 'required',
        ]);

        $link->update($request->only(['title', 'description', 'uptime_status', 'category']));

        return redirect()->route('admin.links')
            ->with('success', "Node updated successfully.");
    }

    // ─── Ads Management ───
    public function ads(Request $request): View
    {
        $filter = $request->get('filter', 'pending');
        $query = Advertisement::latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $ads = $query->paginate(20)->withQueryString();

        return view('admin.ads', compact('ads', 'filter'));
    }

    public function approveAd(Request $request, int $id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addWeek(),
        ]);

        return redirect()->route('admin.ads')
            ->with('success', "Ad \"{$ad->title}\" approved and activated for 1 week.");
    }

    public function rejectAd(int $id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->update(['status' => 'rejected']);

        return redirect()->route('admin.ads')
            ->with('success', "Ad \"{$ad->title}\" rejected.");
    }

    public function createAd(): View
    {
        $adTypes = AdType::cases();
        $placements = AdPlacement::cases();
        return view('admin.ads-form', compact('adTypes', 'placements'));
    }

    public function storeAd(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'url' => 'required|string|max:255',
            'ad_type' => 'required|string',
            'placement' => 'required|string',
            'status' => 'required|string',
            'contact_info' => 'nullable|string|max:255',
            'banner' => 'nullable|image|max:1024',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        $data = $request->only(['title', 'description', 'url', 'ad_type', 'placement', 'status', 'contact_info', 'starts_at', 'expires_at']);

        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('ads', 'public');
        }

        Advertisement::create($data);

        return redirect()->route('admin.ads')->with('success', 'Ad created successfully.');
    }

    public function editAd(int $id): View
    {
        $ad = Advertisement::findOrFail($id);
        $adTypes = AdType::cases();
        $placements = AdPlacement::cases();
        return view('admin.ads-form', compact('ad', 'adTypes', 'placements'));
    }

    public function updateAd(Request $request, int $id)
    {
        $ad = Advertisement::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'url' => 'required|string|max:255',
            'ad_type' => 'required|string',
            'placement' => 'required|string',
            'status' => 'required|string',
            'contact_info' => 'nullable|string|max:255',
            'banner' => 'nullable|image|max:1024',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        $data = $request->only(['title', 'description', 'url', 'ad_type', 'placement', 'status', 'contact_info', 'starts_at', 'expires_at']);

        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('ads', 'public');
        }

        $ad->update($data);

        return redirect()->route('admin.ads')->with('success', 'Ad updated successfully.');
    }

    public function deleteAd(int $id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->delete();

        return redirect()->route('admin.ads')->with('success', 'Ad deleted successfully.');
    }

    // ─── Uptime Logs ───
    public function uptimeLogs(): View
    {
        $logs = UptimeLog::with('link')
            ->latest('checked_at')
            ->paginate(50);

        return view('admin.uptime-logs', compact('logs'));
    }

    // ─── Blacklist Management ───
    public function blacklist(): View
    {
        $entries = BlacklistedUrl::latest()->paginate(20);
        return view('admin.blacklist', compact('entries'));
    }

    public function addBlacklist(Request $request)
    {
        $request->validate([
            'url_pattern' => 'required|string|max:255',
            'reason' => 'nullable|string|max:500',
        ]);

        BlacklistedUrl::create([
            'url_pattern' => $request->url_pattern,
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.blacklist')
            ->with('success', 'URL pattern added to blacklist.');
    }

    public function removeBlacklist(int $id)
    {
        BlacklistedUrl::findOrFail($id)->delete();

        return redirect()->route('admin.blacklist')
            ->with('success', 'Entry removed from blacklist.');
    }

    public function cleanupDuplicates()
    {
        // Find URLs that appear more than once
        $duplicates = Link::select('url')
            ->groupBy('url')
            ->havingRaw('COUNT(url) > 1')
            ->get();

        $count = 0;
        foreach ($duplicates as $duplicate) {
            // Keep the oldest link (lowest ID)
            $links = Link::where('url', $duplicate->url)
                ->orderBy('id', 'asc')
                ->get();

            $keep = $links->shift(); // Remove first element from collection and keep it
            
            foreach ($links as $link) {
                $link->delete();
                $count++;
            }
        }

        return redirect()->route('admin.links')
            ->with('success', "Cleanup complete. Removed {$count} duplicate link(s).");
    }

    public function enrichMetadata(int $id)
    {
        $link = Link::findOrFail($id);
        
        // Mark as processing
        $link->update(['crawl_queue_status' => 'processing']);
        
        // Run crawler synchronously for instant admin feedback
        try {
            (new \App\Jobs\CrawlLinkJob($link->id))->handle();
            
            return redirect()->back()
                ->with('success', "Metadata for \"{$link->title}\" has been refreshed successfully!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "Crawler failed: " . $e->getMessage());
        }
    }

    public function bulkEnrichMetadata()
    {
        $lowQualityTitles = ['Onion Link', 'New Link', 'Untitled', 'No Title'];
        $lowQualityDescriptions = ['No description provided.', 'No description', '...'];

        $links = Link::where(function($q) use ($lowQualityTitles, $lowQualityDescriptions) {
            $q->whereNull('title')
              ->orWhere('title', '')
              ->orWhereIn('title', $lowQualityTitles)
              ->orWhereRaw('LENGTH(title) < 5')
              ->orWhereNull('description')
              ->orWhere('description', '')
              ->orWhereIn('description', $lowQualityDescriptions)
              ->orWhereRaw('LENGTH(description) < 10');
        })->get();

        $count = 0;
        foreach ($links as $link) {
            $link->update([
                'crawl_queue_status' => 'queued',
                'queued_at' => now(),
            ]);
            \App\Jobs\CrawlLinkJob::dispatch($link->id);
            $count++;
        }

        return redirect()->route('admin.links')
            ->with('success', "Dispatched {$count} metadata enrichment jobs.");
    }
}
