<x-app.layouts title="Advertise">

    {{-- ══════════════════════════════════════════════════════
         PAGE-LEVEL STYLES
    ══════════════════════════════════════════════════════ --}}
    <style>
        /* ── Pricing Grid ── */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .pricing-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-secondary);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
        }

        .pricing-card.popular {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 1px var(--accent-blue), 0 4px 24px rgba(88, 166, 255, 0.12);
        }

        .pricing-card.popular:hover {
            box-shadow: 0 0 0 1px var(--accent-blue), 0 12px 36px rgba(88, 166, 255, 0.22);
        }

        .popular-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: var(--accent-blue);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.2rem 0.55rem;
            border-radius: 50px;
        }

        .pricing-header {
            padding: 1.2rem 1.25rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .tier-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 0.65rem;
        }

        .tier-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.2rem;
        }

        .tier-duration {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .tier-price {
            margin-top: 0.75rem;
        }

        .price-usd {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
        }

        .price-usd sup {
            font-size: 1rem;
            font-weight: 700;
            vertical-align: top;
            margin-top: 0.3rem;
        }

        .price-btc {
            font-size: 0.72rem;
            font-family: var(--font-mono);
            color: #f7931a;
            margin-top: 0.3rem;
            min-height: 1.1em;
        }

        .pricing-features {
            padding: 1rem 1.25rem;
            flex: 1;
        }

        .pricing-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pricing-features li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
            padding: 0.28rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .pricing-features li:last-child {
            border-bottom: none;
        }

        .feat-check {
            color: #3fb950;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .pricing-footer {
            padding: 1rem 1.25rem 1.25rem;
        }

        .btn-pick-package {
            display: block;
            width: 100%;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--bg-primary);
            color: var(--text-secondary);
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: background 0.15s, border-color 0.15s, color 0.15s;
        }

        .btn-pick-package:hover,
        .btn-pick-package.selected {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: #fff;
        }

        /* ── BTC Payment Modal ── */
        .btc-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            z-index: 9000;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .btc-modal-overlay.open {
            display: flex;
        }

        .btc-modal {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            max-width: 500px;
            width: 100%;
            padding: 2rem;
            position: relative;
            animation: modalPop 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalPop {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .btc-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.3rem;
            cursor: pointer;
            line-height: 1;
            padding: 0.2rem;
            border-radius: 4px;
            transition: color 0.15s;
        }

        .btc-modal-close:hover {
            color: var(--text-primary);
        }

        .btc-modal h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .btc-modal-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .btc-amount-box {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.25rem;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .btc-amount-display {
            font-size: 1.6rem;
            font-weight: 800;
            font-family: var(--font-mono);
            color: #f7931a;
            letter-spacing: -0.02em;
        }

        .btc-amount-usd {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .btc-rate-note {
            font-size: 0.68rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-style: italic;
        }

        .btc-address-box {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-family: var(--font-mono);
            font-size: 0.78rem;
            color: var(--accent-cyan);
            word-break: break-all;
            cursor: pointer;
            position: relative;
            transition: border-color 0.15s;
            margin-bottom: 0.5rem;
        }

        .btc-address-box:hover {
            border-color: var(--accent-blue);
        }

        .copy-hint {
            font-size: 0.68rem;
            color: var(--text-muted);
            text-align: right;
            margin-bottom: 1rem;
        }

        .btc-steps {
            background: rgba(247, 147, 26, 0.05);
            border: 1px solid rgba(247, 147, 26, 0.2);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
        }

        .btc-steps p {
            font-size: 0.72rem;
            color: var(--text-secondary);
            margin: 0 0 0.4rem 0;
            font-weight: 600;
        }

        .btc-steps ol {
            margin: 0;
            padding-left: 1.2rem;
            font-size: 0.72rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .rate-live-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #3fb950;
            border-radius: 50%;
            margin-right: 0.35rem;
            animation: pulseGreen 1.6s ease-in-out infinite;
        }

        @keyframes pulseGreen {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .btc-modal-proceed {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background: #f7931a;
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: opacity 0.15s;
        }

        .btc-modal-proceed:hover {
            opacity: 0.88;
        }

        /* ── Ad types section ── */
        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-divider {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 2rem 0;
        }
    </style>

    <div class="page-full" style="max-width:980px;">
        <div class="page-header">
            <h1>Advertise on Hidden Line</h1>
            <p>Promote your .onion service to a privacy-conscious audience — pay with Bitcoin.</p>
        </div>

        {{-- Contact Announcement --}}
        <div class="alert alert-info" style="margin-bottom:2rem; border-left: 4px solid var(--accent-blue);">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <span style="font-size:1.5rem;"><i class="fa fa-envelope"></i></span>
                <div>
                    <strong style="color:var(--text-primary); display:block; margin-bottom:0.25rem;">Questions or custom packages?</strong>
                    <p style="margin:0; font-size:0.9rem;">
                        Contact us directly:
                        <a href="mailto:treixnox@protonmail.com"
                            style="color:var(--accent-blue); font-weight:700; text-decoration:underline;">treixnox@protonmail.com</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══ Pricing Tiers ═══ --}}
        <div class="section-title">
            <i class="fa fa-tags"></i> Choose Your Package
        </div>

        <div class="pricing-grid" id="pricing-grid">
            @foreach ($packages as $pkg)
                @php
                    $color = $pkg->badgeColor();
                    $faIcons = [
                        'starter'  => 'fa fa-rocket',
                        'basic'    => 'fa fa-bolt',
                        'standard' => 'fa fa-star',
                        'premium'  => 'fa fa-gem',
                        'pro'      => 'fa fa-fire',
                        'elite'    => 'fa fa-crown',
                    ];
                    $faIcon = $faIcons[$pkg->value] ?? 'fa fa-box';
                @endphp
                <div class="pricing-card {{ $pkg->isPopular() ? 'popular' : '' }}"
                     data-tier="{{ $pkg->value }}"
                     data-price="{{ $pkg->priceUsd() }}"
                     data-label="{{ $pkg->label() }}">

                    @if ($pkg->isPopular())
                        <span class="popular-badge">Most Popular</span>
                    @endif

                    <div class="pricing-header">
                        <div class="tier-icon" style="background: {{ $color }}22; color: {{ $color }};">
                            <i class="{{ $faIcon }}"></i>
                        </div>
                        <div class="tier-name">{{ $pkg->label() }}</div>
                        <div class="tier-duration">{{ $pkg->durationDays() }}-day campaign</div>

                        <div class="tier-price">
                            <div class="price-usd"><sup>$</sup>{{ $pkg->priceUsd() }}</div>
                            <div class="price-btc" id="btc-price-{{ $pkg->value }}">
                                <span style="opacity:0.5;">≈ fetching…</span>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-features">
                        <ul>
                            @foreach ($pkg->features() as $feature)
                                <li>
                                    <i class="fa fa-check feat-check"></i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="pricing-footer">
                        <button type="button"
                                class="btn-pick-package"
                                data-tier="{{ $pkg->value }}"
                                data-price="{{ $pkg->priceUsd() }}"
                                data-label="{{ $pkg->label() }}"
                                onclick="openPaymentModal('{{ $pkg->value }}', {{ $pkg->priceUsd() }}, '{{ $pkg->label() }}', '{{ $color }}')">
                            Select {{ $pkg->label() }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="section-divider">

        {{-- ═══ Visual Ad Type Examples ═══ --}}
        <div class="section-title" style="margin-bottom:1rem;">
            <i class="fa fa-eye"></i> Ad Types &amp; Placements
        </div>

        <div style="margin-bottom:2rem;">
            {{-- 1. Header Banner --}}
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:0.9rem;margin-bottom:0.1rem;">Header Banner</h3>
                        <p style="font-size:0.7rem;color:var(--text-muted);font-weight:400;">Full-width banner at the top of every page</p>
                    </div>
                    <span style="padding:0.2rem 0.5rem;border-radius:4px;font-size:0.7rem;font-weight:700;background:rgba(63,185,80,0.15);color:var(--accent-green);border:1px solid rgba(63,185,80,0.3);">
                        728 × 90 px
                    </span>
                </div>
                <div class="card-body">
                    <div style="position:relative;border-radius:6px;overflow:hidden;border:1px solid var(--border-color);background:linear-gradient(135deg, #1a2332 0%, #0d1117 100%);min-height:90px;display:flex;align-items:center;justify-content:center;padding:1.5rem 1rem;">
                        <span style="position:absolute;top:0.4rem;right:0.4rem;background:rgba(0,0,0,0.7);color:var(--text-muted);padding:0.1rem 0.4rem;border-radius:3px;font-size:0.6rem;font-weight:700;text-transform:uppercase;">Sponsored</span>
                        <div style="text-align:center;">
                            <div style="font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:0.2rem;">Your Service Name Here</div>
                            <div style="font-family:var(--font-mono);font-size:0.75rem;color:var(--accent-cyan);">http://yourservice.onion</div>
                        </div>
                    </div>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;">
                        <strong style="color:var(--text-secondary);">Available from:</strong> Pro & Elite packages · 728×90px or 970×90px (PNG/WebP)
                    </p>
                </div>
            </div>

            {{-- 2. Sidebar Banner --}}
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:0.9rem;margin-bottom:0.1rem;">Sidebar Banner</h3>
                        <p style="font-size:0.7rem;color:var(--text-muted);font-weight:400;">Sidebar placement visible on homepage</p>
                    </div>
                    <span style="padding:0.2rem 0.5rem;border-radius:4px;font-size:0.7rem;font-weight:700;background:rgba(88,166,255,0.15);color:var(--accent-blue);border:1px solid rgba(88,166,255,0.3);">
                        300 × 250 px
                    </span>
                </div>
                <div class="card-body">
                    <div style="max-width:300px;margin:0 auto;">
                        <div style="position:relative;border-radius:6px;overflow:hidden;border:1px solid var(--border-color);background:linear-gradient(180deg, #161b22 0%, #0d1117 100%);min-height:250px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:1rem;">
                            <span style="position:absolute;top:0.4rem;right:0.4rem;background:rgba(0,0,0,0.7);color:var(--text-muted);padding:0.1rem 0.4rem;border-radius:3px;font-size:0.6rem;font-weight:700;text-transform:uppercase;">Ad</span>
                            <div style="font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:0.2rem;">Your Banner Ad</div>
                            <div style="font-size:0.75rem;color:var(--text-muted);text-align:center;margin-bottom:0.75rem;">Promote your .onion service in the sidebar</div>
                            <div style="font-family:var(--font-mono);font-size:0.65rem;color:var(--accent-cyan);">http://example.onion</div>
                        </div>
                    </div>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;text-align:center;">
                        <strong style="color:var(--text-secondary);">Available from:</strong> Standard package and above
                    </p>
                </div>
            </div>

            {{-- 3. Sponsored Link --}}
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:0.9rem;margin-bottom:0.1rem;">Sponsored Link</h3>
                        <p style="font-size:0.7rem;color:var(--text-muted);font-weight:400;">Appears within link listings with "Sponsored" label</p>
                    </div>
                    <span style="padding:0.2rem 0.5rem;border-radius:4px;font-size:0.7rem;font-weight:700;background:rgba(188,140,255,0.15);color:var(--accent-purple);border:1px solid rgba(188,140,255,0.3);">
                        Text Only
                    </span>
                </div>
                <div class="card-body">
                    <table class="links-table" style="max-width:600px;margin-bottom:0;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th style="width:100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="link-title"><a href="#">Regular Link Example</a></td>
                                <td><span class="uptime-badge uptime-online">● Online</span></td>
                            </tr>
                            <tr class="sponsored-row">
                                <td class="link-title">
                                    <span class="badge-sponsored">Ad</span>
                                    <a href="#">Your Service Name Here</a>
                                </td>
                                <td><span class="badge-sponsored">Sponsored</span></td>
                            </tr>
                            <tr>
                                <td class="link-title"><a href="#">Another Regular Link</a></td>
                                <td><span class="uptime-badge uptime-online">● Online</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;">
                        <strong style="color:var(--text-secondary);">Available:</strong> All packages · Text-based, no image required.
                    </p>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        {{-- ═══ Submission Form ═══ --}}
        <div class="card">
            <div class="card-header">Submit Ad Request</div>
            <div class="card-body">
                <form action="{{ route('advertise.store') }}" method="POST" enctype="multipart/form-data" id="ad-request-form">
                    @csrf

                    {{-- Honeypot --}}
                    <div class="hp-field">
                        <label for="website_url_hp">Website</label>
                        <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off">
                    </div>

                    {{-- Hidden package tier field --}}
                    <input type="hidden" name="package_tier" id="form-package-tier" value="{{ old('package_tier') }}">

                    {{-- Selected package indicator --}}
                    <div id="selected-package-indicator" style="display:none; margin-bottom:1rem; padding:0.75rem 1rem; border-radius:8px; background:rgba(88,166,255,0.08); border:1px solid rgba(88,166,255,0.25);">
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.2rem;">Selected Package</div>
                        <div style="font-weight:700; color:var(--text-primary);" id="selected-package-label">—</div>
                    </div>

                    <div class="form-group">
                        <label for="ad-title">Ad Title *</label>
                        <input type="text" name="title" id="ad-title" value="{{ old('title') }}"
                            placeholder="Your service name" required minlength="3" maxlength="100">
                    </div>

                    <div class="form-group">
                        <label for="ad-url">.onion URL *</label>
                        <input type="text" name="url" id="ad-url" value="{{ old('url') }}"
                            placeholder="http://yourservice.onion" required>
                        <div class="form-hint">Must be a valid .onion URL.</div>
                    </div>

                    <div class="form-group">
                        <label for="ad-type">Ad Type *</label>
                        <select name="ad_type" id="ad-type" required>
                            @foreach ($adTypes as $type)
                                <option value="{{ $type->value }}" {{ old('ad_type') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-hint">See examples above to understand each type.</div>
                    </div>

                    <div class="form-group">
                        <label for="ad-placement">Preferred Placement *</label>
                        <select name="placement" id="ad-placement" required>
                            @foreach ($placements as $placement)
                                <option value="{{ $placement->value }}" {{ old('placement') === $placement->value ? 'selected' : '' }}>
                                    {{ $placement->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ad-banner">Banner Image (optional)</label>
                        <input type="file" name="banner" id="ad-banner"
                            accept="image/png,image/jpg,image/jpeg,image/gif,image/webp">
                        <div class="form-hint">
                            Max 512KB. PNG, JPG, GIF, or WebP.<br>
                            Recommended sizes: Header 728×90px · Sidebar 300×250px · Featured 468×60px
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ad-contact">Contact Information *</label>
                        <input type="text" name="contact_info" id="ad-contact" value="{{ old('contact_info') }}"
                            placeholder="Email, XMPP, or Session ID for payment discussion" required>
                    </div>

                    <div class="form-group">
                        <label for="ad-challenge">{{ $challenge }} *</label>
                        <input type="number" name="challenge" id="ad-challenge" required placeholder="Your answer"
                            style="max-width:150px;">
                        <div class="form-hint">Anti-spam verification.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Submit Ad Request</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         BTC PAYMENT MODAL
    ═══════════════════════════════════════════════════════ --}}
    <div class="btc-modal-overlay" id="btc-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="btc-modal-title">
        <div class="btc-modal">
            <button class="btc-modal-close" onclick="closePaymentModal()" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>

            <div style="display:flex; align-items:center; gap:0.65rem; margin-bottom:0.3rem;">
                <i class="fab fa-bitcoin" style="font-size:1.5rem;color:#f7931a;"></i>
                <h3 id="btc-modal-title">Bitcoin Payment</h3>
            </div>
            <span class="btc-modal-badge" id="btc-modal-badge">Package</span>

            {{-- Live BTC amount --}}
            <div class="btc-amount-box">
                <div class="btc-amount-display" id="modal-btc-amount">—</div>
                <div class="btc-amount-usd" id="modal-usd-amount">$0 USD</div>
                <div class="btc-rate-note">
                    <span class="rate-live-dot"></span>
                    Rate: <span id="modal-btc-rate">fetching…</span> USD/BTC · updates every 60s
                </div>
            </div>

            {{-- BTC address --}}
            <div style="font-size:0.75rem; font-weight:600; color:var(--text-secondary); margin-bottom:0.4rem;">
                Send exact amount to this Bitcoin address:
            </div>
            <div class="btc-address-box" id="btc-address-display" onclick="copyBtcAddress()" title="Click to copy">
                {{ config('services.btc_address', '1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf') }}
            </div>
            <div class="copy-hint" id="copy-hint-text">Click address to copy</div>

            <div class="btc-steps">
                <p>How to pay:</p>
                <ol>
                    <li>Copy the Bitcoin address above</li>
                    <li>Open your BTC wallet and paste the address</li>
                    <li>Send the <strong>exact</strong> BTC amount shown</li>
                    <li>Submit your ad request below — include your wallet TX hash in the contact field</li>
                </ol>
            </div>

            <button class="btc-modal-proceed" onclick="proceedWithPackage()">
                <i class="fa fa-arrow-right" style="margin-right:0.4rem;"></i>Continue to Ad Request Form
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         JAVASCRIPT — BTC Rate + Modal Logic
    ═══════════════════════════════════════════════════════ --}}
    <script>
        // ── State ───────────────────────────────────────────
        let btcRateUsd      = null;
        let rateLastFetched = 0;
        let selectedTier    = null;
        let selectedPrice   = null;
        let selectedLabel   = null;
        let rateInterval    = null;

        const BTC_ADDR = document.getElementById('btc-address-display').textContent.trim();
        const RATE_URL = '{{ route("api.btc-rate") }}';

        // ── Fetch rate & update all price displays ───────────
        async function fetchBtcRate() {
            try {
                const res  = await fetch(RATE_URL, { cache: 'no-store' });
                const data = await res.json();
                if (data && data.usd) {
                    btcRateUsd      = data.usd;
                    rateLastFetched = Date.now();
                    updateAllPrices();
                    updateModalRate();
                }
            } catch (e) {
                console.warn('BTC rate fetch failed:', e);
            }
        }

        function usdToBtc(usd) {
            if (!btcRateUsd) return null;
            return usd / btcRateUsd;
        }

        function formatBtc(btc) {
            if (btc === null) return '—';
            return btc.toFixed(6) + ' BTC';
        }

        function updateAllPrices() {
            // Update every pricing card's BTC sub-price
            document.querySelectorAll('[id^="btc-price-"]').forEach(el => {
                const tier  = el.id.replace('btc-price-', '');
                const card  = document.querySelector(`.pricing-card[data-tier="${tier}"]`);
                const price = card ? parseInt(card.dataset.price) : null;
                if (price && btcRateUsd) {
                    el.innerHTML = formatBtc(usdToBtc(price));
                }
            });
        }

        function updateModalRate() {
            const rateEl = document.getElementById('modal-btc-rate');
            if (rateEl && btcRateUsd) {
                rateEl.textContent = '$' + btcRateUsd.toLocaleString('en-US', { maximumFractionDigits: 0 });
            }
            // If modal is open, refresh modal BTC amount too
            if (selectedPrice && btcRateUsd) {
                const btc = usdToBtc(selectedPrice);
                document.getElementById('modal-btc-amount').textContent = formatBtc(btc);
            }
        }

        // ── Modal ─────────────────────────────────────────────
        function openPaymentModal(tier, priceUsd, label, color) {
            selectedTier  = tier;
            selectedPrice = priceUsd;
            selectedLabel = label;

            // Update badge
            const badge = document.getElementById('btc-modal-badge');
            badge.textContent = label + ' — $' + priceUsd + ' USD';
            badge.style.background = color + '22';
            badge.style.color      = color;
            badge.style.border     = '1px solid ' + color + '44';

            // Update amounts
            document.getElementById('modal-usd-amount').textContent = '$' + priceUsd + ' USD';
            if (btcRateUsd) {
                document.getElementById('modal-btc-amount').textContent = formatBtc(usdToBtc(priceUsd));
            } else {
                document.getElementById('modal-btc-amount').textContent = 'Fetching rate…';
            }
            updateModalRate();

            // Show modal
            const overlay = document.getElementById('btc-modal-overlay');
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';

            // Highlight selected card button
            document.querySelectorAll('.btn-pick-package').forEach(b => b.classList.remove('selected'));
            const btn = document.querySelector(`.btn-pick-package[data-tier="${tier}"]`);
            if (btn) btn.classList.add('selected');
        }

        function closePaymentModal() {
            document.getElementById('btc-modal-overlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close on overlay click
        document.getElementById('btc-modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePaymentModal();
        });

        function proceedWithPackage() {
            if (selectedTier) {
                // Set hidden field + indicator in the form
                document.getElementById('form-package-tier').value = selectedTier;
                const indicator = document.getElementById('selected-package-indicator');
                const labelEl   = document.getElementById('selected-package-label');
                labelEl.textContent = selectedLabel + ' — $' + selectedPrice + ' USD';
                indicator.style.display = 'block';
            }
            closePaymentModal();
            // Scroll to form smoothly
            document.getElementById('ad-request-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // ── Copy address ──────────────────────────────────────
        function copyBtcAddress() {
            navigator.clipboard.writeText(BTC_ADDR).then(() => {
                const hint = document.getElementById('copy-hint-text');
                hint.textContent = '✓ Copied to clipboard!';
                hint.style.color = '#3fb950';
                setTimeout(() => {
                    hint.textContent = 'Click address to copy';
                    hint.style.color = '';
                }, 2500);
            }).catch(() => {
                // Fallback: select text
                const range = document.createRange();
                range.selectNode(document.getElementById('btc-address-display'));
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
            });
        }

        // ── Boot ──────────────────────────────────────────────
        fetchBtcRate();
        rateInterval = setInterval(fetchBtcRate, 60000); // refresh every 60s
    </script>

</x-app.layouts>