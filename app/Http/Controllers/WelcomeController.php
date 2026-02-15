<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $links = Link::where('status', 'active')->where('user_id', '!=', null)->get();
        return view('welcome', compact('links'));
    }
}
