<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Http\Requests\AddLinksRequest;
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
            'crawl_url' => 'required|string|regex:/^https?:\/\/[a-z2-7]{16,56}\.onion(\/.*)?$/i',
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

        // Cooldown check: 1 submission per 2 minutes per session
        $lastSubmit = session('last_link_submit');
        if ($lastSubmit && now()->diffInSeconds($lastSubmit) < 120) {
            $remaining = 120 - now()->diffInSeconds($lastSubmit);
            return redirect()->route('submit.create')
                ->withErrors(['cooldown' => "Please wait {$remaining} seconds before submitting again."])
                ->withInput();
        }

        // Generate unique slug
        $slug = Str::slug($validated['title']);
        $existingSlug = Link::where('slug', $slug)->exists();
        if ($existingSlug) {
            $slug .= '-' . Str::random(5);
        }

        // Auto-crawl: try to get title and description from the .onion URL
        $crawledTitle = null;
        $crawledDescription = null;

        try {
            $response = Http::withOptions([
                'proxy' => 'socks5h://127.0.0.1:9050',
                'timeout' => 15,
                'connect_timeout' => 10,
                'verify' => false,
            ])->get($validated['url']);

            if ($response->successful()) {
                $html = $response->body();

                // Extract <title>
                if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $matches)) {
                    $crawledTitle = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $crawledTitle = Str::limit($crawledTitle, 100, '');
                }

                // Extract meta description
                if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)["\'][^>]*>/si', $html, $matches)) {
                    $crawledDescription = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $crawledDescription = Str::limit($crawledDescription, 500, '');
                } elseif (preg_match('/<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']description["\'][^>]*>/si', $html, $matches)) {
                    $crawledDescription = trim(strip_tags(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8')));
                    $crawledDescription = Str::limit($crawledDescription, 500, '');
                }
            }
        } catch (\Exception $e) {
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

        // All submissions are auto-active — no pending/accept system
        // Anonymous (no user_id) → only appears in search engine
        // Logged-in (has user_id) → appears in both home directory + search engine
        Link::create([
            'title' => $title,
            'description' => $description,
            'url' => $validated['url'],
            'slug' => $slug,
            'category' => $validated['category'],
            'user_id' => Auth::id(),
            'status' => 'active',
            'uptime_status' => 'unknown',
        ]);

        session(['last_link_submit' => now()]);

        $message = Auth::check()
            ? 'Your link has been published! It will appear in the Tor Directory and Search Engine.'
            : 'Your link has been published! It will appear in the Search Engine. Log in and submit to also appear in the Tor Directory.';

        return redirect()->route('submit.create')
            ->with('success', $message);
    }
}
