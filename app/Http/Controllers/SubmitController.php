<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Http\Requests\AddLinksRequest;
use App\Jobs\CrawlLinkJob;
use App\Models\Link;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubmitController extends Controller
{
    public function create(): View
    {
        $categories = Category::cases();

        // Generate math challenge
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['submit_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('submit', compact('categories', 'challenge'));
    }

    /**
     * Crawl a .onion URL via Tor and return title/description.
     * This is a standalone endpoint that pre-fills the submit form.
     */
    public function crawl(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'crawl_url' => 'required|string|regex:/^https?:\/\/([a-z0-9-]+\.)*[a-z2-7]{16,56}\.onion(\/.*)?$/i',
        ]);

        $url = $request->crawl_url;
        $title = null;
        $description = null;

        try {
            $response = Http::withOptions([
                'proxy' => 'socks5h://127.0.0.1:9050',
                'timeout' => 15,
                'connect_timeout' => 10,
                'verify' => false,
            ])->get($url);

            if ($response->successful()) {
                $html = $response->body();

                // Extract <title>
                if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $matches)) {
                    $title = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $title = Str::limit($title, 100, '');
                }

                // Extract meta description
                if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/si', $html, $matches)) {
                    $description = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $description = Str::limit($description, 500, '');
                } elseif (preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']description["\'][^>]*>/si', $html, $matches)) {
                    $description = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $description = Str::limit($description, 500, '');
                }

                session([
                    'crawl_result' => [
                        'title' => $title,
                        'description' => $description,
                    ],
                    'crawled_url' => $url,
                ]);

                return redirect()->route('submit.create')
                    ->with('crawl_result', [
                        'title' => $title,
                        'description' => $description,
                    ]);
            } else {
                session()->forget(['crawl_result', 'crawled_url']);
                return redirect()->route('submit.create')
                    ->with('crawl_error', 'Site returned HTTP ' . $response->status())
                    ->withInput();
            }
        } catch (\Exception $e) {
            session()->forget(['crawl_result', 'crawled_url']);
            return redirect()->route('submit.create')
                ->with('crawl_error', 'Could not connect via Tor — site may be offline or unreachable.')
                ->withInput();
        }
    }

    public function store(AddLinksRequest $request)
    {
        $validated = $request->validated();

        // Validate challenge answer
        if ((int) $validated['challenge'] !== session('submit_challenge_answer')) {
            return redirect()->route('submit.create')
                ->withErrors(['challenge' => 'Incorrect answer to security question.'])
                ->withInput();
        }

        // Check if the URL already exists. If it does, safely redirect to its page.
        $existingLink = Link::where('url', $validated['url'])->first();
        if ($existingLink) {
            return redirect()->route('link.show', $existingLink->slug)
                ->with('info', 'This network node has already been submitted to the directory.');
        }



        // Generate unique slug
        $slug = Str::slug($validated['title']);
        $existingSlug = Link::where('slug', $slug)->exists();
        if ($existingSlug) {
            $slug .= '-' . Str::random(5);
        }

        // ── Immediate online check (non-blocking, fast timeout) ──────────
        // Perform a quick HEAD/GET check to determine initial uptime status.
        // This gives instant feedback while the full crawl runs in the queue.
        $uptimeStatus = 'unknown';
        $crawledTitle = null;
        $crawledDescription = null;

        try {
            $response = Http::withOptions([
                'proxy' => 'socks5h://127.0.0.1:9050',
                'timeout' => 15,
                'connect_timeout' => 10,
                'verify' => false,
            ])->get($validated['url']);

            if ($response->successful() || in_array($response->status(), [403, 429, 502, 503])) {
                $uptimeStatus = 'online';
                $html = $response->body();

                // Extract <title> if successful (might be empty on 503)
                if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $matches)) {
                    $crawledTitle = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $crawledTitle = Str::limit($crawledTitle, 100, '');
                }

                // Extract meta description
                if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/si', $html, $matches)) {
                    $crawledDescription = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $crawledDescription = Str::limit($crawledDescription, 500, '');
                } elseif (preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]*name=["\']description["\'][^>]*>/si', $html, $matches)) {
                    $crawledDescription = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $crawledDescription = Str::limit($crawledDescription, 500, '');
                }
            } else {
                $uptimeStatus = 'offline';
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $uptimeStatus = 'timeout';
        } catch (\Exception $e) {
            $uptimeStatus = 'timeout';
            // Crawl failure is non-fatal — continue with user-provided data
        }

        // Use crawled data as fallback if user-provided data is empty/default
        $title = strip_tags($validated['title']);
        $description = strip_tags($validated['description'] ?? '');

        // If user left defaults and crawler found data, use crawled data
        if ($crawledTitle && (empty($title) || strlen($title) < 5)) {
            $title = $crawledTitle;
        }
        if ($crawledDescription && (empty($description) || $description === 'No description provided.')) {
            $description = $crawledDescription;
        }
        if (empty($description)) {
            $description = $crawledDescription ?? 'No description provided.';
        }

        // ── Create link with queue tracking fields ───────────────────────
        // All submissions are auto-active — no pending/accept system
        // Anonymous (no user_id) → only appears in search engine
        // Logged-in (has user_id) → appears in both home directory + search engine
        $link = Link::create([
            'title' => $title,
            'description' => $description,
            'url' => $validated['url'],
            'slug' => $slug,
            'category' => $validated['category'],
            'user_id' => Auth::id(),
            'status' => 'active',
            'uptime_status' => $uptimeStatus,
            'crawl_status' => 'pending',
            'crawl_queue_status' => 'queued',
            'queued_at' => now(),
        ]);

        // ── Auto-dispatch to crawler queue ───────────────────────────────
        // Immediately queue a full crawl job for this link.
        // The CrawlLinkJob handles: content extraction, link discovery,
        // metadata enrichment, and uptime verification.
        CrawlLinkJob::dispatch($link->id);

        session(['last_link_submit' => now()]);

        $message = Auth::check()
            ? 'Your link has been published and queued for crawling! It will appear in the Tor Directory and Search Engine.'
            : 'Your link has been published and queued for crawling! It will appear in the Search Engine. Log in and submit to also appear in the Tor Directory.';

        return redirect()->route('submit.create')
            ->with('success', $message);
    }
}
