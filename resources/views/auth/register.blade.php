<x-app.layouts title="Create Account">

    <div style="max-width:440px;margin:0 auto;padding:2rem 0;">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:1.75rem;">
            <x-app.logo style="width:3rem;height:3rem;margin:0 auto 1rem;display:block;" />
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .3rem;letter-spacing:-.02em;">Create
                Account</h1>
            <p style="font-size:.75rem;color:var(--color-gh-dim);margin:0;">Join the network. Submit and manage links
                privately.</p>
        </div>

        {{-- Global Errors --}}
        @if ($errors->any())
            <div
                style="border:1px solid rgba(248,113,113,.25);border-radius:.45rem;padding:.75rem 1rem;margin-bottom:1rem;background:rgba(248,113,113,.06);">
                @foreach ($errors->all() as $error)
                    <p style="font-size:.72rem;color:#f87171;margin:0 0 .2rem;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Form Card --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
            <div style="padding:1.5rem;">
                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    {{-- Username --}}
                    <div style="margin-bottom:1rem;">
                        <label for="username"
                            style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Username
                            *</label>
                        <div
                            style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid {{ $errors->has('username') ? 'rgba(248,113,113,.5)' : 'var(--color-gh-border)' }};border-radius:.45rem;overflow:hidden;">
                            <div
                                style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <input type="text" name="username" id="username" value="{{ old('username') }}"
                                placeholder="Choose your alias" required minlength="3" maxlength="20"
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                        <p style="font-size:.55rem;color:var(--color-gh-dim);opacity:.5;margin:.25rem 0 0;">3–20
                            characters. Letters, numbers, underscores only.</p>
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:1rem;">
                        <label for="password"
                            style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Password
                            *</label>
                        <div
                            style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid {{ $errors->has('password') ? 'rgba(248,113,113,.5)' : 'var(--color-gh-border)' }};border-radius:.45rem;overflow:hidden;">
                            <div
                                style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0110 0v4" />
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                minlength="6"
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                        <p style="font-size:.55rem;color:var(--color-gh-dim);opacity:.5;margin:.25rem 0 0;">Minimum 6
                            characters.</p>
                    </div>
                    <div style="margin-bottom:1.5rem;">
                        <label for="captcha"
                            style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Security
                            Verification *</label>
                        <div style="display:flex;gap:.75rem;align-items:stretch;">
                            {{-- Captcha Image Side --}}
                            <div
                                style="flex:1.2;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;position:relative;display:flex;align-items:center;justify-content:center;padding:.4rem;">
                                <img id="captcha-img" src="{{ $code }}" alt="captcha"
                                    style="height:30px;display:block;border-radius:3px;">
                                <button type="button" onclick="refreshCaptcha(event)"
                                    style="position:absolute;right:.4rem;top:50%;transform:translateY(-50%);background:transparent;border:none;color:var(--color-gh-dim);cursor:pointer;opacity:.5;transition:opacity .2s;display:flex;align-items:center;"
                                    onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.5'">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16" />
                                    </svg>
                                </button>
                            </div>
                            {{-- Input Side --}}
                            <div
                                style="flex:1;background:var(--color-gh-bg);border:1px solid {{ $errors->has('captcha') ? 'rgba(248,113,113,.5)' : 'var(--color-gh-border)' }};border-radius:.5rem;overflow:hidden;">
                                <input type="text" name="captcha" id="captcha" placeholder="ENTER CODE" required
                                    minlength="6"
                                    style="width:100%;height:100%;background:transparent;border:none;color:#fff;padding:0 .75rem;font-size:.8rem;outline:none;text-align:center;letter-spacing:.15em;text-transform:uppercase;">
                            </div>
                        </div>
                        @error('captcha')
                            <p style="font-size:.55rem;color:#f87171;margin:.35rem 0 0;">{{ $message }}</p>
                        @enderror
                    </div>

                    <script>
                        function refreshCaptcha(event) {
                            const btn = event.currentTarget;
                            btn.style.transform = 'rotate(360deg)';
                            btn.style.transition = 'transform 0.5s ease';

                            fetch('{{ route('captcha.refresh') }}')
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('captcha-img').src = data.code;
                                    setTimeout(() => {
                                        btn.style.transform = 'rotate(0deg)';
                                        btn.style.transition = 'none';
                                    }, 500);
                                });
                        }
                    </script>
                    {{-- Password Confirm --}}
                    <div style="margin-bottom:1.25rem;">
                        <label for="password_confirmation"
                            style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Confirm
                            Password *</label>
                        <div
                            style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.45rem;overflow:hidden;">
                            <div
                                style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0110 0v4" />
                                </svg>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="••••••••" required minlength="6"
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                    </div>

                    {{-- Email (required) --}}
                    <div
                        style="margin-bottom:1.25rem;border:1px solid rgba(88,166,255,.12);border-radius:.5rem;padding:1rem;">
                        <div
                            style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-accent);margin-bottom:.6rem;display:flex;align-items:center;gap:.35rem;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            Email Verification *
                        </div>
                        <label for="email"
                            style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.35rem;">Email
                            Address *</label>
                        <div
                            style="display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid {{ $errors->has('email') ? 'rgba(248,113,113,.5)' : 'var(--color-gh-border)' }};border-radius:.45rem;overflow:hidden;">
                            <div
                                style="padding:0 .65rem;display:flex;align-items:center;color:var(--color-gh-dim);opacity:.4;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                placeholder="your@email.com" required
                                style="flex:1;background:transparent;border:none;color:#fff;padding:.6rem .6rem .6rem 0;font-size:.82rem;outline:none;">
                        </div>
                        <p style="font-size:.55rem;color:var(--color-gh-dim);opacity:.5;margin:.3rem 0 0;">A
                            verification link will be sent to this address. Verification is required to activate your
                            account.</p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="register-submit"
                        style="width:100%;padding:.65rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.45rem;font-size:.75rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;">
                        Register Identity
                    </button>
                </form>

                {{-- Login Link --}}
                <div
                    style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--color-gh-border);text-align:center;">
                    <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0;">
                        Already registered? <a href="{{ route('login.form') }}"
                            style="color:var(--color-gh-accent);font-weight:700;text-decoration:none;">Sign in to your
                            account</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Privacy Notice --}}
        <div style="margin-top:1rem;border:1px solid rgba(88,166,255,.15);border-radius:.5rem;padding:.85rem 1rem;">
            <div
                style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-accent);margin-bottom:.35rem;display:flex;align-items:center;gap:.35rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Privacy Guarantee
            </div>
            <p style="font-size:.68rem;color:var(--color-gh-dim);line-height:1.6;margin:0;">
                Email is required to activate your account and for account recovery. If you lose your password,
                verification via email is the only way to recover your access.
            </p>
        </div>

    </div>


</x-app.layouts>
