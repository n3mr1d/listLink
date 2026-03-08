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

        // Avoid ORDER BY RAND() which causes MariaDB to write temporary disk files.
        // Instead: fetch IDs with a cheap indexed scan, shuffle in PHP, then fetch records.
        $headerAds = $this->randomAds(
            Advertisement::active()->byPlacement(AdPlacement::HEADER)
        );

        $sidebarAds = $this->randomAds(
            Advertisement::active()->byPlacement(AdPlacement::SIDEBAR)
        );

        $sponsoredLinks = $this->randomAds(
            Advertisement::active()->where('ad_type', 'sponsored')
        );

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

    /**
     * Fetch ads in a random order without using ORDER BY RAND().
     * Retrieves IDs via a lightweight indexed query, shuffles in PHP,
     * then fetches the full records — no temp disk files created in MariaDB.
     */
    private function randomAds($query): \Illuminate\Database\Eloquent\Collection
    {
        $ids = (clone $query)->pluck('id')->toArray();

        if (empty($ids)) {
            return \Illuminate\Database\Eloquent\Collection::make();
        }

        shuffle($ids);

        // Fetch records and preserve the shuffled order
        $records = Advertisement::whereIn('id', $ids)->get()->keyBy('id');

        return \Illuminate\Database\Eloquent\Collection::make(
            array_map(fn($id) => $records[$id], $ids)
        );
    }
}
