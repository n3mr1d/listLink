<?php

namespace App\Http\Controllers;

use App\Enum\AdPackage;
use App\Enum\AdPlacement;
use App\Enum\AdType;
use App\Mail\AdvertiseSubmittedAdminMail;
use App\Mail\AdvertiseSubmittedUserMail;
use App\Models\Advertisement;
use App\Services\AdBannerCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $totalImpressions = \App\Models\AdStat::sum('impressions');
        $totalClicks = \App\Models\AdStat::sum('clicks');

        // Fetch top 3 performing ads (Active)
        $activeAds = \App\Models\Advertisement::active()
            ->withSum('stats as total_clicks', 'clicks')
            ->withSum('stats as total_impressions', 'impressions')
            ->orderByRaw('(COALESCE(total_clicks, 0) + COALESCE(total_impressions, 0)) DESC')
            ->take(3)
            ->get();

        $totalActiveAdsCount = \App\Models\Advertisement::active()->count();

        // Group packages for the view
        $packageGroups = [
            'Header Banners' => array_filter($packages, fn($p) => in_array($p->value, ['basic', 'standard', 'premium'])),
            'Sponsored Text Links' => array_filter($packages, fn($p) => in_array($p->value, ['sponsored_14', 'sponsored_30'])),
            'Sidebar Banners' => array_filter($packages, fn($p) => in_array($p->value, ['sidebar_14', 'sidebar_30'])),
        ];

        // Top Ads by Clicks (All time/Total)
        $topAdsByClicks = \App\Models\Advertisement::select('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->join('ad_stats', 'advertisements.id', '=', 'ad_stats.advertisement_id')
            ->selectRaw('SUM(ad_stats.clicks) as total_clicks')
            ->groupBy('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->orderByDesc('total_clicks')
            ->limit(5)
            ->get();

        // Top Ads by Views (Impressions)
        $topAdsByViews = \App\Models\Advertisement::select('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->join('ad_stats', 'advertisements.id', '=', 'ad_stats.advertisement_id')
            ->selectRaw('SUM(ad_stats.impressions) as total_views')
            ->groupBy('advertisements.id', 'advertisements.title', 'advertisements.url')
            ->orderByDesc('total_views')
            ->limit(5)
            ->get();

        return view('advertise', compact(
            'adTypes',
            'placements',
            'packages',
            'packageGroups',
            'challenge',
            'ad',
            'totalImpressions',
            'totalClicks',
            'activeAds',
            'totalActiveAdsCount',
            'topAdsByClicks',
            'topAdsByViews'
        ));
    }

    public function update(Request $request, int $id)
    {
        $ad = Advertisement::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'url' => ['required', 'string'],
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
            'title'        => 'required|string|min:3|max:100',
            'description'  => 'nullable|string|max:500',
            'url'          => ['required', 'string'],
            'ad_type'      => 'required|string',
            'placement'    => 'required|string',
            'package_tier' => 'nullable|string',
            'contact_info' => 'required|string|max:255',
            'banner'       => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
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
            // Auto-resize to 670×76 px and compress (WebP/JPEG)
            $bannerPath = AdBannerCompressor::process($request->file('banner'));
        }

        // Resolve price from selected package
        $priceUsd = null;
        if ($request->package_tier && $pkg = AdPackage::tryFrom($request->package_tier)) {
            $priceUsd = $pkg->priceUsd();
        }

        $ad = Advertisement::create([
            'user_id'        => auth()->id(),
            'title'          => strip_tags($request->title),
            'description'    => $request->description ? strip_tags($request->description) : null,
            'url'            => $request->url,
            'banner_path'    => $bannerPath,
            'ad_type'        => $request->ad_type,
            'placement'      => $request->placement,
            'package_tier'   => $request->package_tier ?? null,
            'price_usd'      => $priceUsd,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'contact_info'   => strip_tags($request->contact_info),
        ]);

        // ── Email Notifications ─────────────────────────────────────────────
        try {
            // 1. Notify admin
            $adminEmail = config('site.admin_email');
            if ($adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($adminEmail)->queue(new AdvertiseSubmittedAdminMail($ad));
            }

            // 2. Confirm to advertiser if contact_info looks like an email
            $userEmail = $ad->contact_info;
            if ($userEmail && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($userEmail)->queue(new AdvertiseSubmittedUserMail($ad));
            }
        } catch (\Throwable $e) {
            // Never let mail failure break the user flow
            Log::error('AdvertiseController: failed to send notification emails', [
                'ad_id' => $ad->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Redirect to Bitcoin payment gateway
        return redirect()->route('payment.show', $ad->id)
            ->with('info', 'Ad submitted! Please complete your Bitcoin payment below.');
    }
}
