<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $links = $user->links()->latest()->paginate(10, ['*'], 'links_page');
        $ads = \App\Models\Advertisement::where('user_id', $user->id)->latest()->paginate(10, ['*'], 'ads_page');

        return view('user.dashboard', compact('links', 'ads'));
    }
}
