<x-app.layouts title="Register">

    <div style="max-width:420px;margin:0 auto;padding:2rem 0;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:1.75rem;">
            <x-app.logo style="width:3rem;height:3rem;margin:0 auto 1rem;display:block;" />
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .3rem;letter-spacing:-.02em;">Create Account</h1>
            <p style="font-size:.75rem;color:var(--color-gh-dim);margin:0;">Join the network. Submit and manage links privately.</p>
        </div>

        {{-- Form Card --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
            <div style="padding:1.5rem;">
                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    {{-- Username --}}
                    <div style="margin-bottom:1rem;">
                        <label for="username" style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Username *</label>
                        <div style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.45rem;overflow:hidden;">
                            <div style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                placeholder="Choose your alias" required minlength="3" maxlength="20"
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                        <p style="font-size:.55rem;color:var(--color-gh-dim);opacity:.5;margin:.25rem 0 0;">3-20 characters. Must be unique.</p>
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:1.25rem;">
                        <label for="password" style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Password *</label>
                        <div style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.45rem;overflow:hidden;">
                            <div style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </div>
                            <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6"
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                        <p style="font-size:.55rem;color:var(--color-gh-dim);opacity:.5;margin:.25rem 0 0;">Minimum 6 characters. Use something strong.</p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" style="width:100%;padding:.65rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.45rem;font-size:.75rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;">
                        Register Identity
                    </button>
                </form>

                {{-- Login Link --}}
                <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--color-gh-border);text-align:center;">
                    <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0;">
                        Already registered? <a href="{{ route('login.form') }}" style="color:var(--color-gh-accent);font-weight:700;text-decoration:none;">Sign in to your account</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Privacy Notice --}}
        <div style="margin-top:1rem;border:1px solid rgba(88,166,255,.15);border-radius:.5rem;padding:.85rem 1rem;">
            <div style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-accent);margin-bottom:.35rem;display:flex;align-items:center;gap:.35rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Privacy Guarantee
            </div>
            <p style="font-size:.68rem;color:var(--color-gh-dim);line-height:1.6;margin:0;">
                We do not require email addresses or any PII. Your account is tied only to your username. If you lose your password, there is no recovery option.
            </p>
        </div>
    </div>

</x-app.layouts>