<x-app.layouts title="My Profile – Control Panel">

    <style>
        .cp-section { border:1px solid var(--color-gh-border); border-radius:.6rem; overflow:hidden; margin-bottom:1.25rem; }
        .cp-head { padding:.7rem 1rem; border-bottom:1px solid var(--color-gh-border); background:rgba(22,27,34,.6); display:flex; align-items:center; gap:.5rem; }
        .cp-head-title { font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:var(--color-gh-accent); }
        .cp-body { padding:1.25rem; }
        .cp-field-wrap { margin-bottom:1rem; }
        .cp-label { font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:var(--color-gh-dim); display:block; margin-bottom:.35rem; }
        .cp-input-row { display:flex; align-items:center; background:var(--color-gh-bg); border:1px solid var(--color-gh-border); border-radius:.45rem; overflow:hidden; }
        .cp-input-icon { padding:0 .65rem; display:flex; align-items:center; color:var(--color-gh-dim); opacity:.4; flex-shrink:0; }
        .cp-input { flex:1; background:transparent; border:none; color:#fff; padding:.6rem .6rem .6rem 0; font-size:.82rem; outline:none; }
        .cp-btn { display:inline-flex; align-items:center; gap:.4rem; padding:.55rem 1rem; border:none; border-radius:.4rem; font-size:.68rem; font-weight:900; text-transform:uppercase; letter-spacing:.06em; cursor:pointer; }
        .cp-btn-primary { background:var(--color-gh-accent); color:#0d1117; }
        .cp-btn-ghost { background:transparent; border:1px solid var(--color-gh-border); color:var(--color-gh-dim); }
        .cp-hint { font-size:.55rem; color:var(--color-gh-dim); opacity:.5; margin:.25rem 0 0; }
        .cp-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.55rem; font-weight:800; text-transform:uppercase; letter-spacing:.08em; padding:.2rem .55rem; border-radius:.35rem; border:1px solid; }
        .cp-badge-ok { color:#4ade80; border-color:rgba(74,222,128,.25); background:rgba(74,222,128,.06); }
        .cp-badge-warn { color:#fb923c; border-color:rgba(251,146,60,.25); background:rgba(251,146,60,.06); }
        .cp-badge-null { color:var(--color-gh-dim); border-color:var(--color-gh-border); }
        .alert-success { border:1px solid rgba(74,222,128,.25); background:rgba(74,222,128,.06); border-radius:.45rem; padding:.7rem 1rem; margin-bottom:1rem; }
        .alert-error { border:1px solid rgba(248,113,113,.25); background:rgba(248,113,113,.06); border-radius:.45rem; padding:.7rem 1rem; margin-bottom:1rem; }
        .alert-text { font-size:.72rem; margin:0; }
        .alert-success .alert-text { color:#4ade80; }
        .alert-error .alert-text { color:#f87171; }
    </style>

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:1.5rem;">
        <div>
            <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.3rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.12em;">Profile Settings</span>
            </div>
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .2rem;letter-spacing:-.02em;">Control Panel</h1>
            <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0;">Manage your identity, credentials, and email verification.</p>
        </div>
        <a href="{{ route('dashboard') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .85rem;border:1px solid var(--color-gh-border);border-radius:.4rem;font-size:.63rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--color-gh-dim);text-decoration:none;">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
            Dashboard
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert-success"><p class="alert-text">{{ session('success') }}</p></div>
    @endif
    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $err)
                <p class="alert-text" style="margin-bottom:.15rem;">{{ $err }}</p>
            @endforeach
        </div>
    @endif

    {{-- Email Verification Pending Banner --}}
    @if($user->email && !$user->hasVerifiedEmail())
        <div style="border:1px solid rgba(251,146,60,.3);background:rgba(251,146,60,.06);border-radius:.5rem;padding:.85rem 1rem;margin-bottom:1.25rem;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;">
            <div>
                <p style="font-size:.7rem;font-weight:800;color:#fb923c;margin:0 0 .2rem;">Email Pending Verification</p>
                <p style="font-size:.65rem;color:var(--color-gh-dim);margin:0;">{{ $user->email }} — check your inbox for the verification code or link.</p>
            </div>
            <a href="{{ route('profile.verify.notice') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .85rem;border:1px solid rgba(251,146,60,.3);border-radius:.4rem;font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#fb923c;text-decoration:none;">
                Verify Now
            </a>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr;gap:0;">

        {{-- ── ACCOUNT INFO ──────────────────────────────────────── --}}
        <div class="cp-section">
            <div class="cp-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="cp-head-title">Account Information</span>
            </div>
            <div class="cp-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.65rem;">
                    <div>
                        <span class="cp-label">Username</span>
                        <span style="font-size:.85rem;font-weight:800;color:#fff;">{{ $user->username }}</span>
                    </div>
                    <div>
                        <span class="cp-label">Email</span>
                        @if($user->email)
                            <span style="font-size:.78rem;color:var(--color-gh-dim);">{{ $user->email }}</span>
                        @else
                            <span style="font-size:.72rem;color:var(--color-gh-dim);font-style:italic;">Not set (anonymous)</span>
                        @endif
                    </div>
                    <div>
                        <span class="cp-label">Email Status</span>
                        @if(!$user->email)
                            <span class="cp-badge cp-badge-null">No Email</span>
                        @elseif($user->hasVerifiedEmail())
                            <span class="cp-badge cp-badge-ok">
                                <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                                Verified
                            </span>
                        @else
                            <span class="cp-badge cp-badge-warn">
                                <span style="width:5px;height:5px;border-radius:50%;background:#fb923c;display:inline-block;"></span>
                                Unverified
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="cp-label">Role</span>
                        <span style="font-size:.72rem;padding:.15rem .5rem;border-radius:.3rem;background:rgba(88,166,255,.1);border:1px solid rgba(88,166,255,.2);color:var(--color-gh-accent);font-weight:800;text-transform:uppercase;letter-spacing:.06em;">{{ ucfirst($user->role) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CHANGE USERNAME ───────────────────────────────────── --}}
        <div class="cp-section">
            <div class="cp-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                <span class="cp-head-title">Change Username</span>
            </div>
            <div class="cp-body">
                <form action="{{ route('profile.username') }}" method="POST" style="max-width:380px;">
                    @csrf
                    <div class="cp-field-wrap">
                        <label class="cp-label" for="username">New Username</label>
                        <div class="cp-input-row">
                            <div class="cp-input-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}"
                                class="cp-input" placeholder="New alias" minlength="3" maxlength="20" required>
                        </div>
                        <p class="cp-hint">3–20 characters. Letters, numbers, underscores only.</p>
                    </div>
                    <button type="submit" class="cp-btn cp-btn-primary">Update Username</button>
                </form>
            </div>
        </div>

        {{-- ── CHANGE PASSWORD ───────────────────────────────────── --}}
        <div class="cp-section">
            <div class="cp-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <span class="cp-head-title">Change Password</span>
            </div>
            <div class="cp-body">
                <form action="{{ route('profile.password') }}" method="POST" style="max-width:380px;">
                    @csrf
                    <div class="cp-field-wrap">
                        <label class="cp-label" for="current_password">Current Password</label>
                        <div class="cp-input-row">
                            <div class="cp-input-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </div>
                            <input type="password" name="current_password" id="current_password" class="cp-input" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="cp-field-wrap">
                        <label class="cp-label" for="new_password">New Password</label>
                        <div class="cp-input-row">
                            <div class="cp-input-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </div>
                            <input type="password" name="password" id="new_password" class="cp-input" placeholder="••••••••" minlength="6" required>
                        </div>
                        <p class="cp-hint">Minimum 6 characters.</p>
                    </div>
                    <div class="cp-field-wrap">
                        <label class="cp-label" for="password_confirmation">Confirm New Password</label>
                        <div class="cp-input-row">
                            <div class="cp-input-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="cp-input" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button type="submit" class="cp-btn cp-btn-primary">Update Password</button>
                </form>
            </div>
        </div>

        {{-- ── CHANGE EMAIL ──────────────────────────────────────── --}}
        <div class="cp-section">
            <div class="cp-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span class="cp-head-title">{{ $user->email ? 'Change Email Address' : 'Add Email Address' }}</span>
            </div>
            <div class="cp-body">
                <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0 0 1rem;line-height:1.6;">
                    @if($user->email && $user->hasVerifiedEmail())
                        Your current email is <strong style="color:#fff;">{{ $user->email }}</strong> (verified).
                        Changing it will require re-verification of the new address.
                    @elseif($user->email)
                        Your current email <strong style="color:#fff;">{{ $user->email }}</strong> is <span style="color:#fb923c;">not yet verified</span>.
                        You can update it or verify the existing one.
                    @else
                        You have no email set. Adding one enables account recovery via email verification.
                    @endif
                </p>
                <form action="{{ route('profile.email') }}" method="POST" style="max-width:380px;">
                    @csrf
                    <div class="cp-field-wrap">
                        <label class="cp-label" for="email">{{ $user->email ? 'New Email Address' : 'Email Address' }}</label>
                        <div class="cp-input-row">
                            <div class="cp-input-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <input type="email" name="email" id="email" class="cp-input"
                                placeholder="your@email.com" value="{{ old('email') }}" required>
                        </div>
                        <p class="cp-hint">A verification email will be sent to confirm the change.</p>
                    </div>
                    <button type="submit" class="cp-btn cp-btn-primary">
                        {{ $user->email ? 'Update & Verify Email' : 'Add & Verify Email' }}
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-app.layouts>
