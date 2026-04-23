<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Login
    // ─────────────────────────────────────────────────────────────

    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (Auth::attempt(['username' => $validated['username'], 'password' => $validated['password']])) {
            if (Auth::user()->status === 'banned') {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['login' => 'This account has been suspended by an administrator.'])
                    ->withInput(['username' => $validated['username']]);
            }

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

    // ─────────────────────────────────────────────────────────────
    // Register
    // ─────────────────────────────────────────────────────────────

    public function registerForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'email' => $validated['email'],
            'role' => 'user',
        ]);

        $this->sendVerificationEmail($user, $request);
        return redirect()->route('verify.notice', ['userId' => $user->id])
            ->with('success', 'Account created! Please check your email to verify your address.');
    }

    // ─────────────────────────────────────────────────────────────
    // Email Verification
    // ─────────────────────────────────────────────────────────────

    /** Show "check your email" notice */
    public function verifyNotice(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);
        return view('auth.verify-notice', compact('user'));
    }

    /** Handle the link-click token from the email */
    public function verifyEmail(Request $request, string $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login.form')
                ->withErrors(['verify' => 'Invalid or expired verification link.']);
        }

        $this->markEmailVerified($user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('welcome.register');
    }

    /** Handle 6-digit code submission */
    public function verifyCode(Request $request, int $userId)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($userId);

        if ($user->email_verification_code !== strtoupper($request->code)) {
            return redirect()->back()
                ->withErrors(['code' => 'The verification code is incorrect.'])
                ->withInput();
        }

        $this->markEmailVerified($user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('welcome.register');
    }

    /** Resend verification email */
    public function resendVerification(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Throttle: 1 resend per 60 seconds
        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInSeconds(now()) < 60) {
            return redirect()->back()
                ->withErrors(['resend' => 'Please wait before requesting another verification email.']);
        }

        $this->sendVerificationEmail($user, $request);

        return redirect()->route('verify.notice', ['userId' => $user->id])
            ->with('success', 'Verification email resent. Check your inbox.');
    }

    // ─────────────────────────────────────────────────────────────
    // Logout
    // ─────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    // ─────────────────────────────────────────────────────────────
    // Internal helpers
    // ─────────────────────────────────────────────────────────────

    private function sendVerificationEmail(User $user, Request $request): void
    {
        $token = Str::random(64);
        $code = strtoupper(Str::random(6)); // 6-char alphanumeric code

        $user->update([
            'email_verification_token' => $token,
            'email_verification_code' => $code,
            'email_verification_sent_at' => now(),
            'email_verified_at' => null,
        ]);

        // Detect Tor vs Clearnet
        $isOnion = $this->isOnionRequest($request);
        $baseUrl = $isOnion ? config('app.url') : (config('app.clearnet_url') ?: config('app.url'));
        $verifyUrl = rtrim($baseUrl, '/') . route('verify.email', ['token' => $token], false);

        Mail::to($user->email)->send(new EmailVerificationMail(
            $user->username,
            $verifyUrl,
            $code,
            $isOnion
        ));
    }

    private function markEmailVerified(User $user): void
    {
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_code' => null,
            'email_verification_sent_at' => null,
        ]);
    }

    private function isOnionRequest(Request $request): bool
    {
        $host = $request->getHost();
        $clearnetHost = parse_url(config('site.clearnet_url'), PHP_URL_HOST);

        return $host !== $clearnetHost || str_ends_with($host, '.onion');
    }
}
