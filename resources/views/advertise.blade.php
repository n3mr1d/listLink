<x-app.layouts title="Advertise">

    <style>
        .pkg-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .65rem;
        }

        @media (max-width: 600px) {
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

        .stats-contact-grid {
            display: grid;
            grid-template-columns: 2fr 1.5fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
            align-items: start;
        }

        @media (max-width: 768px) {
            .stats-contact-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
    </style>

    <div style="max-width:960px;margin:0 auto;padding:1rem 0 3rem;">

        {{-- Page header --}}
        <div style="margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--color-gh-border);">
            <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0 0 .3rem;letter-spacing:-.02em;">Advertise on
                {{ config('app.name') }}
            </h1>
            <p style="color:var(--color-gh-dim);font-size:.85rem;margin:0;">Promote your .onion service to a
                privacy-conscious audience pay with Bitcoin.</p>
        </div>

        <div class="stats-contact-grid">
            {{-- Stats --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div
                    style="border:1px solid var(--color-gh-border);padding:1.25rem;border-radius:.6rem;text-align:center;display:flex;flex-direction:column;justify-content:center;min-height:90px;">
                    <div
                        style="font-size:.6rem;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;font-weight:800;margin-bottom:.5rem;">
                        Network Visibility</div>
                    <div
                        style="font-size:1.85rem;font-weight:900;color:var(--color-gh-accent);line-height:1;letter-spacing:-.02em;">
                        {{ number_format($totalImpressions) }}
                        <div style="font-size:.6rem;color:var(--color-gh-dim);font-weight:700;margin-top:.3rem;">TOTAL
                            IMPRESSIONS monthly </div>
                    </div>
                </div>
                <div
                    style="border:1px solid var(--color-gh-border);padding:1.25rem;border-radius:.6rem;text-align:center;display:flex;flex-direction:column;justify-content:center;min-height:90px;">
                    <div
                        style="font-size:.6rem;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;font-weight:800;margin-bottom:.5rem;">
                        Active Engagement</div>
                    <div style="font-size:1.85rem;font-weight:900;color:#fdb147;line-height:1;letter-spacing:-.02em;">
                        {{ number_format($totalClicks) }}
                        <div style="font-size:.6rem;color:var(--color-gh-dim);font-weight:700;margin-top:.3rem;">TOTAL
                            CLICKS monthly</div>
                    </div>
                </div>
            </div>

            {{-- Contact notice (Restored & Refined) --}}
            <div
                style="border:1px solid rgba(88,166,255,.2);background:rgba(88,166,255,.03);padding:1.1rem;border-radius:.6rem;height:100%;box-sizing:border-box;">
                <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.6rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)"
                        stroke-width="2.5">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                    <span
                        style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;">Advertising
                        Support</span>
                </div>
                <p style="font-size:.8rem;color:var(--color-gh-dim);margin:0 0 .5rem;line-height:1.4;">Need a custom
                    campaign or bulk discount?</p>
                <a href="mailto:{{ config('site.contact_email') }}"
                    style="display:inline-block;color:var(--color-gh-accent);font-weight:800;font-size:.85rem;text-decoration:none;border-bottom:1px solid rgba(88,166,255,.4);">
                    {{ config('site.contact_email') }}
                </a>
            </div>
        </div>

        {{-- Active Advertisers --}}
        @if($activeAds->count() > 0)
            <div style="margin-bottom:2rem;">
                <h2
                    style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);margin:0 0 .85rem;display:flex;align-items:center;gap:.5rem;">

                    Top Performing Campaigns {{ $totalActiveAdsCount }}
                </h2>
                <div style="display:flex;flex-direction:column;gap:.5rem;">
                    @foreach($activeAds as $activeAd)
                        <div
                            style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.75rem 1.1rem;display:flex;align-items:center;justify-content:space-between;gap:1.5rem;transition:border-color .2s;">
                            <div style="min-width:0;flex:1;">
                                <div
                                    style="font-size:.85rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:.3rem;">
                                    {{ $activeAd->title }}
                                </div>
                                <div
                                    style="font-size:.62rem;color:var(--color-gh-dim);display:flex;gap:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">
                                    <span style="display:flex;align-items:center;gap:.25rem;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="3" style="margin-top:-1px;">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        {{ $activeAd->starts_at?->format('d M Y') ?? 'Live' }}
                                    </span>
                                    <span style="display:flex;align-items:center;gap:.25rem;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#f87171"
                                            stroke-width="3" style="margin-top:-1px;">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                        </svg>
                                        {{ $activeAd->expires_at?->format('d M Y') ?? 'No Expire' }}
                                    </span>
                                </div>
                            </div>
                            <div style="display:flex;gap:1.25rem;align-items:center;flex-shrink:0;">
                                <div style="text-align:right;">
                                    <div style="font-size:.8rem;font-weight:900;color:var(--color-gh-accent);line-height:1.1;">
                                        {{ number_format($activeAd->total_impressions ?? 0) }}
                                    </div>
                                    <div
                                        style="font-size:.5rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.05em;">
                                        Views</div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:.8rem;font-weight:900;color:#fdb147;line-height:1.1;">
                                        {{ number_format($activeAd->total_clicks ?? 0) }}
                                    </div>
                                    <div
                                        style="font-size:.5rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.05em;">
                                        Clicks</div>
                                </div>
                                <a href="{{ route('ad.track', $activeAd->id) }}" target="_blank"
                                    style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);color:#fff;padding:.4rem .7rem;border-radius:.4rem;font-size:.62rem;font-weight:900;text-decoration:none;text-transform:uppercase;letter-spacing:.05em;display:flex;align-items:center;gap:.4rem;transition:all .2s;margin-left:.25rem;"
                                    onmouseover="this.style.borderColor='var(--color-gh-accent)';this.style.background='rgba(88,166,255,.1)';"
                                    onmouseout="this.style.borderColor='var(--color-gh-border)';this.style.background='var(--color-gh-btn-bg)';">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                    Visit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($totalActiveAdsCount > 3)
                    <div
                        style="margin-top:.85rem;text-align:center;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;border-top:1px dashed var(--color-gh-border);padding-top:.85rem;">
                        Load more ({{ $totalActiveAdsCount }} total campaigns)
                    </div>
                @endif
            </div>
        @endif

        {{-- ═══ Pricing Tiers ═══ --}}
        @if(!$ad)
            <div style="margin-bottom:1.5rem;">
                <h2
                    style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);margin:0 0 .75rem;">
                    Choose Your Package</h2>

                @php
                    $standardPackages = array_filter($packages, fn($p) => in_array($p->value, ['basic', 'standard', 'premium']));
                    $sponsoredPackages = array_filter($packages, fn($p) => in_array($p->value, ['sponsored_14', 'sponsored_30']));
                    $sidebarPackages = array_filter($packages, fn($p) => in_array($p->value, ['sidebar_14', 'sidebar_30']));

                    $packageGroups = [
                        'Header Banners' => $standardPackages,
                        'Sponsored Text Links' => $sponsoredPackages,
                        'Sidebar Banners' => $sidebarPackages
                    ];
                @endphp

                @foreach($packageGroups as $groupTitle => $groupPkgs)
                    @if(!empty($groupPkgs))
                        @if(!$loop->first)
                            <hr style="border:none;border-top:1px dashed rgba(48,54,61,.5);margin:1.25rem 0 .75rem;">
                        @endif
                        <h3
                            style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.15em;color:var(--color-gh-dim);margin:0 0 .6rem;">
                            {{ $groupTitle }}
                        </h3>
                        <div class="pkg-grid" style="{{ count($groupPkgs) == 2 ? 'grid-template-columns: repeat(2, 1fr);' : '' }}">
                            @foreach ($groupPkgs as $pkg)
                                @php
                                    $color = $pkg->badgeColor();
                                    $icons = [
                                        'basic' => '⚡',
                                        'standard' => '⭐',
                                        'premium' => '💎',
                                        'sponsored_14' => '🔗',
                                        'sponsored_30' => '🚀',
                                        'sidebar_14' => '📌',
                                        'sidebar_30' => '🔥'
                                    ];
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
                    @endif
                @endforeach
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
                            style="background:rgba(74,222,128,.1);color:#4ade80;border:1px solid rgba(74,222,128,.2);padding:.25rem .55rem;border-radius:.3rem;font-size:.68rem;font-weight:700;white-space:nowrap;">670
                            × 76 px</span>
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
                                style="color:#fff;font-weight:700;">Standard banner:</span> 670×76 px &middot;
                            PNG/GIF/WebP/JPG (GIF animation is preserved)</p>
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
                                    Max 2 MB &middot; PNG/JPG/WebP/GIF &middot; <strong style="color:#fff;">Auto-resized
                                        &amp; compressed (GIF animation preserved)</strong></div>

                                {{-- Live JS preview --}}
                                <div id="pub-banner-preview-wrap" style="display:none;margin-top:.5rem;">
                                    <p
                                        style="font-size:.6rem;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin:0 0 .2rem;">
                                        Preview</p>
                                    <img id="pub-banner-preview-img" alt="Banner preview"
                                        style="width:670px;max-width:100%;height:76px;object-fit:cover;border-radius:.35rem;border:1px solid var(--color-gh-accent);display:block;">
                                </div>
                                <script>
                                    (function () {
                                        var inp = document.getElementById('ad-banner');
                                        var wrap = document.getElementById('pub-banner-preview-wrap');
                                        var img = document.getElementById('pub-banner-preview-img');
                                        if (!inp) return;
                                        inp.addEventListener('change', function () {
                                            if (!this.files || !this.files[0]) return;
                                            var r = new FileReader();
                                            r.onload = function (e) { img.src = e.target.result; wrap.style.display = 'block'; };
                                            r.readAsDataURL(this.files[0]);
                                        });
                                    })();
                                </script>
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