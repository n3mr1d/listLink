<x-app.layouts title="Welcome to {{ config('app.name') }}">

    <div style="max-width:520px;margin:0 auto;padding:3rem 0;text-align:center;">

        {{-- Logo --}}
        <x-app.logo style="width:4rem;height:4rem;margin:0 auto 1.5rem;display:block;" />

        {{-- Welcome Heading --}}
        <div style="margin-bottom:1.5rem;">
            <div style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.2em;color:var(--color-gh-accent);margin-bottom:.5rem;">
                Registration Successful
            </div>
            <h1 style="font-size:1.8rem;font-weight:900;color:#fff;letter-spacing:-.03em;margin:0 0 .5rem;">
                Welcome, {{ Auth::user()->username }}
            </h1>
            <p style="font-size:.8rem;color:var(--color-gh-dim);line-height:1.7;margin:0;">
                Your identity has been established on the network.<br>
                You now have access to submit links, manage your nodes, and monitor uptime.
            </p>
        </div>

        {{-- Status Card --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;margin-bottom:1.5rem;text-align:left;">
            <div style="background:rgba(22,27,34,.6);border-bottom:1px solid var(--color-gh-border);padding:.7rem 1rem;">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);">Account Status</span>
            </div>
            <div style="padding:1rem 1.25rem;">
                <div style="display:flex;flex-direction:column;gap:.65rem;">
                    {{-- Username --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:.65rem;color:var(--color-gh-dim);display:flex;align-items:center;gap:.35rem;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Username
                        </span>
                        <span style="font-size:.72rem;font-weight:800;color:#fff;">{{ Auth::user()->username }}</span>
                    </div>
                    {{-- Email status --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:.65rem;color:var(--color-gh-dim);display:flex;align-items:center;gap:.35rem;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Email
                        </span>
                        @if(Auth::user()->email)
                            <span style="font-size:.65rem;font-weight:800;color:#4ade80;display:flex;align-items:center;gap:.25rem;">
                                <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                                Verified
                            </span>
                        @else
                            <span style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);display:flex;align-items:center;gap:.25rem;">
                                <span style="width:5px;height:5px;border-radius:50%;background:var(--color-gh-dim);display:inline-block;"></span>
                                Anonymous (no email)
                            </span>
                        @endif
                    </div>
                    {{-- Access level --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:.65rem;color:var(--color-gh-dim);display:flex;align-items:center;gap:.35rem;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Access Level
                        </span>
                        <span style="font-size:.65rem;padding:.15rem .5rem;border-radius:.3rem;background:rgba(88,166,255,.1);border:1px solid rgba(88,166,255,.2);color:var(--color-gh-accent);font-weight:800;text-transform:uppercase;letter-spacing:.06em;">
                            Member
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-bottom:1.5rem;">
            <a href="{{ route('submit.create') }}" style="display:flex;flex-direction:column;align-items:center;gap:.5rem;padding:1rem;border:1px solid var(--color-gh-border);border-radius:.5rem;text-decoration:none;color:var(--color-gh-dim);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#fff;">Submit Link</span>
                <span style="font-size:.58rem;color:var(--color-gh-dim);">Broadcast a node</span>
            </a>
            <a href="{{ route('dashboard') }}" style="display:flex;flex-direction:column;align-items:center;gap:.5rem;padding:1rem;border:1px solid var(--color-gh-border);border-radius:.5rem;text-decoration:none;color:var(--color-gh-dim);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><circle cx="6" cy="6" r="1"/><circle cx="6" cy="18" r="1"/></svg>
                <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#fff;">Dashboard</span>
                <span style="font-size:.58rem;color:var(--color-gh-dim);">Command center</span>
            </a>
            <a href="{{ route('profile') }}" style="display:flex;flex-direction:column;align-items:center;gap:.5rem;padding:1rem;border:1px solid var(--color-gh-border);border-radius:.5rem;text-decoration:none;color:var(--color-gh-dim);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#fff;">Edit Profile</span>
                <span style="font-size:.58rem;color:var(--color-gh-dim);">Control panel</span>
            </a>
            <a href="{{ route('home') }}" style="display:flex;flex-direction:column;align-items:center;gap:.5rem;padding:1rem;border:1px solid var(--color-gh-border);border-radius:.5rem;text-decoration:none;color:var(--color-gh-dim);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#fff;">Browse</span>
                <span style="font-size:.58rem;color:var(--color-gh-dim);">Explore the network</span>
            </a>
        </div>

        {{-- Note --}}
        <p style="font-size:.65rem;color:var(--color-gh-dim);line-height:1.7;opacity:.6;">
            Remember your username and password. If you registered without an email, there is no account recovery option.
        </p>

    </div>

</x-app.layouts>
