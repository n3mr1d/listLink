<?php

namespace App\Http\Controllers;

use App\Enum\AdPackage;
use App\Enum\AdPlacement;
use App\Enum\AdType;
use App\Models\Advertisement;
use App\Rules\UrlFilter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertiseController extends Controller
{
    public function create(): View
    {
        $adTypes    = AdType::cases();
        $placements = AdPlacement::cases();
        $packages   = AdPackage::cases();

        // Generate challenge
        $a = rand(1, 15);
        $b = rand(1, 15);
        session(['ad_challenge_answer' => $a + $b]);
        $challenge = "What is {$a} + {$b}?";

        return view('advertise', compact('adTypes', 'placements', 'packages', 'challenge'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|min:3|max:100',
            'description'  => 'nullable|string|max:500',
            'url'          => ['required', 'string', new UrlFilter()],
            'ad_type'      => 'required|string',
            'placement'    => 'required|string',
            'package_tier' => 'nullable|string',
            'contact_info' => 'required|string|max:255',
            'banner'       => 'nullable|image|mimes:png,jpg,gif,webp|max:512',
            'challenge'    => 'required',
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

        // Resolve price from selected package
        $priceUsd = null;
        if ($request->package_tier && $pkg = AdPackage::tryFrom($request->package_tier)) {
            $priceUsd = $pkg->priceUsd();
        }

        $ad = Advertisement::create([
            'user_id'      => auth()->id(),
            'title'        => strip_tags($request->title),
            'description'  => $request->description ? strip_tags($request->description) : null,
            'url'          => $request->url,
            'banner_path'  => $bannerPath,
            'ad_type'      => $request->ad_type,
            'placement'    => $request->placement,
            'package_tier' => $request->package_tier ?? null,
            'price_usd'    => $priceUsd,
            'status'       => 'pending',
            'payment_status' => 'unpaid',
            'contact_info' => strip_tags($request->contact_info),
        ]);

        // Redirect to Bitcoin payment gateway
        return redirect()->route('payment.show', $ad->id)
            ->with('info', 'Ad submitted! Please complete your Bitcoin payment below.');
    }
}
