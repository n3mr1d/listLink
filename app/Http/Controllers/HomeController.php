<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Enum\AdPlacement;
use App\Models\Advertisement;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        // Only show links from registered users (user_id is NOT null)
        $links = Link::active()
            ->whereNotNull('user_id')
            ->with('user')
            ->latest()
            ->paginate(20);

        $categories = Category::cases();

        // Get ALL active ads for each placement
        $headerAds = Advertisement::active()
            ->byPlacement(AdPlacement::HEADER)
            ->inRandomOrder()
            ->get();

        $sidebarAds = Advertisement::active()
            ->byPlacement(AdPlacement::SIDEBAR)
            ->inRandomOrder()
            ->get();

        $sponsoredLinks = Advertisement::active()
            ->where('ad_type', 'sponsored')
            ->inRandomOrder()
            ->get();

        // Stats — only count links from registered users for homepage context
        $stats = [
            'total_links' => Link::active()->whereNotNull('user_id')->count(),
            'online_links' => Link::active()->whereNotNull('user_id')->where('uptime_status', 'online')->count(),
            'categories' => count($categories),
        ];

        return view('home', compact(
            'links',
            'categories',
            'headerAds',
            'sidebarAds',
            'sponsoredLinks',
            'stats'
        ));
    }
}
