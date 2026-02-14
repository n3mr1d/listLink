<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Http\Requests\AddLinksRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use PHPUnit\Exception;

class AddLinkController extends Controller
{
    // show index page
    public function index()
    {
        $categories = Category::cases();
        return view('links.index', compact('categories'));
    }
    // save link to database

    public function store(AddLinksRequest $request)
    {
        $validate = $request->validated();
        // check link user is online or not


    }


    // input to database
    private function storeDatabase($validate, $user = null)
    {
        // store to database
        Link::creating([
            'title' => $validate['title'],
            'description' => $validate['description'],
            'url' => $validate['url'],
            'category' => $validate['category'],
            'user_id' => $user->id ?? null,
            'status' => 'active',
            'last_check' => now(),
        ]);

        return redirect()->route('links.index')->with('success', 'Link added successfully');


    }
    private function checkOnline($url): bool|string
    {
        try {
            $response = Http::withOptions([
                'proxy' => 'socks5h://127.0.0.1:9050',
                'timeout' => 15,
            ])->head($url);

            return $response->status();

        } catch (\Exception $e) {
            return "error : " . $e->getMessage();
        }
    }
}
