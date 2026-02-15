<?php

namespace App\Http\Controllers;

use App\Enum\Category;
use App\Http\Requests\AddLinksRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
        $status = $this->checkOnline($validate['url']);
        if ($status == "200") {
            // get user information if user is logged in
            if (Auth::check()) {
                $user = Auth::user();
                $this->storeDatabase($validate, $user);
                return redirect()->back()->with('success', 'Link added successfully and status online');

            } else {
                $this->storeDatabase($validate);
                return redirect()->back()->with('success', 'Link added successfully and status online');

            }
        } else {
            return redirect()->route('links.index')->withErrors('Link is not working or invalid ');
        }

    }


    // input to database
    private function storeDatabase($validate, $user = null)
    {

        // store to database
        $link = Link::create([
            'title' => $validate['title'],
            'description' => $validate['description'],
            'url' => $validate['url'],
            'slug' => Str::slug($validate['title']),
            'category' => $validate['category'],
            'user_id' => $user->id ?? null,
            'status' => 'active',
            'last_check' => now(),
        ]);



    }
    private function checkOnline(string $url): string
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
