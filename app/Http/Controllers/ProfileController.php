<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user control panel.
     */
    public function index(): View
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    /**
     * Update username.
     */
    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'min:3',
                'max:20',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username,' . Auth::id(),
            ],
        ]);

        Auth::user()->update(['username' => $request->username]);

        return redirect()->route('profile')->with('success', 'Username updated successfully.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => $request->password]);

        return redirect()->route('profile')->with('success', 'Password updated successfully.');
    }

    /**
     * Initiate email change request (sends verification to the new address).
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email:rfc',
                'max:191',
                'unique:users,email,' . Auth::id(),
            ],
        ]);

        $user = Auth::user();
        $newEmail = $request->email;

        // Store new email temporarily and send verification
        $token = Str::random(64);
        $code = strtoupper(Str::random(6));

        $user->update([
            'email' => $newEmail,
            'email_verified_at' => null,
            'email_verification_token' => $token,
            'email_verification_code' => $code,
            'email_verification_sent_at' => now(),
        ]);

        $isOnion = $this->isOnionRequest($request);
        $baseUrl = $isOnion ? config('site.onion_url') : config('site.clearnet_url');
        $verifyUrl = rtrim($baseUrl, '/') . route('verify.email', ['token' => $token], false);

        Mail::to($newEmail)->send(new EmailVerificationMail(
            $user->username,
            $verifyUrl,
            $code,
            $isOnion
        ));

        return redirect()->route('profile.verify.notice')
            ->with('success', 'A verification email has been sent to ' . $newEmail . '. Please verify to confirm the change.');
    }

    /**
     * Show pending email verification notice (on profile change).
     */
    public function verifyNotice(): View
    {
        return view('user.verify-email-notice', ['user' => Auth::user()]);
    }

    /**
     * Resend profile verification email.
     */
    public function resendVerification(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('profile')->with('success', 'Your email is already verified.');
        }

        if ($user->email_verification_sent_at && $user->email_verification_sent_at->diffInSeconds(now()) < 60) {
            return redirect()->back()
                ->withErrors(['resend' => 'Please wait before requesting another verification email.']);
        }

        $token = Str::random(64);
        $code = strtoupper(Str::random(6));

        $user->update([
            'email_verification_token' => $token,
            'email_verification_code' => $code,
            'email_verification_sent_at' => now(),
        ]);

        $isOnion = $this->isOnionRequest($request);
        $baseUrl = $isOnion ? config('site.onion_url') : config('site.clearnet_url');
        $verifyUrl = rtrim($baseUrl, '/') . route('verify.email', ['token' => $token], false);

        Mail::to($user->email)->send(new EmailVerificationMail(
            $user->username,
            $verifyUrl,
            $code,
            $isOnion
        ));

        return redirect()->back()->with('success', 'Verification email resent.');
    }

    /**
     * Handle 6-digit code verification from profile page.
     */
    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = Auth::user();

        if ($user->email_verification_code !== strtoupper($request->code)) {
            return redirect()->back()
                ->withErrors(['code' => 'The verification code is incorrect.']);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_code' => null,
            'email_verification_sent_at' => null,
        ]);

        return redirect()->route('profile')->with('success', 'Email verified successfully!');
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    private function isOnionRequest(Request $request): bool
    {
        $host = $request->getHost();
        $clearnetHost = parse_url(config('site.clearnet_url'), PHP_URL_HOST);

        return $host !== $clearnetHost || str_ends_with($host, '.onion');
    }
}
