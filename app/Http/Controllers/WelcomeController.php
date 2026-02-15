<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Link;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(): View
    {
        $links = Link::where('status', 'active')->where('user_id', '!=', null)->get();
        return view('welcome', compact('links'));
    }
    public function store(RegisterRequest $request)
    {
        // validate request and store to databases
        $validate = $request->validated();
        $user = User::create([
            'username' => $validate['username'],
            'password' => $validate['password'],
        ]);
        return redirect()->route('login.index')->with('success', 'User created successfully');
    }
}
