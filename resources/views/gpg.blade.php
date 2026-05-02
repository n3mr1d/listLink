<x-app.layouts title="GPG Verification"
    description="Verify official communications and secure your interactions with {{ config('app.name') }} using our PGP/GPG public key.">

    <div style="max-width:900px;margin:2rem auto 4rem;padding:0 1.5rem;">

        {{-- Hero / Header --}}
        <div style="text-align:center;margin-bottom:3rem;position:relative;">
            <div style="width:70px;height:70px;background:linear-gradient(135deg, rgba(88,166,255,0.2) 0%, rgba(88,166,255,0.05) 100%);border:1px solid rgba(88,166,255,0.3);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;color:var(--color-gh-accent);box-shadow:0 8px 24px rgba(0,0,0,0.2);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>
            <h1 style="font-size:2.25rem;font-weight:900;color:#fff;margin:0 0 .75rem;letter-spacing:-0.02em;">Security Protocol</h1>
            <p style="color:var(--color-gh-dim);font-size:0.95rem;max-width:600px;margin:0 auto;line-height:1.6;">
                Verify the authenticity of our communications. All official emails from <span style="color:#fff;font-weight:600;">{{ config('app.name') }}</span> are digitally signed with the identity below.
            </p>
        </div>

        <div style="display:grid;grid-template-columns:1fr;gap:2rem;">

            {{-- Main Content Card --}}
            <div style="background:rgba(22,27,34,0.5);border:1px solid var(--color-gh-border);border-radius:1rem;overflow:hidden;backdrop-filter:blur(10px);">
                
                {{-- Metadata Header --}}
                <div style="padding:1.5rem;border-bottom:1px solid var(--color-gh-border);background:rgba(255,255,255,0.02);display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:1rem;">
                    <div>
                        <div style="font-size:0.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:0.15em;margin-bottom:0.4rem;">Trust Identity</div>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <span style="font-size:1.1rem;font-weight:700;color:#fff;">{{ config('site.whoami') }}</span>
                            <span style="font-size:0.8rem;color:var(--color-gh-accent);opacity:0.8;">&lt;{{ config('site.contact_email') }}&gt;</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <span style="display:flex;align-items:center;gap:0.4rem;font-size:0.65rem;font-weight:700;color:#4ade80;background:rgba(74,222,128,0.1);padding:0.4rem 0.75rem;border-radius:2rem;border:1px solid rgba(74,222,128,0.2);">
                            <span style="width:6px;height:6px;background:#4ade80;border-radius:50%;display:inline-block;box-shadow:0 0 8px #4ade80;"></span>
                            VALID & ACTIVE
                        </span>
                    </div>
                </div>

                {{-- Fingerprint Section --}}
                <div style="padding:1.5rem;background:rgba(0,0,0,0.15);">
                    <label style="display:block;font-size:0.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.75rem;">Key Fingerprint</label>
                    <div style="position:relative;">
                        <code style="display:block;width:100%;background:#0d1117;color:#fff;font-family:'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;font-size:0.85rem;padding:1.25rem;border-radius:0.75rem;border:1px solid var(--color-gh-border);letter-spacing:0.05em;line-height:1.6;word-break:break-all;">
                            {{ config('site.gpg_fingerprint') }}
                        </code>
                        <div style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);">
                             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-border)" stroke-width="1.5" style="opacity:0.5;">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                             </svg>
                        </div>
                    </div>
                </div>

                {{-- Key Block Section --}}
                <div style="padding:1.5rem;padding-top:0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                        <label style="font-size:0.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:0.1em;">Public Key Block</label>
                        <div style="display:flex;gap:0.75rem;">
                            <button onclick="downloadKey()" style="background:transparent;border:1px solid var(--color-gh-border);color:var(--color-gh-dim);padding:0.5rem 0.9rem;font-size:0.7rem;font-weight:700;border-radius:0.5rem;cursor:pointer;display:flex;align-items:center;gap:0.5rem;transition:all 0.2s;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Download .asc
                            </button>
                            <button id="copy-btn" onclick="copyKey()" style="background:var(--color-gh-accent);border:none;color:#fff;padding:0.5rem 1rem;font-size:0.7rem;font-weight:700;border-radius:0.5rem;cursor:pointer;display:flex;align-items:center;gap:0.5rem;transition:all 0.2s;box-shadow:0 4px 12px rgba(88,166,255,0.2);">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                                <span id="copy-text">Copy Key</span>
                            </button>
                        </div>
                    </div>
                    <div style="position:relative;">
                        <pre id="gpg-key-block" style="margin:0;padding:1.5rem;background:#0d1117;border:1px solid var(--color-gh-border);border-radius:0.75rem;color:rgba(230,237,243,0.85);font-size:0.75rem;line-height:1.5;overflow-x:auto;max-height:500px;font-family:'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;">{{ config('site.gpg_public_key') }}</pre>
                        
                        {{-- Subtle Bottom Fade for long keys --}}
                        <div style="position:absolute;bottom:1px;left:1px;right:1px;height:40px;background:linear-gradient(to top, #0d1117, transparent);border-bottom-left-radius:0.75rem;border-bottom-right-radius:0.75rem;pointer-events:none;"></div>
                    </div>
                </div>
            </div>

            {{-- Help / Instructions Footer --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1.5rem;">
                <div style="background:rgba(35,134,54,0.05);border:1px solid rgba(35,134,54,0.2);padding:1.5rem;border-radius:1rem;display:flex;gap:1rem;">
                    <div style="color:#4ade80;flex-shrink:0;padding-top:0.25rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="margin:0 0 0.5rem;font-size:0.85rem;font-weight:800;color:#fff;">How to Verify</h4>
                        <p style="margin:0;font-size:0.8rem;color:var(--color-gh-dim);line-height:1.5;">
                            Save the key as <code style="color:#fff;">key.asc</code> and run:
                            <br><code style="display:inline-block;margin-top:0.4rem;background:rgba(0,0,0,0.3);padding:0.2rem 0.4rem;border-radius:0.3rem;">gpg --import key.asc</code>
                        </p>
                    </div>
                </div>

                <div style="background:rgba(88,166,255,0.05);border:1px solid rgba(88,166,255,0.2);padding:1.5rem;border-radius:1rem;display:flex;gap:1rem;">
                    <div style="color:var(--color-gh-accent);flex-shrink:0;padding-top:0.25rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                    </div>
                    <div>
                        <h4 style="margin:0 0 0.5rem;font-size:0.85rem;font-weight:800;color:#fff;">Encrypted Support</h4>
                        <p style="margin:0;font-size:0.8rem;color:var(--color-gh-dim);line-height:1.5;">
                            Need to send sensitive data? Encrypt your message using our public key before sending to ensure only we can read it.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyKey() {
            const btn = document.getElementById('copy-btn');
            const textSpan = document.getElementById('copy-text');
            const keyBlock = document.getElementById('gpg-key-block').innerText;
            
            navigator.clipboard.writeText(keyBlock).then(() => {
                const originalBg = btn.style.background;
                const originalText = textSpan.innerText;
                
                btn.style.background = '#238636';
                textSpan.innerText = 'Copied!';
                
                setTimeout(() => {
                    btn.style.background = originalBg;
                    textSpan.innerText = originalText;
                }, 2000);
            });
        }

        function downloadKey() {
            const keyBlock = document.getElementById('gpg-key-block').innerText;
            const blob = new Blob([keyBlock], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'hiddenline_public_key.asc';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>

    <style>
        button:hover {
            filter: brightness(1.2);
            transform: translateY(-1px);
        }
        button:active {
            transform: translateY(0);
        }
    </style>
</x-app.layouts>