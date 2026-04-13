<x-app.layouts title="Advertise">

    <style>
        .pkg-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: .65rem;
        }

        @media (min-width: 768px) {
            .pkg-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 420px) {
            .pkg-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-input {
            width: 100%;
            box-sizing: border-box;
            background: var(--color-gh-btn-bg);
            border: 1px solid var(--color-gh-border);
            border-radius: .4rem;
            padding: .55rem .75rem;
            color: #fff;
            font-size: .85rem;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--color-gh-accent);
        }

        .form-label {
            font-size: .7rem;
            font-weight: 700;
            color: var(--color-gh-dim);
            text-transform: uppercase;
            letter-spacing: .1em;
            display: block;
            margin-bottom: .3rem;
        }
    </style>

    <div style="max-width:960px;margin:0 auto;padding:1rem 0 3rem;">

        {{-- Page header --}}
        <div style="margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--color-gh-border);">
            <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0 0 .3rem;letter-spacing:-.02em;">Advertise on
                {{ config('app.name') }}</h1>
            <p style="color:var(--color-gh-dim);font-size:.85rem;margin:0;">Promote your .onion service to a
                privacy-conscious audience — pay with Bitcoin.</p>
        </div>

        {{-- Contact notice --}}
        <div
            style="border:1px solid rgba(88,166,255,.25);border-left:3px solid var(--color-gh-accent);background:rgba(88,166,255,.06);padding:.75rem 1rem;border-radius:.4rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.65rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)"
                stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                <polyline points="22,6 12,13 2,6" />
            </svg>
            <div style="font-size:.82rem;">
                <strong style="color:#fff;display:block;margin-bottom:.15rem;">Questions or custom packages?</strong>
                Contact us: <a href="mailto:{{ config('app.contact_email') }}"
                    style="color:var(--color-gh-accent);font-weight:700;">{{ config('app.contact_email') }}</a>
            </div>
        </div>

        {{-- ═══ Pricing Tiers ═══ --}}
        @if(!$ad)
            <div style="margin-bottom:1.5rem;">
                <h2
                    style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);margin:0 0 .75rem;">
                    Choose Your Package</h2>
                <div class="pkg-grid">
                    @foreach ($packages as $pkg)
                        @php
                            $color = $pkg->badgeColor();
                            $icons = ['starter' => '🚀', 'basic' => '⚡', 'standard' => '⭐', 'premium' => '💎', 'pro' => '🔥', 'elite' => '👑'];
                            $icon = $icons[$pkg->value] ?? '📦';
                        @endphp
                        <div
                            style="border:1px solid {{ $pkg->isPopular() ? 'var(--color-gh-accent)' : 'var(--color-gh-border)' }};border-radius:.5rem;overflow:hidden;position:relative;display:flex;flex-direction:column;">

                            @if ($pkg->isPopular())
                                <span
                                    style="position:absolute;top:.5rem;right:.5rem;background:var(--color-gh-accent);color:#0d1117;font-size:.58rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:.2rem .5rem;border-radius:2rem;">Popular</span>
                            @endif

                            <div style="padding:.85rem;border-bottom:1px solid var(--color-gh-border);">
                                <div
                                    style="width:2rem;height:2rem;border-radius:.35rem;background:{{ $color }}22;display:flex;align-items:center;justify-content:center;font-size:1rem;margin-bottom:.5rem;">
                                    {{ $icon }}
                                </div>
                                <div style="color:#fff;font-weight:700;font-size:.88rem;margin-bottom:.15rem;">
                                    {{ $pkg->label() }}
                                </div>
                                <div style="color:var(--color-gh-dim);font-size:.68rem;">{{ $pkg->durationDays() }}-day campaign
                                </div>
                                <div style="margin-top:.5rem;">
                                    <span
                                        style="font-size:1.5rem;font-weight:900;color:#fff;line-height:1;">${{ $pkg->priceUsd() }}</span>
                                </div>
                            </div>

                            <div style="padding:.75rem .85rem;flex:1;">
                                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.4rem;">
                                    @foreach ($pkg->features() as $feature)
                                        <li
                                            style="display:flex;align-items:flex-start;gap:.4rem;font-size:.72rem;color:var(--color-gh-dim);line-height:1.4;">
                                            <span style="color:#4ade80;flex-shrink:0;margin-top:.05rem;">✓</span> {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <hr style="border:none;border-top:1px solid var(--color-gh-border);margin:1.5rem 0;">

        {{-- Ad Type Examples --}}
        <div style="margin-bottom:1.5rem;">
            <h2
                style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);margin:0 0 .75rem;">
                Ad Types &amp; Placements</h2>
            <div style="display:flex;flex-direction:column;gap:.65rem;">

                {{-- Header Banner --}}
                <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                    <div
                        style="padding:.65rem 1rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                        <div>
                            <h3 style="color:#fff;font-weight:700;font-size:.85rem;margin:0 0 .1rem;">Header Banner</h3>
                            <p style="color:var(--color-gh-dim);font-size:.7rem;margin:0;">Full-width banner at the top
                                of every page</p>
                        </div>
                        <span
                            style="background:rgba(74,222,128,.1);color:#4ade80;border:1px solid rgba(74,222,128,.2);padding:.25rem .55rem;border-radius:.3rem;font-size:.68rem;font-weight:700;white-space:nowrap;">728
                            × 90 px</span>
                    </div>
                    <div style="padding:1rem;">
                        <div
                            style="border:1px solid var(--color-gh-border);border-radius:.4rem;min-height:80px;display:flex;align-items:center;justify-content:center;position:relative;">
                            <span
                                style="position:absolute;top:.3rem;right:.5rem;background:rgba(0,0,0,.7);color:var(--color-gh-dim);padding:.1rem .4rem;border-radius:.2rem;font-size:.58rem;font-weight:700;text-transform:uppercase;">Sponsored</span>
                            <div style="text-align:center;">
                                <div style="font-size:1rem;font-weight:700;color:#fff;margin-bottom:.25rem;">Your
                                    Service Name Here</div>
                                <div style="font-family:monospace;font-size:.72rem;color:var(--color-gh-accent);">
                                    http://yourservice.onion</div>
                            </div>
                        </div>
                        <p style="font-size:.72rem;color:var(--color-gh-dim);margin:.65rem 0 0;"><span
                                style="color:#fff;font-weight:700;">Available from:</span> Pro &amp; Elite packages ·
                            728×90px or 970×90px (PNG/WebP)</p>
                    </div>
                </div>

                {{-- Sidebar + Sponsored side-by-side on wider screens --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;">
                    {{-- Sidebar --}}
                    <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                        <div
                            style="padding:.65rem 1rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                            <div>
                                <h3 style="color:#fff;font-weight:700;font-size:.85rem;margin:0 0 .1rem;">Sidebar Banner
                                </h3>
                                <p style="color:var(--color-gh-dim);font-size:.7rem;margin:0;">Sidebar placement on
                                    homepage</p>
                            </div>
                            <span
                                style="background:rgba(88,166,255,.1);color:var(--color-gh-accent);border:1px solid rgba(88,166,255,.2);padding:.25rem .55rem;border-radius:.3rem;font-size:.68rem;font-weight:700;white-space:nowrap;">300×250</span>
                        </div>
                        <div style="padding:1rem;text-align:center;">
                            <div
                                style="border:1px solid var(--color-gh-border);border-radius:.4rem;min-height:100px;display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;padding:.75rem;">
                                <span
                                    style="position:absolute;top:.3rem;right:.4rem;background:rgba(0,0,0,.7);color:var(--color-gh-dim);padding:.1rem .35rem;border-radius:.2rem;font-size:.55rem;font-weight:700;text-transform:uppercase;">Ad</span>
                                <div style="font-size:.85rem;font-weight:700;color:#fff;margin-bottom:.25rem;">Your
                                    Banner Ad</div>
                                <div style="font-family:monospace;font-size:.65rem;color:var(--color-gh-accent);">
                                    http://example.onion</div>
                            </div>
                            <p style="font-size:.7rem;color:var(--color-gh-dim);margin:.5rem 0 0;"><span
                                    style="color:#fff;font-weight:700;">From:</span> Standard+</p>
                        </div>
                    </div>

                    {{-- Sponsored Link --}}
                    <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                        <div
                            style="padding:.65rem 1rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                            <div>
                                <h3 style="color:#fff;font-weight:700;font-size:.85rem;margin:0 0 .1rem;">Sponsored Link
                                </h3>
                                <p style="color:var(--color-gh-dim);font-size:.7rem;margin:0;">Within listings with
                                    "Sponsored" label</p>
                            </div>
                            <span
                                style="background:rgba(168,85,247,.1);color:#a855f7;border:1px solid rgba(168,85,247,.2);padding:.25rem .55rem;border-radius:.3rem;font-size:.68rem;font-weight:700;white-space:nowrap;">Text</span>
                        </div>
                        <div style="padding:1rem;">
                            <div style="border:1px solid var(--color-gh-border);border-radius:.4rem;overflow:hidden;">
                                <div style="padding:.4rem .75rem;background:rgba(168,85,247,.05);">
                                    <div style="display:flex;align-items:center;gap:.4rem;font-size:.78rem;">
                                        <span
                                            style="background:rgba(168,85,247,.1);color:#a855f7;border:1px solid rgba(168,85,247,.2);padding:.1rem .35rem;border-radius:.2rem;font-size:.58rem;font-weight:700;text-transform:uppercase;">Ad</span>
                                        <a href="#"
                                            style="color:var(--color-gh-accent);font-weight:700;font-size:.78rem;">Your
                                            Service Name</a>
                                    </div>
                                </div>
                            </div>
                            <p style="font-size:.7rem;color:var(--color-gh-dim);margin:.5rem 0 0;"><span
                                    style="color:#fff;font-weight:700;">Available:</span> All packages</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--color-gh-border);margin:1.5rem 0;">

        {{-- ═══ Submission Form ═══ --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
            <div
                style="padding:.7rem 1rem;border-bottom:1px solid var(--color-gh-border);color:#fff;font-weight:700;font-size:.85rem;">
                {{ $ad ? 'Update Advertisement Destination' : 'Submit Ad Request' }}
            </div>
            <div style="padding:1.25rem;">
                <form action="{{ $ad ? route('advertise.update', $ad->id) : route('advertise.store') }}" method="POST"
                    enctype="multipart/form-data" id="ad-request-form">
                    @csrf
                    @if($ad) @method('PUT') @endif

                    <div style="display:none;"><label for="website_url_hp">Website</label><input type="text"
                            name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off"></div>

                    @if(!$ad)
                        <div style="margin-bottom:1rem;">
                            <label class="form-label" for="form-package-tier">Package *</label>
                            <select name="package_tier" id="form-package-tier" required
                                style="width:100%;box-sizing:border-box;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.55rem .75rem;color:#fff;font-size:.85rem;outline:none;appearance:none;">
                                <option value="">— Select a package —</option>
                                @foreach ($packages as $pkg)
                                    <option value="{{ $pkg->value }}" {{ old('package_tier') === $pkg->value ? 'selected' : '' }}>
                                        {{ $pkg->label() }} — ${{ $pkg->priceUsd() }} USD · {{ $pkg->durationDays() }} days
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Form grid --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                        <div style="display:flex;flex-direction:column;gap:.75rem;">
                            <div>
                                <label class="form-label" for="ad-title">Ad Title *</label>
                                <input type="text" name="title" id="ad-title"
                                    value="{{ old('title', $ad->title ?? '') }}"
                                    class="form-input {{ $ad ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    placeholder="Your service name" required minlength="3" maxlength="100" {{ $ad ? 'readonly' : '' }}>
                            </div>
                            <div>
                                <label class="form-label" for="ad-url">.onion URL *</label>
                                <input type="text" name="url" id="ad-url" value="{{ old('url', $ad->url ?? '') }}"
                                    class="form-input" style="font-family:monospace;"
                                    placeholder="http://yourservice.onion" required>
                                <div
                                    style="font-size:.62rem;color:var(--color-gh-dim);font-style:italic;margin-top:.25rem;">
                                    Must be a valid .onion URL.</div>
                            </div>
                            <div>
                                <label class="form-label" for="ad-type">Ad Type *</label>
                                <select name="ad_type" id="ad-type" {{ $ad ? 'disabled' : 'required' }}
                                    class="form-input {{ $ad ? 'opacity-50' : '' }}" style="appearance:none;">
                                    @foreach ($adTypes as $type)
                                        <option value="{{ $type->value }}" {{ old('ad_type', $ad->ad_type->value ?? '') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @if($ad) <input type="hidden" name="ad_type" value="{{ $ad->ad_type->value }}"> @endif
                            </div>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:.75rem;">
                            <div>
                                <label class="form-label" for="ad-placement">Preferred Placement *</label>
                                <select name="placement" id="ad-placement" {{ $ad ? 'disabled' : 'required' }}
                                    class="form-input {{ $ad ? 'opacity-50' : '' }}" style="appearance:none;">
                                    @foreach ($placements as $placement)
                                        <option value="{{ $placement->value }}" {{ old('placement', $ad->placement->value ?? '') === $placement->value ? 'selected' : '' }}>{{ $placement->label() }}</option>
                                    @endforeach
                                </select>
                                @if($ad) <input type="hidden" name="placement" value="{{ $ad->placement->value }}">
                                @endif
                            </div>
                            <div>
                                <label class="form-label" for="ad-banner">Banner Image (optional)</label>
                                <input type="file" name="banner" id="ad-banner" {{ $ad ? 'disabled' : '' }}
                                    class="form-input {{ $ad ? 'opacity-50' : '' }}"
                                    style="padding:.4rem .6rem;font-size:.75rem;color:var(--color-gh-dim);"
                                    accept="image/png,image/jpg,image/jpeg,image/gif,image/webp">
                                <div
                                    style="font-size:.62rem;color:var(--color-gh-dim);margin-top:.25rem;line-height:1.5;">
                                    Max 512KB. PNG/JPG/WebP. Header 728×90px · Sidebar 300×250px</div>
                            </div>
                            <div>
                                <label class="form-label" for="ad-contact">Contact Information *</label>
                                <input type="text" name="contact_info" id="ad-contact"
                                    value="{{ old('contact_info', $ad->contact_info ?? '') }}"
                                    class="form-input {{ $ad ? 'opacity-50' : '' }}"
                                    placeholder="Email, XMPP, or Session ID" required {{ $ad ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div
                        style="display:flex;flex-direction:column;align-items:center;gap:.75rem;padding-top:.75rem;border-top:1px solid var(--color-gh-border);">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:.3rem;">
                            <label class="form-label" for="ad-challenge">{{ $challenge }} *</label>
                            <input type="number" name="challenge" id="ad-challenge" required placeholder="?"
                                class="form-input" style="width:6rem;text-align:center;font-weight:700;">
                            <div
                                style="font-size:.6rem;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;font-weight:800;">
                                Human Verification</div>
                        </div>
                        <button type="submit"
                            style="width:100%;max-width:380px;padding:.75rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.45rem;font-weight:900;font-size:.82rem;text-transform:uppercase;letter-spacing:.1em;cursor:pointer;">
                            {{ $ad ? 'Save Changes' : 'Submit Advertisement Request' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

</x-app.layouts>