<?php

namespace App\Http\Controllers;

use App\Enum\AdPlacement;
use App\Enum\AdType;
use App\Rules\OnionUrlRule;
use App\Models\Advertisement;
use App\Rules\UrlFilter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertiseController extends Controller
{
    public function create(): View
    {
        $adTypes = AdType::cases();
        $placements = AdPlacement::cases();

        // Generate challenge
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['ad_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('advertise', compact('adTypes', 'placements', 'challenge'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'url' => ['required', 'string', new UrlFilter()],
            'ad_type' => 'required|string',
            'placement' => 'required|string',
            'contact_info' => 'required|string|max:255',
            'banner' => 'nullable|image|mimes:png,jpg,gif,webp|max:512',
            'challenge' => 'required',
        ]);

        // Validate challenge
        if ((int) $request->challenge !== session('ad_challenge_answer')) {
            return redirect()->route('advertise.create')
                ->withErrors(['challenge' => 'Incorrect answer to security question.'])
                ->withInput();
        }

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('ads', 'public');
        }

        Advertisement::create([
            'user_id' => auth()->id(),
            'title' => strip_tags($request->title),
            'description' => $request->description ? strip_tags($request->description) : null,
            'url' => $request->url,
            'banner_path' => $bannerPath,
            'ad_type' => $request->ad_type,
            'placement' => $request->placement,
            'status' => 'pending',
            'contact_info' => strip_tags($request->contact_info),
        ]);

        return redirect()->route('advertise.create')
            ->with('success', 'Your ad has been submitted for review. We will contact you regarding payment and approval.');
    }
}
