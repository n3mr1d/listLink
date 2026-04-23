<x-app.layouts title="Verify Your Email">

    <div style="max-width:440px;margin:0 auto;padding:2.5rem 0;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:1.75rem;">
            <x-app.logo style="width:3rem;height:3rem;margin:0 auto 1rem;display:block;" />
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .3rem;letter-spacing:-.02em;">Check Your Email</h1>
            <p style="font-size:.75rem;color:var(--color-gh-dim);margin:0 0 .5rem;">A verification message was sent to <strong style="color:var(--color-gh-accent);">{{ $user->email }}</strong></p>
            <p style="font-size:.65rem;color:var(--color-gh-dim);background:rgba(255,255,255,0.03);padding:.5rem;border-radius:.4rem;border:1px dashed var(--color-gh-border);">
                Please check your <strong>Inbox</strong> or <strong>Spam</strong> folder.<br>
                Looking for sender: <code style="color:var(--color-gh-accent);">{{ config('mail.from.address') }}</code>
            </p>
            <p style="font-size:.6rem;color:var(--color-gh-dim);opacity:.7;margin-top:.75rem;">
                Emails usually arrive within <strong>1 minute</strong>. If you don't see it by then, please check your spam or use the resend button below.
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
                @foreach($errors->all() as $error)
                    <p style="font-size:.72rem;color:#f87171;margin:0 0 .2rem;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Instructions --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;margin-bottom:1rem;">
            <div style="padding:.7rem 1rem;border-bottom:1px solid var(--color-gh-border);background:rgba(22,27,34,.6);">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-accent);">Activation Options</span>
            </div>
            <div style="padding:1.25rem;">
                <p style="font-size:.78rem;color:var(--color-gh-dim);line-height:1.7;margin:0 0 1rem;">
                    We sent an email with two ways to activate your account:
                </p>
                <div style="display:flex;flex-direction:column;gap:.65rem;margin-bottom:1.25rem;">
                    <div style="display:flex;align-items:flex-start;gap:.65rem;">
                        <div style="width:20px;height:20px;border-radius:.3rem;background:rgba(88,166,255,.1);border:1px solid rgba(88,166,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.1rem;">
                            <span style="font-size:.6rem;font-weight:900;color:var(--color-gh-accent);">1</span>
                        </div>
                        <div>
                            <p style="font-size:.75rem;font-weight:700;color:#fff;margin:0 0 .15rem;">Click the instant link</p>
                            <p style="font-size:.65rem;color:var(--color-gh-dim);margin:0;">Open the activation link in your email — works immediately.</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:.65rem;">
                        <div style="width:20px;height:20px;border-radius:.3rem;background:rgba(88,166,255,.1);border:1px solid rgba(88,166,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:.1rem;">
                            <span style="font-size:.6rem;font-weight:900;color:var(--color-gh-accent);">2</span>
                        </div>
                        <div>
                            <p style="font-size:.75rem;font-weight:700;color:#fff;margin:0 0 .15rem;">Enter the 6-character code below</p>
                            <p style="font-size:.65rem;color:var(--color-gh-dim);margin:0;">Find the code in the email and paste it here.</p>
                        </div>
                    </div>
                </div>

                {{-- Code Form --}}
                <form action="{{ route('verify.code', ['userId' => $user->id]) }}" method="POST">
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

        {{-- Resend --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.85rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:.75rem;">
            <p style="font-size:.65rem;color:var(--color-gh-dim);margin:0;">Didn't receive the email?</p>
            <form action="{{ route('verify.resend', ['userId' => $user->id]) }}" method="POST">
                @csrf
                <button type="submit" style="padding:.4rem .85rem;background:transparent;border:1px solid var(--color-gh-border);color:var(--color-gh-dim);border-radius:.4rem;font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;">
                    Resend Email
                </button>
            </form>
        </div>

    </div>

</x-app.layouts>
