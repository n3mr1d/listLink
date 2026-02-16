<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (Auth::attempt(['username' => $validated['username'], 'password' => $validated['password']])) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home')->with('success', 'Logged in successfully.');
        }

        return redirect()->back()
            ->withErrors(['login' => 'Invalid username or password.'])
            ->withInput(['username' => $validated['username']]);
    }

    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        return redirect()->route('login.form')
            ->with('success', 'Account created successfully. Please log in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }
}
