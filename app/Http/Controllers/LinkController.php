<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Comment;
use App\Models\Like;
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
        $reportLabel = Report::cases();
        $sidebarAds = $this->randomAds(
            \App\Models\Advertisement::active()->byPlacement(\App\Enum\AdPlacement::SIDEBAR)
        );

        // Generate challenge for comment form
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['comment_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('link-detail', compact('link', 'challenge', 'headerAds', 'sidebarAds','reportLabel'));
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

        $username = $request->input('username');
        if (empty($username)) {
            $username = $this->generateAnonymousName();
        }

        Comment::create([
            'link_id' => $link->id,
            'username' => $username,
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

    public function vote(Request $request, int $id)
    {
        $link = Link::where('status', 'active')->findOrFail($id);
        $isDislike = $request->routeIs('link.dislike');

        // Generate fingerprint for anti-spam
        $fingerprint = hash('sha256', $request->ip() . $request->userAgent() . $request->header('Accept-Language'));

        // Check if user already voted (logged in or same fingerprint)
        $existingVote = Like::where('link_id', $link->id)
            ->where(function ($q) use ($fingerprint) {
                if (auth()->check()) {
                    $q->where('user_id', auth()->id())
                      ->orWhere('fingerprint', $fingerprint);
                } else {
                    $q->where('fingerprint', $fingerprint);
                }
            })
            ->first();

        if ($existingVote) {
            // Check if they are toggling the same vote
            if ($existingVote->is_dislike == $isDislike) {
                return redirect()->back()->with('info', 'You have already voted on this link.');
            }

            // They are changing their vote (like to dislike or vice versa)
            // Update counts on link
            if ($isDislike) {
                $link->decrement('likes_count');
                $link->increment('dislikes_count');
            } else {
                $link->increment('likes_count');
                $link->decrement('dislikes_count');
            }

            $existingVote->update(['is_dislike' => $isDislike]);
        } else {
            // New vote
            Like::create([
                'link_id' => $link->id,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'fingerprint' => $fingerprint,
                'is_dislike' => $isDislike,
            ]);

            if ($isDislike) {
                $link->increment('dislikes_count');
            } else {
                $link->increment('likes_count');
            }
        }

        $link->update(['last_voted_at' => now()]);

        return redirect()->back()->with('success', 'Thank you for your vote!');
    }

    private function generateAnonymousName(): string
    {
        $adjectives = [
            'Silent', 'Dark', 'Hidden', 'Brave', 'Shadow', 'Ghost', 'Crypto', 
            'Onion', 'Deep', 'Swift', 'Lone', 'Secret', 'Vivid', 'Echo', 
            'Void', 'Neo', 'Cyber', 'Frost', 'Iron', 'Golden', 'Alpha'
        ];
        $nouns = [
            'Agent', 'User', 'Node', 'Peer', 'Ghost', 'Runner', 'Vault', 
            'Pulse', 'Entity', 'Member', 'Specter', 'Watcher', 'Cipher', 
            'Protocol', 'Vector', 'Phantom', 'Oracle', 'Drifter', 'Signal'
        ];
        
        // Seed with session id + IP to keep it somewhat consistent per user session
        $seed = crc32(session()->getId() . request()->ip());
        srand($seed);
        
        $adj = $adjectives[array_rand($adjectives)];
        $noun = $nouns[array_rand($nouns)];
        $num = rand(100, 999);
        
        // Reset seed
        srand();
        
        return "{$adj}{$noun}_{$num}";
    }
}
