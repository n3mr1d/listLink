<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function show(string $slug): View
    {
        $link = Link::where('slug', $slug)
            ->where('status', 'active')
            ->with(['comments', 'user'])
            ->firstOrFail();

        // Fetch ads for detail page
        $headerAds = $this->randomAds(
            \App\Models\Advertisement::active()->byPlacement(\App\Enum\AdPlacement::HEADER)
        );

        $sidebarAds = $this->randomAds(
            \App\Models\Advertisement::active()->byPlacement(\App\Enum\AdPlacement::SIDEBAR)
        );

        // Generate challenge for comment form
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['comment_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('link-detail', compact('link', 'challenge', 'headerAds', 'sidebarAds'));
    }

    /**
     * Fetch ads in a random order without using ORDER BY RAND().
     */
    private function randomAds($query): \Illuminate\Database\Eloquent\Collection
    {
        $ids = (clone $query)->pluck('id')->toArray();

        if (empty($ids)) {
            return \Illuminate\Database\Eloquent\Collection::make();
        }

        shuffle($ids);

        // Fetch records and preserve the shuffled order
        $records = \App\Models\Advertisement::whereIn('id', $ids)->get()->keyBy('id');

        return \Illuminate\Database\Eloquent\Collection::make(
            array_map(fn($id) => $records[$id], $ids)
        );
    }

    public function storeComment(Request $request, int $id)
    {
        $link = Link::where('status', 'active')->findOrFail($id);

        $request->validate([
            'username' => 'nullable|string|max:50',
            'content' => 'required|string|min:3|max:1000',
            'challenge' => 'required',
        ]);

        // Validate challenge
        if ((int) $request->challenge !== session('comment_challenge_answer')) {
            return redirect()->route('link.show', $link->slug)
                ->withErrors(['challenge' => 'Incorrect answer to security question.'])
                ->withInput();
        }

        Comment::create([
            'link_id' => $link->id,
            'username' => $request->input('username') ?: 'Anonymous',
            'content' => strip_tags($request->input('content')),
        ]);

        return redirect()->route('link.show', $link->slug)
            ->with('success', 'Comment posted successfully.');
    }

    public function random()
    {
        $link = Link::active()->online()->inRandomOrder()->first();

        if (!$link) {
            return redirect()->route('home')->with('error', 'No active links found.');
        }

        return redirect()->route('link.show', $link->slug);
    }
}
