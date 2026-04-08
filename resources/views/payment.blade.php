<x-app.layouts title="Bitcoin Payment">
    <div class="max-w-[900px] mx-auto px-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white flex items-center gap-3">
            <i class="fab fa-bitcoin text-[#f7931a]"></i> Bitcoin Payment
        </h1>
        <p class="text-gh-dim text-sm">Complete your payment to activate your ad campaign.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-[1fr_340px] gap-6 items-start">

        {{-- ── LEFT — Instructions & Address ── --}}
        <div>
            {{-- Status bar --}}
            <div id="status-bar" class="flex items-center gap-4 p-4 rounded-xl mb-5 border transition-all duration-400 awaiting bg-blue-500/10 border-blue-500/20">
                <span id="status-icon" class="text-2xl w-10 text-center shrink-0 text-gh-accent">
                    <i id="status-fa-icon" class="fa fa-clock"></i>
                </span>
                <div>
                    <div id="status-label" class="font-bold text-sm text-white leading-tight">Awaiting Payment</div>
                    <div id="status-sub" class="text-[0.7rem] text-gh-dim mt-0.5">We are watching the blockchain for your payment.</div>
                </div>
            </div>

            {{-- Confirmed overlay --}}
            <div id="confirmed-view" class="hidden flex-col items-center gap-4 p-10 text-center">
                <div class="w-20 h-20 rounded-full bg-green-500/15 border-2 border-green-500/40 flex items-center justify-center text-3xl text-green-500 animate-[popIn_0.4s_cubic-bezier(0.34,1.56,0.64,1)_both]">
                    <i class="fa fa-check"></i>
                </div>
                <div class="text-xl font-extrabold text-white">Payment Confirmed!</div>
                <p class="text-sm text-gh-dim max-w-[320px] mx-auto leading-relaxed">
                    Your Bitcoin payment has been confirmed on the blockchain. Our team will review and activate your ad within 24 hours.
                </p>
                <a href="{{ route('advertise.create') }}" class="mt-4 py-2.5 px-6 rounded-lg bg-gh-accent text-gh-bg font-bold text-sm hover:scale-105 transition-transform">
                    <i class="fa fa-plus mr-2"></i>Submit Another Ad
                </a>
            </div>

            {{-- Main payment card --}}
            <div id="payment-card" class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-xl">
                <div class="px-5 py-4 border-b border-gh-border bg-white/5 flex items-center gap-3 font-bold text-sm text-white">
                    <i class="fab fa-bitcoin text-[#f7931a]"></i> Send Exact Amount
                </div>
                <div class="p-6">
                    {{-- Ad summary --}}
                    <div class="flex justify-between items-start gap-4 mb-5 pb-5 border-b border-gh-border">
                        <div>
                            <div class="text-[0.65rem] text-gh-dim font-bold uppercase tracking-wider mb-1 flex items-center gap-2">
                                <i class="fa fa-ad text-[0.6rem]"></i> Ad Campaign
                            </div>
                            <div class="text-base font-bold text-white">{{ $ad->title }}</div>
                            @if($ad->package_tier)
                                <div class="text-xs text-gh-dim mt-1.5 flex items-center gap-2">
                                    <i class="fa fa-box text-[0.65rem]"></i>
                                    <span class="capitalize">{{ $ad->package_tier }}</span> Package &middot; <span class="text-gh-text-secondary">${{ $ad->price_usd }} USD</span>
                                </div>
                            @endif
                        </div>
                        <div class="bg-gh-bg/50 border border-gh-border rounded-lg px-3 py-1.5 font-mono text-xs text-gh-dim shrink-0 flex items-center gap-2">
                            <i class="fa fa-hashtag opacity-50 text-[0.65rem]"></i>
                            {{ $payment->payment_ref }}
                        </div>
                    </div>

                    {{-- BTC amount --}}
                    <div class="flex items-baseline gap-4 mb-5">
                        <span class="text-4xl font-extrabold font-mono text-[#f7931a] tracking-tight flex items-center gap-2.5">
                            <i class="fab fa-bitcoin text-2xl opacity-80"></i>
                            {{ rtrim(number_format((float)$payment->amount_btc, 8, '.', ''), '0') }}
                        </span>
                        <span class="text-base font-medium text-gh-dim">≈ ${{ number_format((float)$payment->amount_usd, 2) }} USD</span>
                    </div>

                    {{-- Rate / timer --}}
                    <div class="flex items-center gap-4 flex-wrap text-[0.7rem] text-gh-dim/70 mb-6 font-medium">
                        <span class="flex items-center gap-2">
                            <i class="fa fa-lock text-[#f7931a]"></i>
                            Rate locked at <span class="text-gh-text-secondary font-bold">${{ number_format((float)$payment->btc_rate_snapshot, 0) }}/BTC</span>
                        </span>
                        <span class="w-1 h-1 bg-gh-border rounded-full"></span>
                        <span class="flex items-center gap-2">
                            <i class="fa fa-clock"></i>
                            Expires in <strong id="timer-val" class="font-mono text-gh-text-secondary font-bold">24:00:00</strong>
                        </span>
                    </div>

                    {{-- Payment address --}}
                    <div class="mb-6">
                        <label class="block text-[0.65rem] font-extrabold text-gh-dim uppercase tracking-[0.1em] mb-2 px-1">
                            <i class="fab fa-bitcoin mr-1 text-[#f7931a]"></i>
                            Bitcoin Address (BTC Mainnet only)
                        </label>
                        <div id="btc-address-display" class="group bg-gh-bg border border-gh-border rounded-xl p-4 font-mono text-xs text-gh-accent break-all relative cursor-pointer hover:border-[#f7931a] transition-all" onclick="copyAddress()">
                            {{ $payment->btc_address ?: '—' }}
                            <button class="absolute top-1/2 right-3 -translate-y-1/2 bg-[#f7931a]/15 border border-[#f7931a]/30 text-[#f7931a] rounded-md px-2.5 py-1 text-[0.65rem] font-bold transition-all hover:bg-[#f7931a]/30" tabindex="-1">
                                <i class="fa fa-copy mr-1"></i>COPY
                            </button>
                        </div>
                        <div id="copy-tip" class="flex items-center gap-2 mt-2 px-1 text-[0.65rem] text-gh-dim/60 italic transition-colors">
                            <i class="fa fa-info-circle text-[0.6rem]"></i>
                            Click address to copy to clipboard
                        </div>
                    </div>

                    {{-- TX detected --}}
                    <div id="tx-box" class="hidden bg-gh-bg border border-gh-border rounded-xl p-4 mb-6 shadow-inner">
                        <div class="text-[0.65rem] font-extrabold text-gh-dim uppercase tracking-wider mb-2 flex items-center gap-2">
                            <i class="fa fa-link text-[#f7931a]"></i> Transaction ID
                        </div>
                        <div id="tx-hash-val" class="font-mono text-[0.7rem] text-gh-accent break-all cursor-pointer hover:underline" onclick="openTxExplorer()" title="Click to view on mempool.space">—</div>
                    </div>

                    {{-- Steps --}}
                    <ul class="space-y-3 mb-6">
                        @foreach([
                            'Open your Bitcoin wallet application',
                            'Scan the QR code or paste the address manually',
                            'Enter the exact BTC amount shown above',
                            'Confirm and broadcast the transaction',
                            'This page will update automatically when payment is detected'
                        ] as $index => $step)
                            <li class="flex items-start gap-4 text-xs text-gh-text-secondary leading-relaxed">
                                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-[#f7931a]/15 text-[#f7931a] text-[0.65rem] font-extrabold flex items-center justify-center border border-[#f7931a]/20">
                                    {{ $index + 1 }}
                                </span>
                                {{ $step }}
                            </li>
                        @endforeach
                    </ul>

                    {{-- Warning --}}
                    <div class="bg-red-500/5 border border-red-500/20 rounded-xl p-4 text-[0.72rem] text-gh-dim leading-relaxed mb-6">
                        <strong class="text-red-500 flex items-center gap-2 mb-1.5 font-bold uppercase tracking-wider text-[0.65rem]">
                            <i class="fa fa-exclamation-triangle"></i> Important
                        </strong>
                        <p>
                            Send <span class="text-white font-bold underline decoration-red-500/50">only Bitcoin (BTC)</span> to this address. Other assets will be permanently lost.
                            Include memo <span class="font-mono text-white bg-white/5 px-1 rounded">{{ $payment->payment_ref }}</span> if supported.
                        </p>
                    </div>

                    {{-- Poll status --}}
                    <div id="poll-badge" class="flex items-center gap-3 text-[0.65rem] text-gh-dim font-medium px-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="flex items-center gap-2">
                            <i class="fa fa-satellite-dish text-[0.6rem]"></i>
                            Monitoring blockchain &middot; checks every 15s
                        </span>
                    </div>

                </div>
            </div>

    {{-- ── RIGHT — QR Code ── --}}
    <div class="space-y-6">
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-xl">
            <div class="px-5 py-4 border-b border-gh-border bg-white/5 flex items-center gap-3 font-bold text-sm text-white">
                <i class="fa fa-qrcode text-[#f7931a]"></i> Scan to Pay
            </div>
            <div class="p-8 flex flex-col items-center gap-6">
                {{-- QR code: generated server-side (SVG) --}}
                <div class="bg-white p-4 rounded-2xl shadow-2xl flex items-center justify-center">
                    @if ($qrSvg)
                        <img src="{{ $qrSvg }}" alt="Bitcoin Payment QR Code" width="196" height="196" class="block rounded-lg">
                    @else
                        <div class="w-[196px] h-[196px] flex flex-col items-center justify-center gap-3 bg-gh-bg/10 text-gh-dim text-xs text-center p-6 rounded-lg">
                            <i class="fa fa-exclamation-circle text-2xl text-[#f7931a]"></i>
                            <span>QR unavailable<br>Use address below</span>
                        </div>
                    @endif
                </div>

                <div class="bg-[#f7931a]/10 border border-[#f7931a]/25 rounded-xl px-5 py-2.5 flex items-center gap-3 font-mono text-sm font-bold text-[#f7931a]">
                    <i class="fab fa-bitcoin"></i>
                    {{ rtrim(number_format((float)$payment->amount_btc, 8, '.', ''), '0') }}
                </div>

                <div class="text-[0.7rem] text-gh-dim text-center leading-relaxed">
                    Scan with your Bitcoin wallet.<br>
                    <span class="inline-flex items-center gap-2 mt-2 text-green-500/80 font-medium">
                        <i class="fa fa-check-circle text-[0.75rem]"></i>
                        BIP21 format · Address &amp; Amount
                    </span>
                </div>
            </div>
        </div>

        {{-- Live rate --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-lg">
            <div class="px-5 py-3.5 border-b border-gh-border bg-white/5 flex items-center gap-3 font-bold text-xs text-white uppercase tracking-wider">
                <i class="fab fa-bitcoin text-[#f7931a]"></i> Live BTC Rate
            </div>
            <div class="p-5">
                <div id="live-rate-val" class="text-2xl font-extrabold font-mono text-white tracking-tight">
                    ${{ number_format((float)$payment->btc_rate_snapshot, 0) }}
                </div>
                <div class="flex items-center gap-2 mt-2 text-[0.68rem] text-gh-dim">
                    <i class="fa fa-sync-alt animate-spin text-[0.6rem] [animation-duration:3s]"></i>
                    USD / BTC &middot; refreshes every 60s
                </div>
                <div class="mt-4 pt-4 border-t border-gh-border text-[0.7rem] text-gh-dim leading-relaxed">
                    <i class="fa fa-lock mr-1.5 text-[#f7931a]"></i>
                    Your BTC amount is <strong class="text-white">locked</strong> at
                    ${{ number_format((float)$payment->btc_rate_snapshot, 0) }}. Rate changes won't affect your payment.
                </div>
            </div>
        </div>

        {{-- Help --}}
        <div class="bg-gh-bar-bg/50 border border-gh-border rounded-xl p-5 shadow-lg">
            <div class="text-xs font-bold text-white flex items-center gap-2.5 mb-2.5">
                <i class="fa fa-envelope text-gh-accent"></i> Need help?
            </div>
            <a href="mailto:treixnox@protonmail.com" class="text-gh-accent text-[0.75rem] font-bold hover:underline">treixnox@protonmail.com</a>
            <div class="flex items-center gap-2 mt-3 p-2 bg-gh-bg/50 rounded-lg border border-gh-border/50 transition-colors hover:bg-gh-bg">
                <i class="fa fa-hashtag text-[0.6rem] text-gh-dim"></i>
                <span class="text-[0.7rem] text-gh-dim">Ref:</span>
                <strong class="font-mono text-[0.72rem] text-gh-text-secondary ml-auto">{{ $payment->payment_ref }}</strong>
            </div>
        </div>
    </div>
</div>

    </div>
</div>


<script>
// ── Constants ─────────────────────────────────────────────
const BIP21_URI   = @json($payment->bip21Uri());
const BTC_ADDR    = @json($payment->btc_address);
const EXPIRES_AT  = new Date(@json($payment->expires_at->toIso8601String()));
const STATUS_URL  = @json(route('payment.status', $payment->id));
const RATE_URL    = @json(route('api.btc-rate'));
const TX_EXPLORER = 'https://mempool.space/tx/';

let currentStatus = @json($payment->status);
let txHashKnown   = @json($payment->tx_hash ?? '');
let pollInterval  = null;
let rateInterval  = null;
let timerInterval = null;

// ── Status config by status ────────────────────────────────
const STATUS_CONFIG = {
    awaiting  : { fa: 'fa-clock',              bg: 'bg-blue-500/10',   border: 'border-blue-500/20',   text: 'text-gh-accent',  sub: 'Monitoring the blockchain for your payment.' },
    detected  : { fa: 'fa-eye',                bg: 'bg-orange-500/10', border: 'border-orange-500/30', text: 'text-[#f7931a]', sub: 'Payment detected! Waiting for blockchain confirmation.' },
    confirming: { fa: 'fa-cog animate-spin',    bg: 'bg-orange-500/15', border: 'border-orange-500/40', text: 'text-[#f7931a]', sub: 'Transaction is being confirmed by the network.' },
    confirmed : { fa: 'fa-check-circle',       bg: 'bg-green-500/10',  border: 'border-green-500/25',  text: 'text-green-500',  sub: 'Payment confirmed! Your ad is under review.' },
    expired   : { fa: 'fa-hourglass-end',      bg: 'bg-red-500/10',    border: 'border-red-500/20',    text: 'text-red-500',    sub: 'Payment window expired. Contact us if you already sent BTC.' },
    overpaid  : { fa: 'fa-exclamation-triangle', bg: 'bg-purple-500/10', border: 'border-purple-500/30', text: 'text-purple-400', sub: 'Overpayment detected — please contact us.' },
};

function renderStatus(status, data) {
    const cfg     = STATUS_CONFIG[status] || STATUS_CONFIG.awaiting;
    const bar     = document.getElementById('status-bar');
    const iconWrapper = document.getElementById('status-icon');
    const iconEl  = document.getElementById('status-fa-icon');
    const labelEl = document.getElementById('status-label');
    const subEl   = document.getElementById('status-sub');

    // Update Bar
    bar.className = `flex items-center gap-4 p-4 rounded-xl mb-5 border transition-all duration-500 ${cfg.bg} ${cfg.border}`;

    // Update Icon
    iconWrapper.className = `text-2xl w-10 text-center shrink-0 ${cfg.text}`;
    iconEl.className = `fa ${cfg.fa}`;

    labelEl.textContent = data.label || status.charAt(0).toUpperCase() + status.slice(1);
    subEl.textContent   = cfg.sub;

    // Confirmations
    if (data.confirmations > 0) {
        subEl.textContent += ` (${data.confirmations} confirmation${data.confirmations > 1 ? 's' : ''})`;
    }

    // TX hash
    if (data.tx_hash) {
        txHashKnown = data.tx_hash;
        document.getElementById('tx-box').classList.remove('hidden');
        document.getElementById('tx-hash-val').textContent = data.tx_hash;
    }

    // Confirmed: show success screen
    if (status === 'confirmed') {
        document.getElementById('payment-card').classList.add('hidden');
        document.getElementById('confirmed-view').classList.remove('hidden');
        document.getElementById('confirmed-view').classList.add('flex');
        stopAll();
    }

    // Expired: stop polling
    if (status === 'expired') {
        stopAll();
        document.getElementById('poll-badge').classList.add('hidden');
    }
}

// ── Countdown timer ───────────────────────────────────────
function updateTimer() {
    const diff = EXPIRES_AT - Date.now();
    const el   = document.getElementById('timer-val');
    if (!el) return;

    if (diff <= 0) {
        el.textContent = 'Expired';
        el.classList.add('urgent');
        return;
    }

    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    el.textContent = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
    el.classList.toggle('urgent', diff < 3600000);
}

// ── Blockchain poll ───────────────────────────────────────
async function pollStatus() {
    try {
        const res  = await fetch(STATUS_URL, { cache: 'no-store' });
        const data = await res.json();
        if (data.status !== currentStatus) {
            currentStatus = data.status;
            renderStatus(data.status, data);
        }
    } catch(e) { console.warn('Poll failed:', e); }
}

function stopAll() {
    if (pollInterval)  { clearInterval(pollInterval);  pollInterval  = null; }
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
    if (rateInterval)  { clearInterval(rateInterval);  rateInterval  = null; }
}

// ── Copy address ──────────────────────────────────────────
function copyAddress() {
    const tip = document.getElementById('copy-tip');
    navigator.clipboard.writeText(BTC_ADDR).then(() => {
        tip.innerHTML = '<i class="fa fa-check-circle" style="color:#3fb950;font-size:0.65rem;"></i> Copied to clipboard!';
        tip.style.color = '#3fb950';
        setTimeout(() => {
            tip.innerHTML = '<i class="fa fa-hand-pointer" style="font-size:0.65rem;"></i> Click address to copy to clipboard';
            tip.style.color = '';
        }, 2500);
    }).catch(() => {
        const range = document.createRange();
        range.selectNode(document.getElementById('btc-address-display'));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    });
}

// ── TX Explorer ───────────────────────────────────────────
function openTxExplorer() {
    if (txHashKnown) window.open(TX_EXPLORER + txHashKnown, '_blank', 'noopener');
}

// ── Live BTC rate ─────────────────────────────────────────
async function fetchLiveRate() {
    try {
        const res  = await fetch(RATE_URL, { cache: 'no-store' });
        const data = await res.json();
        if (data?.usd) {
            document.getElementById('live-rate-val').textContent =
                '$' + Number(data.usd).toLocaleString('en-US', { maximumFractionDigits: 0 });
        }
    } catch(e) { /* silent */ }
}

// ── Boot ──────────────────────────────────────────────────
renderStatus(currentStatus, {
    label        : @json($payment->statusLabel()),
    confirmations: {{ $payment->confirmations }},
    tx_hash      : @json($payment->tx_hash ?? ''),
});

updateTimer();
timerInterval = setInterval(updateTimer, 1000);

if (!['confirmed', 'expired'].includes(currentStatus)) {
    pollStatus();
    pollInterval = setInterval(pollStatus, 15000);
}

fetchLiveRate();
rateInterval = setInterval(fetchLiveRate, 60000);
</script>

</x-app.layouts>
