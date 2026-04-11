<x-app.layouts title="Bitcoin Payment">

    <style>
        .pay-layout { display: grid; grid-template-columns: 1fr; gap: 1rem; }
        @media (min-width: 640px) { .pay-layout { grid-template-columns: 1fr 300px; } }
        .pay-card { border: 1px solid var(--color-gh-border); border-radius: .5rem; overflow: hidden; }
        .pay-card-head { padding: .65rem 1rem; border-bottom: 1px solid var(--color-gh-border); display: flex; align-items: center; gap: .4rem; font-weight: 700; font-size: .82rem; color: #fff; }
        .step-num { width: 1.4rem; height: 1.4rem; border-radius: 50%; background: rgba(247,147,26,.15); color: #f7931a; display: inline-flex; align-items: center; justify-content: center; font-size: .62rem; font-weight: 800; border: 1px solid rgba(247,147,26,.2); flex-shrink: 0; }
    </style>

    <div style="max-width:860px;margin:0 auto;padding:1rem 0 3rem;">

        {{-- Header --}}
        <div style="margin-bottom:1.25rem;">
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .25rem;display:flex;align-items:center;gap:.5rem;">
                ₿ Bitcoin Payment
            </h1>
            <p style="color:var(--color-gh-dim);font-size:.82rem;margin:0;">Complete your payment to activate your ad campaign.</p>
        </div>

        {{-- Status bar --}}
        <div id="status-bar" style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;border-radius:.45rem;margin-bottom:1rem;border:1px solid rgba(88,166,255,.2);background:rgba(88,166,255,.07);transition:all .4s;">
            <span id="status-icon" style="font-size:1.25rem;flex-shrink:0;color:var(--color-gh-accent);">
                <span id="status-fa-icon">⏳</span>
            </span>
            <div>
                <div id="status-label" style="font-weight:700;font-size:.85rem;color:#fff;line-height:1.2;">Awaiting Payment</div>
                <div id="status-sub" style="font-size:.72rem;color:var(--color-gh-dim);margin-top:.15rem;">We are watching the blockchain for your payment.</div>
            </div>
        </div>

        {{-- Confirmed overlay (hidden by default) --}}
        <div id="confirmed-view" style="display:none;flex-direction:column;align-items:center;gap:.75rem;padding:2.5rem 1rem;text-align:center;">
            <div style="width:3.5rem;height:3.5rem;border-radius:50%;background:rgba(74,222,128,.12);border:2px solid rgba(74,222,128,.35);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#4ade80;">✓</div>
            <div style="font-size:1.25rem;font-weight:900;color:#fff;">Payment Confirmed!</div>
            <p style="font-size:.82rem;color:var(--color-gh-dim);max-width:320px;line-height:1.6;">Your Bitcoin payment has been confirmed. Our team will review and activate your ad within 24 hours.</p>
            <a href="{{ route('advertise.create') }}" style="padding:.55rem 1.25rem;background:var(--color-gh-accent);color:#0d1117;font-weight:800;border-radius:.4rem;text-decoration:none;font-size:.8rem;">+ Submit Another Ad</a>
        </div>

        <div id="payment-card" class="pay-layout">

            {{-- LEFT: Instructions & Address --}}
            <div class="pay-card">
                <div class="pay-card-head">₿ Send Exact Amount</div>
                <div style="padding:1rem;">

                    {{-- Ad summary --}}
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:.9rem;padding-bottom:.9rem;border-bottom:1px solid var(--color-gh-border);">
                        <div>
                            <div style="font-size:.62rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.2rem;">Ad Campaign</div>
                            <div style="font-size:.9rem;font-weight:700;color:#fff;">{{ $ad->title }}</div>
                            @if($ad->package_tier)
                                <div style="font-size:.72rem;color:var(--color-gh-dim);margin-top:.25rem;">
                                    {{ ucfirst($ad->package_tier) }} Package · <span style="color:#fff;">${{ $ad->price_usd }} USD</span>
                                </div>
                            @endif
                        </div>
                        <div style="font-family:monospace;font-size:.68rem;color:var(--color-gh-dim);background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);padding:.3rem .55rem;border-radius:.3rem;white-space:nowrap;flex-shrink:0;">
                            # {{ $payment->payment_ref }}
                        </div>
                    </div>

                    {{-- BTC Amount --}}
                    <div style="display:flex;align-items:baseline;gap:.75rem;margin-bottom:.75rem;">
                        <span style="font-size:2rem;font-weight:900;font-family:monospace;color:#f7931a;line-height:1;display:flex;align-items:center;gap:.35rem;">
                            ₿ {{ rtrim(number_format((float)$payment->amount_btc, 8, '.', ''), '0') }}
                        </span>
                        <span style="font-size:.85rem;color:var(--color-gh-dim);">≈ ${{ number_format((float)$payment->amount_usd, 2) }} USD</span>
                    </div>

                    {{-- Rate / Timer --}}
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.5rem 1rem;font-size:.68rem;color:rgba(125,133,144,.7);margin-bottom:.9rem;">
                        <span>🔒 Rate locked at <strong style="color:rgba(230,237,243,.8);">${{ number_format((float)$payment->btc_rate_snapshot, 0) }}/BTC</strong></span>
                        <span>·</span>
                        <span>⏱ Expires in <strong id="timer-val" style="font-family:monospace;color:rgba(230,237,243,.8);">24:00:00</strong></span>
                    </div>

                    {{-- Address --}}
                    <div style="margin-bottom:.75rem;">
                        <label style="display:block;font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.35rem;">₿ Bitcoin Address (BTC Mainnet only)</label>
                        <div id="btc-address-display" onclick="copyAddress()"
                             style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.65rem .75rem;font-family:monospace;font-size:.72rem;color:var(--color-gh-accent);word-break:break-all;cursor:pointer;position:relative;">
                            {{ $payment->btc_address ?: '—' }}
                            <span style="position:absolute;top:50%;right:.6rem;transform:translateY(-50%);background:rgba(247,147,26,.15);border:1px solid rgba(247,147,26,.3);color:#f7931a;border-radius:.25rem;padding:.15rem .5rem;font-size:.6rem;font-weight:800;">COPY</span>
                        </div>
                        <div id="copy-tip" style="font-size:.62rem;color:rgba(125,133,144,.55);font-style:italic;margin-top:.3rem;">Click address to copy to clipboard</div>
                    </div>

                    {{-- TX detected --}}
                    <div id="tx-box" style="display:none;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.65rem .75rem;margin-bottom:.75rem;">
                        <div style="font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.3rem;">🔗 Transaction ID</div>
                        <div id="tx-hash-val" style="font-family:monospace;font-size:.7rem;color:var(--color-gh-accent);word-break:break-all;cursor:pointer;" onclick="openTxExplorer()" title="View on mempool.space">—</div>
                    </div>

                    {{-- Steps --}}
                    <ul style="list-style:none;padding:0;margin:0 0 .75rem;display:flex;flex-direction:column;gap:.55rem;">
                        @foreach(['Open your Bitcoin wallet application','Scan the QR or paste the address manually','Enter the exact BTC amount shown above','Confirm and broadcast the transaction','This page updates automatically when payment is detected'] as $i => $step)
                            <li style="display:flex;align-items:flex-start;gap:.55rem;font-size:.75rem;color:var(--color-gh-dim);line-height:1.4;">
                                <span class="step-num">{{ $i + 1 }}</span>{{ $step }}
                            </li>
                        @endforeach
                    </ul>

                    {{-- Warning --}}
                    <div style="background:rgba(248,81,73,.05);border:1px solid rgba(248,81,73,.2);border-radius:.4rem;padding:.65rem .75rem;font-size:.72rem;color:var(--color-gh-dim);line-height:1.6;margin-bottom:.75rem;">
                        <strong style="color:#f85149;display:block;margin-bottom:.3rem;font-size:.62rem;text-transform:uppercase;letter-spacing:.08em;">⚠ Important</strong>
                        Send <span style="color:#fff;font-weight:700;text-decoration:underline;text-decoration-color:rgba(248,81,73,.4);">only Bitcoin (BTC)</span> to this address. Other assets will be permanently lost.
                        Include memo <code style="font-family:monospace;background:rgba(255,255,255,.06);padding:.1rem .3rem;border-radius:.2rem;">{{ $payment->payment_ref }}</code> if supported.
                    </div>

                    {{-- Poll badge --}}
                    <div id="poll-badge" style="display:flex;align-items:center;gap:.5rem;font-size:.62rem;color:var(--color-gh-dim);">
                        <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></span>
                        Monitoring blockchain · checks every 15s
                    </div>
                </div>
            </div>

            {{-- RIGHT: QR + Rate + Help --}}
            <div style="display:flex;flex-direction:column;gap:.75rem;">

                {{-- QR --}}
                <div class="pay-card">
                    <div class="pay-card-head">▣ Scan to Pay</div>
                    <div style="padding:1rem;display:flex;flex-direction:column;align-items:center;gap:.75rem;">
                        <div style="background:#fff;padding:.75rem;border-radius:.5rem;">
                            @if ($qrSvg)
                                <img src="{{ $qrSvg }}" alt="Bitcoin Payment QR Code" width="180" height="180" style="display:block;border-radius:.25rem;">
                            @else
                                <div style="width:180px;height:180px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;font-size:.75rem;color:#666;text-align:center;">
                                    <span style="font-size:1.5rem;">⚠</span>QR unavailable<br>Use address below
                                </div>
                            @endif
                        </div>
                        <div style="background:rgba(247,147,26,.1);border:1px solid rgba(247,147,26,.25);border-radius:.4rem;padding:.5rem .85rem;font-family:monospace;font-size:.85rem;font-weight:700;color:#f7931a;">
                            ₿ {{ rtrim(number_format((float)$payment->amount_btc, 8, '.', ''), '0') }}
                        </div>
                        <div style="font-size:.68rem;color:var(--color-gh-dim);text-align:center;line-height:1.5;">
                            Scan with your Bitcoin wallet.<br>
                            <span style="color:rgba(74,222,128,.7);">✓ BIP21 format · Address &amp; Amount</span>
                        </div>
                    </div>
                </div>

                {{-- Live Rate --}}
                <div class="pay-card">
                    <div class="pay-card-head" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;">₿ Live BTC Rate</div>
                    <div style="padding:.85rem 1rem;">
                        <div id="live-rate-val" style="font-size:1.5rem;font-weight:900;font-family:monospace;color:#fff;">
                            ${{ number_format((float)$payment->btc_rate_snapshot, 0) }}
                        </div>
                        <div style="font-size:.65rem;color:var(--color-gh-dim);margin-top:.25rem;">USD / BTC · refreshes 60s</div>
                        <div style="margin-top:.65rem;padding-top:.65rem;border-top:1px solid var(--color-gh-border);font-size:.7rem;color:var(--color-gh-dim);line-height:1.5;">
                            🔒 Your BTC amount is <strong style="color:#fff;">locked</strong> at ${{ number_format((float)$payment->btc_rate_snapshot, 0) }}. Rate changes won't affect your payment.
                        </div>
                    </div>
                </div>

                {{-- Help --}}
                <div class="pay-card">
                    <div style="padding:.85rem 1rem;">
                        <div style="font-size:.78rem;font-weight:700;color:#fff;margin-bottom:.35rem;">✉ Need help?</div>
                        <a href="mailto:treixnox@protonmail.com" style="color:var(--color-gh-accent);font-size:.75rem;font-weight:700;">treixnox@protonmail.com</a>
                        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.6rem;padding:.5rem .65rem;background:var(--color-gh-btn-bg);border-radius:.35rem;border:1px solid var(--color-gh-border);">
                            <span style="font-size:.65rem;color:var(--color-gh-dim);">Ref:</span>
                            <strong style="font-family:monospace;font-size:.72rem;color:rgba(230,237,243,.7);margin-left:auto;word-break:break-all;">{{ $payment->payment_ref }}</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<script>
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

const STATUS_CONFIG = {
    awaiting  : { icon:'⏳', bg:'rgba(88,166,255,.07)',  border:'rgba(88,166,255,.2)',  color:'var(--color-gh-accent)', sub:'Monitoring the blockchain for your payment.' },
    detected  : { icon:'👁',  bg:'rgba(247,147,26,.07)', border:'rgba(247,147,26,.25)', color:'#f7931a',                sub:'Payment detected! Waiting for confirmation.' },
    confirming: { icon:'⚙',  bg:'rgba(247,147,26,.1)',  border:'rgba(247,147,26,.35)', color:'#f7931a',                sub:'Transaction is being confirmed by the network.' },
    confirmed : { icon:'✅',  bg:'rgba(74,222,128,.07)', border:'rgba(74,222,128,.2)',  color:'#4ade80',                sub:'Payment confirmed! Your ad is under review.' },
    expired   : { icon:'⌛',  bg:'rgba(248,81,73,.07)',  border:'rgba(248,81,73,.2)',   color:'#f85149',                sub:'Payment window expired. Contact us if you already sent BTC.' },
    overpaid  : { icon:'⚠',  bg:'rgba(168,85,247,.07)', border:'rgba(168,85,247,.25)', color:'#a855f7',                sub:'Overpayment detected — please contact us.' },
};

function renderStatus(status, data) {
    const cfg = STATUS_CONFIG[status] || STATUS_CONFIG.awaiting;
    const bar = document.getElementById('status-bar');
    bar.style.background = cfg.bg;
    bar.style.borderColor = cfg.border;
    document.getElementById('status-icon').style.color = cfg.color;
    document.getElementById('status-fa-icon').textContent = cfg.icon;
    document.getElementById('status-label').textContent = data.label || (status.charAt(0).toUpperCase() + status.slice(1));
    let sub = cfg.sub;
    if (data.confirmations > 0) sub += ` (${data.confirmations} confirmation${data.confirmations > 1 ? 's' : ''})`;
    document.getElementById('status-sub').textContent = sub;
    if (data.tx_hash) {
        txHashKnown = data.tx_hash;
        document.getElementById('tx-box').style.display = 'block';
        document.getElementById('tx-hash-val').textContent = data.tx_hash;
    }
    if (status === 'confirmed') {
        document.getElementById('payment-card').style.display = 'none';
        const cv = document.getElementById('confirmed-view');
        cv.style.display = 'flex'; stopAll();
    }
    if (status === 'expired') {
        stopAll();
        document.getElementById('poll-badge').style.display = 'none';
    }
}

function updateTimer() {
    const diff = EXPIRES_AT - Date.now();
    const el = document.getElementById('timer-val');
    if (!el) return;
    if (diff <= 0) { el.textContent = 'Expired'; return; }
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    el.textContent = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
}

async function pollStatus() {
    try {
        const r = await fetch(STATUS_URL, { cache:'no-store' });
        const d = await r.json();
        if (d.status !== currentStatus) { currentStatus = d.status; renderStatus(d.status, d); }
    } catch(e) {}
}

function stopAll() {
    if (pollInterval)  { clearInterval(pollInterval);  pollInterval  = null; }
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
    if (rateInterval)  { clearInterval(rateInterval);  rateInterval  = null; }
}

function copyAddress() {
    const tip = document.getElementById('copy-tip');
    navigator.clipboard.writeText(BTC_ADDR).then(() => {
        tip.textContent = '✓ Copied to clipboard!'; tip.style.color = '#4ade80';
        setTimeout(() => { tip.textContent = 'Click address to copy'; tip.style.color = ''; }, 2500);
    }).catch(() => {
        const r = document.createRange(); r.selectNode(document.getElementById('btc-address-display'));
        window.getSelection().removeAllRanges(); window.getSelection().addRange(r);
    });
}

function openTxExplorer() { if (txHashKnown) window.open(TX_EXPLORER + txHashKnown, '_blank', 'noopener'); }

async function fetchLiveRate() {
    try {
        const r = await fetch(RATE_URL, { cache:'no-store' });
        const d = await r.json();
        if (d?.usd) document.getElementById('live-rate-val').textContent = '$' + Number(d.usd).toLocaleString('en-US', {maximumFractionDigits:0});
    } catch(e) {}
}

renderStatus(currentStatus, {
    label: @json($payment->statusLabel()),
    confirmations: {{ $payment->confirmations }},
    tx_hash: @json($payment->tx_hash ?? ''),
});

updateTimer();
timerInterval = setInterval(updateTimer, 1000);
if (!['confirmed','expired'].includes(currentStatus)) {
    pollStatus();
    pollInterval = setInterval(pollStatus, 15000);
}
fetchLiveRate();
rateInterval = setInterval(fetchLiveRate, 60000);
</script>

</x-app.layouts>
