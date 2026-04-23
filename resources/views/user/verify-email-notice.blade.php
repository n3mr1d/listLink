<x-app.layouts title="Verify Your Email – Profile">

    <div style="max-width:440px;margin:0 auto;padding:2.5rem 0;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:1.75rem;">
            <x-app.logo style="width:3rem;height:3rem;margin:0 auto 1rem;display:block;" />
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .3rem;letter-spacing:-.02em;">Verify New Email</h1>
            <p style="font-size:.75rem;color:var(--color-gh-dim);margin:0 0 .5rem;">Confirm <strong style="color:var(--color-gh-accent);">{{ $user->email }}</strong> to apply the change.</p>
            <p style="font-size:.65rem;color:var(--color-gh-dim);background:rgba(255,255,255,0.03);padding:.5rem;border-radius:.4rem;border:1px dashed var(--color-gh-border);">
                Please check your <strong>Inbox</strong> or <strong>Spam</strong> folder.<br>
                Looking for sender: <code style="color:var(--color-gh-accent);">{{ config('mail.from.address') }}</code>
            </p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div style="border:1px solid rgba(74,222,128,.25);background:rgba(74,222,128,.06);border-radius:.45rem;padding:.75rem 1rem;margin-bottom:1rem;">
                <p style="font-size:.72rem;color:#4ade80;margin:0;">{{ session('success') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div style="border:1px solid rgba(248,113,113,.25);background:rgba(248,113,113,.06);border-radius:.45rem;padding:.75rem 1rem;margin-bottom:1rem;">
                @foreach($errors->all() as $err)
                    <p style="font-size:.72rem;color:#f87171;margin:0 0 .2rem;">{{ $err }}</p>
                @endforeach
            </div>
        @endif

        {{-- Code Entry --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;margin-bottom:1rem;">
            <div style="padding:.7rem 1rem;border-bottom:1px solid var(--color-gh-border);background:rgba(22,27,34,.6);">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-accent);">Enter Verification Code</span>
            </div>
            <div style="padding:1.25rem;">
                <p style="font-size:.75rem;color:var(--color-gh-dim);line-height:1.7;margin:0 0 1rem;">
                    Check your inbox for the 6-character code or click the activation link in the email.
                </p>
                <form action="{{ route('profile.verify.code') }}" method="POST">
                    @csrf
                    <label for="code" style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;">Verification Code</label>
                    <div style="display:flex;gap:.5rem;">
                        <input type="text" name="code" id="code" placeholder="A1B2C3" maxlength="6"
                            style="flex:1;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);color:#fff;padding:.6rem .75rem;border-radius:.45rem;font-size:1rem;font-weight:900;letter-spacing:.25em;text-transform:uppercase;outline:none;text-align:center;" required>
                        <button type="submit" style="padding:.6rem 1rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.45rem;font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;white-space:nowrap;">
                            Verify
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Resend & Cancel --}}
        <div style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;">
            <form action="{{ route('profile.verify.resend') }}" method="POST">
                @csrf
                <button type="submit" style="padding:.4rem .85rem;background:transparent;border:1px solid var(--color-gh-border);color:var(--color-gh-dim);border-radius:.4rem;font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;">
                    Resend Code
                </button>
            </form>
            <a href="{{ route('profile') }}" style="font-size:.62rem;color:var(--color-gh-dim);text-decoration:none;">
                ← Back to Profile
            </a>
        </div>

    </div>

</x-app.layouts>
