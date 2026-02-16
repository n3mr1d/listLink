<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    public function indexRegister(): View
    {
        return view('register');
    }
    public function storeLogin(LoginRequest $request)
    {
        $validate = $request->validated();
        // check validate user
        if (Auth::attempt(['username' => $validate['username'], 'password' => $validate['password']])) {
            return redirect()->route('welcome.user')->with('success', 'Login successfully');
        } else {
            return redirect()->back()->withErrors(['login' => 'Username or Password is incorrect.']);
        }
    }
    public function login()
    {
        return view('login');
    }
}
