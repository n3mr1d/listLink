<x-app.layouts title="Advertise">

    <div class="max-w-[980px] mx-auto">
        <div class="mb-6 pb-4 border-b border-gh-border">
            <h1 class="text-3xl font-bold text-white mb-1">Advertise on Hidden Line</h1>
            <p class="text-gh-dim">Promote your .onion service to a privacy-conscious audience — pay with Bitcoin.</p>
        </div>

        {{-- Contact Announcement --}}
        <div class="bg-blue-500/10 border border-blue-500/30 text-gh-text p-4 rounded-md mb-8 border-l-[4px] border-l-gh-accent">
            <div class="flex items-center gap-3">
                <span class="text-2xl"><i class="fa fa-envelope text-gh-accent"></i></span>
                <div>
                    <strong class="text-white block mb-1">Questions or custom packages?</strong>
                    <p class="m-0 text-sm">
                        Contact us directly:
                        <a href="mailto:treixnox@protonmail.com"
                            class="text-gh-accent font-bold underline">treixnox@protonmail.com</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══ Pricing Tiers ═══ --}}
        <div class="text-white font-bold text-lg mb-4 flex items-center gap-2">
            <i class="fa fa-tags text-gh-accent"></i> Choose Your Package
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-10" id="pricing-grid">
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
                <div class="bg-gh-bar-bg border border-gh-border rounded-xl flex flex-col overflow-hidden relative transition-all hover:-translate-y-1 hover:shadow-2xl {{ $pkg->isPopular() ? 'ring-1 ring-gh-accent border-gh-accent' : '' }} pricing-card"
                     data-tier="{{ $pkg->value }}"
                     data-price="{{ $pkg->priceUsd() }}"
                     data-label="{{ $pkg->label() }}">

                    @if ($pkg->isPopular())
                        <span class="absolute top-3 right-3 bg-gh-accent text-white text-[0.65rem] font-bold tracking-widest uppercase px-2 py-1 rounded-full shadow-sm">Most Popular</span>
                    @endif

                    <div class="p-5 border-b border-gh-border">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg mb-3" style="background: {{ $color }}22; color: {{ $color }};">
                            <i class="{{ $faIcon }}"></i>
                        </div>
                        <div class="text-white font-bold text-base mb-1">{{ $pkg->label() }}</div>
                        <div class="text-gh-dim text-xs">{{ $pkg->durationDays() }}-day campaign</div>

                        <div class="mt-3">
                            <div class="text-3xl font-extrabold text-white leading-none">
                                <span class="text-lg font-bold align-top mt-1 mr-0.5">$</span>{{ $pkg->priceUsd() }}
                            </div>
                            <div class="text-[0.7rem] font-mono text-orange-500 mt-1.5 min-h-[1.1em] opacity-80" id="btc-price-{{ $pkg->value }}">
                                <span class="opacity-50 italic">≈ fetching…</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 flex-grow">
                        <ul class="space-y-2">
                            @foreach ($pkg->features() as $feature)
                                <li class="flex items-start gap-2.5 text-xs text-gh-text-secondary leading-normal">
                                    <i class="fa fa-check text-green-500 mt-0.5 shrink-0"></i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="p-5 pt-0">
                        <button type="button"
                                class="w-full py-2.5 px-4 rounded-lg border border-gh-border bg-gh-bg text-gh-dim text-xs font-bold transition-all hover:bg-gh-accent hover:border-gh-accent hover:text-white btn-pick-package"
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

        <hr class="border-t border-gh-border my-8">

        {{-- ═══ Visual Ad Type Examples ═══ --}}
        <div class="text-white font-bold text-lg mb-6 flex items-center gap-2">
            <i class="fa fa-eye text-gh-accent"></i> Ad Types &amp; Placements
        </div>

        <div class="mb-8 space-y-6">
            {{-- 1. Header Banner --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden">
                <div class="p-4 border-b border-gh-border flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-white font-bold text-sm">Header Banner</h3>
                        <p class="text-gh-dim text-[0.7rem]">Full-width banner at the top of every page</p>
                    </div>
                    <span class="bg-green-500/10 text-green-500 border border-green-500/20 px-2 py-1 rounded text-[0.7rem] font-bold">
                        728 × 90 px
                    </span>
                </div>
                <div class="p-6">
                    <div class="relative rounded-lg overflow-hidden border border-gh-border bg-gradient-to-br from-[#1a2332] to-gh-bg min-h-[90px] flex items-center justify-center p-6">
                        <span class="absolute top-2 right-2 bg-black/70 text-gh-dim px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase">Sponsored</span>
                        <div class="text-center">
                            <div class="text-xl font-bold text-white mb-1">Your Service Name Here</div>
                            <div class="font-mono text-xs text-gh-accent">http://yourservice.onion</div>
                        </div>
                    </div>
                    <p class="text-xs text-gh-dim mt-4 flex items-center gap-2">
                        <span class="text-white font-bold">Available from:</span> Pro & Elite packages · 728×90px or 970×90px (PNG/WebP)
                    </p>
                </div>
            </div>

            {{-- 2. Sidebar Banner --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden">
                <div class="p-4 border-b border-gh-border flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-white font-bold text-sm">Sidebar Banner</h3>
                        <p class="text-gh-dim text-[0.7rem]">Sidebar placement visible on homepage</p>
                    </div>
                    <span class="bg-gh-accent/10 text-gh-accent border border-gh-accent/20 px-2 py-1 rounded text-[0.7rem] font-bold">
                        300 × 250 px
                    </span>
                </div>
                <div class="p-6">
                    <div class="max-w-[300px] mx-auto">
                        <div class="relative rounded-lg overflow-hidden border border-gh-border bg-gradient-to-b from-gh-bg to-black min-h-[250px] flex flex-col items-center justify-center p-4">
                            <span class="absolute top-2 right-2 bg-black/70 text-gh-dim px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase">Ad</span>
                            <div class="text-base font-bold text-white mb-1">Your Banner Ad</div>
                            <div class="text-xs text-gh-dim text-center mb-4 leading-relaxed">Promote your .onion service in the sidebar</div>
                            <div class="font-mono text-[0.65rem] text-gh-accent">http://example.onion</div>
                        </div>
                    </div>
                    <p class="text-xs text-gh-dim mt-4 text-center">
                        <span class="text-white font-bold">Available from:</span> Standard package and above
                    </p>
                </div>
            </div>

            {{-- 3. Sponsored Link --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden">
                <div class="p-4 border-b border-gh-border flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-white font-bold text-sm">Sponsored Link</h3>
                        <p class="text-gh-dim text-[0.7rem]">Appears within link listings with "Sponsored" label</p>
                    </div>
                    <span class="bg-purple-500/10 text-purple-500 border border-purple-500/20 px-2 py-1 rounded text-[0.7rem] font-bold">
                        Text Only
                    </span>
                </div>
                <div class="p-6">
                    <div class="max-w-[600px] overflow-hidden border border-gh-border rounded-lg">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gh-bg/50">
                                    <th class="px-4 py-2.5 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-2.5 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider w-[120px]">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gh-accent"><a href="#" class="hover:underline">Regular Link Example</a></td>
                                    <td class="px-4 py-3"><span class="bg-green-500/10 text-green-500 border border-green-500/20 px-2 py-0.5 rounded-full text-[0.65rem] font-bold">● Online</span></td>
                                </tr>
                                <tr class="bg-purple-500/5">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="bg-purple-500/10 text-purple-500 border border-purple-500/20 px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase">Ad</span>
                                            <a href="#" class="text-gh-accent font-bold hover:underline">Your Service Name Here</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3"><span class="bg-purple-500/10 text-purple-500 border border-purple-500/20 px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase">Sponsored</span></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gh-accent"><a href="#" class="hover:underline">Another Regular Link</a></td>
                                    <td class="px-4 py-3"><span class="bg-green-500/10 text-green-500 border border-green-500/20 px-2 py-0.5 rounded-full text-[0.65rem] font-bold">● Online</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gh-dim mt-4 flex items-center gap-2">
                        <span class="text-white font-bold">Available:</span> All packages · Text-based, no image required.
                    </p>
                </div>
            </div>
        </div>

        <hr class="border-t border-gh-border my-8">

        {{-- ═══ Submission Form ═══ --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-lg">
            <div class="p-5 border-b border-gh-border bg-white/5 text-white font-bold">Submit Ad Request</div>
            <div class="p-6">
                <form action="{{ route('advertise.store') }}" method="POST" enctype="multipart/form-data" id="ad-request-form">
                    @csrf

                    {{-- Honeypot --}}
                    <div class="hidden">
                        <label for="website_url_hp">Website</label>
                        <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off">
                    </div>

                    {{-- Hidden package tier field --}}
                    <input type="hidden" name="package_tier" id="form-package-tier" value="{{ old('package_tier') }}">

                    {{-- Selected package indicator --}}
                    <div id="selected-package-indicator" class="hidden mb-6 p-4 rounded-xl bg-gh-accent/5 border border-gh-accent/20">
                        <div class="text-[0.65rem] text-gh-dim uppercase tracking-wider font-bold mb-1">Selected Package</div>
                        <div class="text-gh-accent font-extrabold" id="selected-package-label">—</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex flex-col gap-1.5">
                                <label for="ad-title" class="text-xs font-bold text-gh-text-secondary">Ad Title *</label>
                                <input type="text" name="title" id="ad-title" value="{{ old('title') }}"
                                    class="bg-gh-bg border border-gh-border rounded-lg px-4 py-2.5 text-sm text-white focus:border-gh-accent focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/40"
                                    placeholder="Your service name" required minlength="3" maxlength="100">
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="ad-url" class="text-xs font-bold text-gh-text-secondary">.onion URL *</label>
                                <input type="text" name="url" id="ad-url" value="{{ old('url') }}"
                                    class="bg-gh-bg border border-gh-border rounded-lg px-4 py-2.5 text-sm text-white focus:border-gh-accent focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/40 font-mono"
                                    placeholder="http://yourservice.onion" required>
                                <div class="text-[0.65rem] text-gh-dim italic">Must be a valid .onion URL.</div>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="ad-type" class="text-xs font-bold text-gh-text-secondary">Ad Type *</label>
                                <select name="ad_type" id="ad-type" required
                                    class="bg-gh-bg border border-gh-border rounded-lg px-4 py-2.5 text-sm text-white focus:border-gh-accent focus:ring-1 focus:ring-gh-accent outline-none transition-all appearance-none cursor-pointer">
                                    @foreach ($adTypes as $type)
                                        <option value="{{ $type->value }}" {{ old('ad_type') === $type->value ? 'selected' : '' }}>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex flex-col gap-1.5">
                                <label for="ad-placement" class="text-xs font-bold text-gh-text-secondary">Preferred Placement *</label>
                                <select name="placement" id="ad-placement" required
                                    class="bg-gh-bg border border-gh-border rounded-lg px-4 py-2.5 text-sm text-white focus:border-gh-accent focus:ring-1 focus:ring-gh-accent outline-none transition-all appearance-none cursor-pointer">
                                    @foreach ($placements as $placement)
                                        <option value="{{ $placement->value }}" {{ old('placement') === $placement->value ? 'selected' : '' }}>
                                            {{ $placement->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="ad-banner" class="text-xs font-bold text-gh-text-secondary">Banner Image (optional)</label>
                                <input type="file" name="banner" id="ad-banner"
                                    class="bg-gh-bg border border-gh-border rounded-lg px-4 py-2 text-xs text-gh-dim file:mr-4 file:py-1 file:px-4 file:rounded-md file:border-0 file:text-[0.65rem] file:font-bold file:bg-white/10 file:text-white file:cursor-pointer hover:file:bg-white/20 transition-all"
                                    accept="image/png,image/jpg,image/jpeg,image/gif,image/webp">
                                <div class="text-[0.65rem] text-gh-dim leading-relaxed">
                                    Max 512KB. PNG, JPG, GIF, or WebP. Recommended: Header 728×90px · Sidebar 300×250px
                                </div>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label for="ad-contact" class="text-xs font-bold text-gh-text-secondary">Contact Information *</label>
                                <input type="text" name="contact_info" id="ad-contact" value="{{ old('contact_info') }}"
                                    class="bg-gh-bg border border-gh-border rounded-lg px-4 py-2.5 text-sm text-white focus:border-gh-accent focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/40"
                                    placeholder="Email, XMPP, or Session ID" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col items-center gap-6">
                        <div class="flex flex-col gap-2 items-center">
                            <label for="ad-challenge" class="text-xs font-bold text-gh-text-secondary">{{ $challenge }} *</label>
                            <input type="number" name="challenge" id="ad-challenge" required placeholder="?"
                                class="bg-gh-bg border border-gh-border rounded-lg px-4 py-2 w-24 text-center font-bold text-white focus:border-gh-accent focus:ring-1 focus:ring-gh-accent outline-none transition-all">
                            <div class="text-[0.6rem] text-gh-dim uppercase font-extrabold tracking-widest">Human Verification</div>
                        </div>

                        <button type="submit" class="w-full max-w-sm py-3.5 px-8 rounded-xl bg-gh-accent text-gh-bg font-extrabold text-sm uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-gh-accent/10">
                            Submit Advertisement Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ═══════════════════════════════════════════════════════
         BTC PAYMENT MODAL
    ═══════════════════════════════════════════════════════ --}}
    <div id="btc-modal-overlay" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-[9000] items-center justify-center p-4 transition-all duration-300 opacity-0 [&.open]:flex [&.open]:opacity-100" role="dialog" aria-modal="true" aria-labelledby="btc-modal-title">
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl max-w-[500px] w-full p-8 relative shadow-2xl transition-all duration-300 scale-95 opacity-0 [.open>&]:scale-100 [.open>&]:opacity-100">
            <button class="absolute top-4 right-4 text-gh-dim hover:text-white transition-colors p-2 rounded-lg hover:bg-white/5" onclick="closePaymentModal()" aria-label="Close">
                <i class="fa fa-times text-xl"></i>
            </button>

            <div class="flex items-center gap-3 mb-1">
                <i class="fab fa-bitcoin text-2xl text-[#f7931a]"></i>
                <h3 id="btc-modal-title" class="text-white font-bold text-lg">Bitcoin Payment</h3>
            </div>
            <span id="btc-modal-badge" class="inline-block px-2.5 py-1 rounded-md text-[0.7rem] font-bold mb-6">Package</span>

            {{-- Live BTC amount --}}
            <div class="bg-gh-bg border border-gh-border rounded-xl p-6 text-center mb-6">
                <div id="modal-btc-amount" class="text-3xl font-extrabold font-mono text-[#f7931a] tracking-tight">—</div>
                <div id="modal-usd-amount" class="text-gh-dim text-sm mt-1">$0 USD</div>
                <div class="flex items-center justify-center gap-2 mt-4 text-[0.65rem] text-gh-dim/60 italic">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    Rate: <span id="modal-btc-rate">fetching…</span> USD/BTC · updates every 60s
                </div>
            </div>

            {{-- BTC address --}}
            <div class="text-[0.7rem] font-bold text-gh-text-secondary uppercase tracking-wider mb-2">
                Send exact amount to this Bitcoin address:
            </div>
            <div id="btc-address-display" class="bg-gh-bg border border-gh-border rounded-lg p-4 font-mono text-xs text-gh-accent break-all cursor-pointer hover:border-gh-accent transition-colors mb-2" onclick="copyBtcAddress()" title="Click to copy">
                {{ config('services.btc_address', '1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf') }}
            </div>
            <div id="copy-hint-text" class="text-[0.6rem] text-gh-dim text-right mb-6">Click address to copy</div>

            <div class="bg-[#f7931a]/5 border border-[#f7931a]/20 rounded-xl p-5 mb-6">
                <p class="text-xs text-white font-bold mb-3 flex items-center gap-2">
                    <i class="fa fa-info-circle text-[#f7931a]"></i> How to pay:
                </p>
                <ol class="space-y-2.5 text-[0.72rem] text-gh-dim leading-relaxed list-decimal pl-4">
                    <li>Copy the Bitcoin address above</li>
                    <li>Open your BTC wallet and paste the address</li>
                    <li>Send the <strong class="text-white">exact</strong> BTC amount shown</li>
                    <li>Submit your ad request below — include your wallet TX hash in the contact field</li>
                </ol>
            </div>

            <button class="w-full py-4 rounded-xl bg-[#f7931a] text-white font-extrabold text-sm uppercase tracking-widest hover:brightness-110 active:scale-[0.98] transition-all shadow-lg shadow-[#f7931a]/20" onclick="proceedWithPackage()">
                <i class="fa fa-arrow-right mr-2"></i>Continue to Ad Form
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
            document.querySelectorAll('.btn-pick-package').forEach(b => {
                b.classList.remove('bg-gh-accent', 'border-gh-accent', 'text-white');
                b.classList.add('bg-gh-bg', 'border-gh-border', 'text-gh-dim');
            });
            const btn = document.querySelector(`.btn-pick-package[data-tier="${tier}"]`);
            if (btn) {
                btn.classList.remove('bg-gh-bg', 'border-gh-border', 'text-gh-dim');
                btn.classList.add('bg-gh-accent', 'border-gh-accent', 'text-white');
            }
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
                indicator.classList.remove('hidden');
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