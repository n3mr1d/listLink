<?php

namespace App\Http\Controllers;

use App\Jobs\EmailCrawlJob;
use App\Models\CrawledEmail;
use App\Models\DiscoveredLink;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmailCrawlerController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $search   = $request->get('q');
        $domain   = $request->get('domain');
        $status   = $request->get('status', 'all');
        $source   = $request->get('source', 'all');
        $exported = $request->get('exported', 'all');

        $query = CrawledEmail::latest('first_seen_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('source_domain', 'like', "%{$search}%")
                  ->orWhere('page_title', 'like', "%{$search}%");
            });
        }

        if ($domain) {
            $query->where('source_domain', $domain);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($source !== 'all') {
            $query->where('source_type', $source);
        }

        if ($exported === 'yes') {
            $query->where('exported', true);
        } elseif ($exported === 'no') {
            $query->where('exported', false);
        }

        $emails = $query->paginate(50)->withQueryString();

        $stats = [
            'total'       => CrawledEmail::count(),
            'active'      => CrawledEmail::where('status', 'active')->count(),
            'invalid'     => CrawledEmail::where('status', 'invalid')->count(),
            'exported'    => CrawledEmail::where('exported', true)->count(),
            'not_exported'=> CrawledEmail::where('exported', false)->count(),
            'auto_crawl'  => CrawledEmail::where('source_type', 'auto_crawl')->count(),
            'manual'      => CrawledEmail::where('source_type', 'manual')->count(),
            'domains'     => CrawledEmail::distinct('source_domain')->whereNotNull('source_domain')->count(),
            'today'       => CrawledEmail::where('first_seen_at', '>=', now()->startOfDay())->count(),
        ];

        $topDomains = CrawledEmail::selectRaw('source_domain, count(*) as cnt')
            ->whereNotNull('source_domain')
            ->groupBy('source_domain')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        return view('admin.email-crawler.index', compact(
            'emails', 'stats', 'topDomains',
            'search', 'domain', 'status', 'source', 'exported'
        ));
    }

    // ── Manual URL Scan ───────────────────────────────────────────────────

    public function scanUrl(Request $request)
    {
        $validated = $request->validate([
            'url'       => ['required', 'url', 'max:2048'],
            'use_proxy' => ['nullable', 'boolean'],
        ]);

        $url      = $validated['url'];
        $useProxy = (bool) ($validated['use_proxy'] ?? false);
        $jobId    = 'manual-' . Str::random(10);

        EmailCrawlJob::dispatch($url, $jobId, $useProxy);

        return redirect()->route('admin.email-crawler.index')
            ->with('success', "✓ Scan job queued for: {$url} (Job ID: {$jobId})");
    }

    // ── Bulk URL Scan ─────────────────────────────────────────────────────

    public function scanBulk(Request $request)
    {
        $validated = $request->validate([
            'urls'      => ['required', 'string'],
            'use_proxy' => ['nullable', 'boolean'],
        ]);

        $rawUrls  = $validated['urls'];
        $useProxy = (bool) ($validated['use_proxy'] ?? false);
        $jobId    = 'bulk-' . Str::random(10);

        $urls    = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $rawUrls)));
        $queued  = 0;
        $skipped = 0;

        foreach ($urls as $url) {
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                $skipped++;
                continue;
            }

            if (strlen($url) > 2048) {
                $skipped++;
                continue;
            }

            EmailCrawlJob::dispatch($url, $jobId, $useProxy);
            $queued++;
        }

        $msg = "✓ Queued {$queued} URL(s) for email scanning (Job ID: {$jobId}).";
        if ($skipped > 0) {
            $msg .= " {$skipped} invalid URL(s) skipped.";
        }

        return redirect()->route('admin.email-crawler.index')->with('success', $msg);
    }

    // ── Manual Add ────────────────────────────────────────────────────────

    public function manualAdd(Request $request)
    {
        $validated = $request->validate([
            'email'      => ['required', 'email', 'max:320'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ]);

        $email = strtolower(trim($validated['email']));

        if (! CrawledEmail::isValidEmail($email)) {
            return back()->withErrors(['email' => 'Email address failed validation (disposable or invalid format)']);
        }

        $result = CrawledEmail::upsertEmail(
            email:      $email,
            sourceUrl:  $validated['source_url'] ?? null,
            pageTitle:  $validated['notes'] ?? null,
            sourceType: 'manual',
            jobId:      null,
        );

        $msg = $result['created']
            ? "✓ Email '{$email}' added successfully."
            : "ℹ️ Email '{$email}' already exists — last_seen updated.";

        return redirect()->route('admin.email-crawler.index')->with('success', $msg);
    }

    // ── Export CSV ────────────────────────────────────────────────────────

    public function exportCsv(Request $request)
    {
        $status   = $request->get('status', 'active');
        $source   = $request->get('source', 'all');
        $exported = $request->get('exported', 'all');
        $domain   = $request->get('domain');
        $markDone = (bool) $request->get('mark_exported', true);

        $query = CrawledEmail::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($source !== 'all') {
            $query->where('source_type', $source);
        }
        if ($exported === 'no') {
            $query->where('exported', false);
        } elseif ($exported === 'yes') {
            $query->where('exported', true);
        }
        if ($domain) {
            $query->where('source_domain', $domain);
        }

        $emails = $query->orderBy('email')->get();

        if ($emails->isEmpty()) {
            return back()->with('error', 'No emails match the selected filters for export.');
        }

        // Mark as exported
        if ($markDone) {
            $query->update(['exported' => true]);
        }

        $filename = 'emails_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function () use ($emails) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($handle, [
                'Email', 'Source Domain', 'Source URL', 'Page Title',
                'Status', 'Source Type', 'First Seen', 'Last Seen',
            ]);

            foreach ($emails as $row) {
                fputcsv($handle, [
                    $row->email,
                    $row->source_domain,
                    $row->source_url,
                    $row->page_title,
                    $row->status,
                    $row->source_type,
                    $row->first_seen_at?->format('Y-m-d H:i:s'),
                    $row->last_seen_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        Log::info("[EmailCrawler] CSV export: {$emails->count()} emails exported.");

        return response()->stream($callback, 200, $headers);
    }

    // ── Update Email Status ───────────────────────────────────────────────

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,invalid,unsubscribed'],
        ]);

        $email = CrawledEmail::findOrFail($id);
        $email->update(['status' => $validated['status']]);

        return back()->with('success', "Status updated to '{$validated['status']}' for {$email->email}");
    }

    // ── Delete Single Email ───────────────────────────────────────────────

    public function destroy(int $id)
    {
        $email = CrawledEmail::findOrFail($id);
        $addr  = $email->email;
        $email->delete();

        return back()->with('success', "Deleted: {$addr}");
    }

    // ── Bulk Delete by Filters ────────────────────────────────────────────

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:active,invalid,unsubscribed'],
            'source' => ['nullable', 'in:auto_crawl,manual'],
        ]);

        $query = CrawledEmail::query();

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }
        if (! empty($validated['source'])) {
            $query->where('source_type', $validated['source']);
        }

        $count = $query->count();
        $query->delete();

        return redirect()->route('admin.email-crawler.index')
            ->with('success', "✓ Deleted {$count} email record(s).");
    }

    // ── Crawl from Database Links ─────────────────────────────────────────

    public function crawlFromDb(Request $request)
    {
        $validated = $request->validate([
            'source'    => ['required', 'in:links,discovered,both'],
            'limit'     => ['nullable', 'integer', 'min:1', 'max:5000'],
            'use_proxy' => ['nullable', 'boolean'],
        ]);

        $source   = $validated['source'];
        $limit    = (int) ($validated['limit'] ?? 500);
        $useProxy = (bool) ($validated['use_proxy'] ?? false);
        $jobId    = 'db-' . Str::random(10);

        $urls = collect();

        // Pull URLs from the main links table
        if (in_array($source, ['links', 'both'])) {
            $linkUrls = Link::whereNotNull('url')
                ->where('url', '!=', '')
                ->pluck('url');
            $urls = $urls->merge($linkUrls);
        }

        // Pull URLs from the discovered_links table
        if (in_array($source, ['discovered', 'both'])) {
            $discoveredUrls = DiscoveredLink::whereNotNull('url')
                ->where('url', '!=', '')
                ->pluck('url');
            $urls = $urls->merge($discoveredUrls);
        }

        // Deduplicate and limit
        $urls = $urls->unique()->values()->take($limit);

        $queued  = 0;
        $skipped = 0;

        foreach ($urls as $url) {
            // Only HTTP/HTTPS URLs — skip onion-only if no proxy
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                $skipped++;
                continue;
            }

            $isOnion = str_contains($url, '.onion');
            if ($isOnion && ! $useProxy) {
                $skipped++;
                continue;
            }

            EmailCrawlJob::dispatch($url, $jobId, $useProxy || $isOnion);
            $queued++;
        }

        $sourceLabel = match($source) {
            'links'      => 'Links table',
            'discovered' => 'Discovered Links table',
            'both'       => 'Links + Discovered Links tables',
        };

        $msg = "✓ Queued {$queued} URL(s) from {$sourceLabel} for email scanning (Job ID: {$jobId}).";
        if ($skipped > 0) {
            $msg .= " {$skipped} URL(s) skipped (invalid or .onion without proxy).";
        }

        Log::info("[EmailCrawler] crawlFromDb: {$queued} queued, {$skipped} skipped — source={$source}, jobId={$jobId}");

        return redirect()->route('admin.email-crawler.index')->with('success', $msg);
    }

    // ── Reset Export Flag ─────────────────────────────────────────────────

    public function resetExported()
    {
        $count = CrawledEmail::where('exported', true)->count();
        CrawledEmail::where('exported', true)->update(['exported' => false]);

        return redirect()->route('admin.email-crawler.index')
            ->with('success', "✓ Reset exported flag on {$count} email(s).");
    }
}
