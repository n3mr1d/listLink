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

        // Generate challenge for comment form
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['comment_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('link-detail', compact('link', 'challenge'));
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
            'username' => $request->username ?: 'Anonymous',
            'content' => strip_tags($request->content),
        ]);

        return redirect()->route('link.show', $link->slug)
            ->with('success', 'Comment posted successfully.');
    }
}
