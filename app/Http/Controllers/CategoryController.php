<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Models\Link;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $slug): View
    {
        // Find the category enum by slug value
        $category = null;
        foreach (Category::cases() as $cat) {
            if ($cat->value === $slug) {
                $category = $cat;
                break;
            }
        }

        if (!$category) {
            abort(404, 'Category not found.');
        }

        $links = Link::active()
            ->byCategory($category)
            ->with('user')
            ->latest()
            ->paginate(20);

        $categories = Category::cases();

        // Fetch ads for category page
        $headerAds = $this->randomAds(
            \App\Models\Advertisement::active()->byPlacement(\App\Enum\AdPlacement::HEADER)
        );

        $sidebarAds = $this->randomAds(
            \App\Models\Advertisement::active()->byPlacement(\App\Enum\AdPlacement::SIDEBAR)
        );

        return view('category', compact('category', 'links', 'categories', 'headerAds', 'sidebarAds'));
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
}
