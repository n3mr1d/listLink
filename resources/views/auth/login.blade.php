<x-app.layouts title="Login">

    <div style="max-width:420px;margin:0 auto;padding:2.5rem 0;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:1.75rem;">
            <x-app.logo style="width:3rem;height:3rem;margin:0 auto 1rem;display:block;" />
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .3rem;letter-spacing:-.02em;">Welcome Back</h1>
            <p style="font-size:.75rem;color:var(--color-gh-dim);margin:0;">Security starts with privacy. Access your dashboard.</p>
        </div>

        {{-- Form Card --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
            <div style="padding:1.5rem;">
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    {{-- Username --}}
                    <div style="margin-bottom:1.1rem;">
                        <label for="username" style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Username</label>
                        <div style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.45rem;overflow:hidden;">
                            <div style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                placeholder="Enter your identity" required autofocus
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:1.25rem;">
                        <label for="password" style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Password</label>
                        <div style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.45rem;overflow:hidden;">
                            <div style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </div>
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" style="width:100%;padding:.65rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.45rem;font-size:.75rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;">
                        Sign In
                    </button>
                </form>

                {{-- Register Link --}}
                <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--color-gh-border);text-align:center;">
                    <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0;">
                        Don't have an account? <a href="{{ route('register.form') }}" style="color:var(--color-gh-accent);font-weight:700;text-decoration:none;">Register anonymously</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Privacy Notice --}}
        <div style="margin-top:1rem;border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.65rem 1rem;text-align:center;">
            <p style="font-size:.6rem;color:var(--color-gh-dim);margin:0;font-style:italic;display:flex;align-items:center;justify-content:center;gap:.35rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;opacity:.5;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Data is encrypted and stored on offshore servers. No logs, no tracking.
            </p>
        </div>
    </div>

</x-app.layouts>