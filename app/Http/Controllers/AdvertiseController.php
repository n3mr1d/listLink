<?php

namespace App\Http\Controllers;

use App\Enum\AdPackage;
use App\Enum\AdPlacement;
use App\Enum\AdType;
use App\Models\Advertisement;
use App\Rules\UrlFilter;
use App\Services\AdBannerCompressor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertiseController extends Controller
{
    public function create(Request $request): View
    {
        $ad = null;
        if ($request->has('edit')) {
            $ad = Advertisement::where('user_id', auth()->id())->findOrFail($request->edit);
        }

        $adTypes = AdType::cases();
        $placements = AdPlacement::cases();
        $packages = AdPackage::cases();

        // Generate challenge
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['ad_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('advertise', compact('adTypes', 'placements', 'packages', 'challenge', 'ad'));
    }

    public function update(Request $request, int $id)
    {
        $ad = Advertisement::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'url' => ['required', 'string', new UrlFilter()],
            'challenge' => 'required',
        ]);

        // Validate challenge
        if ((int) $request->challenge !== session('ad_challenge_answer')) {
            return redirect()->back()
                ->withErrors(['challenge' => 'Incorrect answer to security question.'])
                ->withInput();
        }

        $ad->update([
            'url' => $request->url,
        ]);

        return redirect()->route('dashboard.ads')
            ->with('success', 'Ad destination updated successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:500',
            'url' => ['required', 'string'],
            'ad_type' => 'required|string',
            'placement' => 'required|string',
            'package_tier' => 'nullable|string',
            'contact_info' => 'required|string|max:255',
            'banner' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
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
            // Auto-resize to 670×76 px and compress (WebP/JPEG)
            $bannerPath = AdBannerCompressor::process($request->file('banner'));
        }

        // Resolve price from selected package
        $priceUsd = null;
        if ($request->package_tier && $pkg = AdPackage::tryFrom($request->package_tier)) {
            $priceUsd = $pkg->priceUsd();
        }

        $ad = Advertisement::create([
            'user_id' => auth()->id(),
            'title' => strip_tags($request->title),
            'description' => $request->description ? strip_tags($request->description) : null,
            'url' => $request->url,
            'banner_path' => $bannerPath,
            'ad_type' => $request->ad_type,
            'placement' => $request->placement,
            'package_tier' => $request->package_tier ?? null,
            'price_usd' => $priceUsd,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'contact_info' => strip_tags($request->contact_info),
        ]);

        // Redirect to Bitcoin payment gateway
        return redirect()->route('payment.show', $ad->id)
            ->with('info', 'Ad submitted! Please complete your Bitcoin payment below.');
    }
}
