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

        return view('category', compact('category', 'links', 'categories'));
    }
}
