<x-app.layouts title="Bitcoin Payment">
<style>
    /* ─── Page Layout ────────────────────────────────────── */
    .pay-wrapper {
        max-width: 860px;
        margin: 0 auto;
        padding: 1.5rem 0 3rem;
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 720px) {
        .pay-wrapper { grid-template-columns: 1fr; }
        .pay-qr-col  { order: -1; }
    }

    /* ─── Cards ──────────────────────────────────────────── */
    .pay-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .pay-card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-primary);
    }

    .pay-card-header .fa, .pay-card-header .fab {
        width: 16px;
        text-align: center;
        color: #f7931a;
    }

    .pay-card-body { padding: 1.25rem; }

    /* ─── Status Bar ─────────────────────────────────────── */
    .pay-status-bar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
        border: 1px solid;
        transition: background 0.4s ease, border-color 0.4s ease;
    }

    .pay-status-bar.awaiting  { background: rgba(88,166,255,.08);  border-color: rgba(88,166,255,.25); }
    .pay-status-bar.detected  { background: rgba(247,147,26,.08);  border-color: rgba(247,147,26,.30); }
    .pay-status-bar.confirming{ background: rgba(247,147,26,.1);   border-color: rgba(247,147,26,.40); }
    .pay-status-bar.confirmed { background: rgba(63,185,80,.1);    border-color: rgba(63,185,80,.35);  }
    .pay-status-bar.expired   { background: rgba(248,81,73,.08);   border-color: rgba(248,81,73,.25);  }
    .pay-status-bar.overpaid  { background: rgba(188,140,255,.08); border-color: rgba(188,140,255,.3); }

    .pay-status-icon {
        font-size: 1.4rem;
        width: 36px;
        text-align: center;
        flex-shrink: 0;
    }

    .awaiting  .pay-status-icon { color: var(--accent-blue); }
    .detected  .pay-status-icon { color: #f7931a; }
    .confirming .pay-status-icon { color: #f7931a; }
    .confirmed .pay-status-icon { color: #3fb950; }
    .expired   .pay-status-icon { color: #f85149; }
    .overpaid  .pay-status-icon { color: #bc8cff; }

    .pay-status-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-primary);
    }

    .pay-status-sub {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 0.1rem;
    }

    /* ─── Amount ─────────────────────────────────────────── */
    .amount-row {
        display: flex;
        align-items: baseline;
        gap: 0.75rem;
        margin-bottom: 1.1rem;
    }

    .amount-btc-main {
        font-size: 2rem;
        font-weight: 800;
        font-family: var(--font-mono);
        color: #f7931a;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .amount-usd-sub {
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* ─── Address ────────────────────────────────────────── */
    .address-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.4rem;
    }

    .address-box {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem 3.2rem 0.75rem 0.9rem;
        font-family: var(--font-mono);
        font-size: 0.78rem;
        color: var(--accent-cyan);
        word-break: break-all;
        position: relative;
        cursor: pointer;
        transition: border-color 0.15s;
        margin-bottom: 0.35rem;
        line-height: 1.5;
    }

    .address-box:hover { border-color: #f7931a; }

    .address-copy-btn {
        position: absolute;
        top: 50%;
        right: 0.65rem;
        transform: translateY(-50%);
        background: rgba(247,147,26,.15);
        border: 1px solid rgba(247,147,26,.3);
        color: #f7931a;
        border-radius: 5px;
        padding: 0.22rem 0.5rem;
        font-size: 0.68rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
        white-space: nowrap;
    }

    .address-copy-btn:hover { background: rgba(247,147,26,.28); }

    .copy-tip {
        font-size: 0.68rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        min-height: 1em;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* ─── Ref badge ──────────────────────────────────────── */
    .ref-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 0.3rem 0.65rem;
        font-family: var(--font-mono);
        font-size: 0.78rem;
        color: var(--text-secondary);
        flex-shrink: 0;
    }

    /* ─── Timer ──────────────────────────────────────────── */
    .timer-val {
        font-family: var(--font-mono);
        color: var(--text-secondary);
        font-weight: 700;
    }

    .timer-val.urgent { color: #f85149; }

    /* ─── Steps ──────────────────────────────────────────── */
    .pay-steps {
        counter-reset: step;
        list-style: none;
        padding: 0;
        margin: 0 0 1.1rem 0;
    }

    .pay-steps li {
        counter-increment: step;
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        font-size: 0.78rem;
        color: var(--text-secondary);
        padding: 0.35rem 0;
        border-bottom: 1px solid rgba(255,255,255,.04);
    }

    .pay-steps li:last-child { border-bottom: none; }

    .pay-steps li::before {
        content: counter(step);
        background: rgba(247,147,26,.15);
        color: #f7931a;
        font-size: 0.65rem;
        font-weight: 800;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 0.05rem;
    }

    /* ─── QR Panel ───────────────────────────────────────── */
    .qr-box {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.85rem;
    }

    #qr-canvas-wrapper {
        background: #ffffff;
        padding: 14px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    #qr-canvas-wrapper img {
        display: block;
        border-radius: 4px;
        width: 196px;
        height: 196px;
    }

    .qr-fallback {
        width: 196px;
        height: 196px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: #f5f5f5;
        border-radius: 4px;
        color: #888;
        font-size: 0.72rem;
        text-align: center;
        padding: 1rem;
    }

    .qr-amount-tag {
        font-family: var(--font-mono);
        font-size: 0.82rem;
        color: #f7931a;
        font-weight: 700;
        background: rgba(247,147,26,.1);
        border: 1px solid rgba(247,147,26,.25);
        border-radius: 6px;
        padding: 0.3rem 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .qr-caption {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-align: center;
        line-height: 1.6;
    }

    /* ─── Network Pills ──────────────────────────────────── */
    .network-info {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }

    .net-pill {
        font-size: 0.65rem;
        padding: 0.2rem 0.55rem;
        border-radius: 50px;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        background: var(--bg-primary);
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* ─── Confirmed Overlay ──────────────────────────────── */
    .confirmed-overlay {
        display: none;
        flex-direction: column;
        align-items: center;
        gap: 0.85rem;
        padding: 2.5rem 1.5rem;
        text-align: center;
    }

    .confirmed-overlay.show { display: flex; }

    .confirmed-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: rgba(63,185,80,.15);
        border: 2px solid rgba(63,185,80,.45);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #3fb950;
        animation: popIn 0.4s cubic-bezier(0.34,1.56,0.64,1) both;
    }

    @keyframes popIn {
        from { transform: scale(0); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }

    /* ─── Poll Badge ─────────────────────────────────────── */
    .poll-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        color: var(--text-muted);
        margin-top: 0.6rem;
    }

    .poll-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3fb950;
        animation: pulseDot 1.8s ease-in-out infinite;
    }

    @keyframes pulseDot {
        0%,100% { opacity: 1; transform: scale(1); }
        50%      { opacity: 0.2; transform: scale(0.65); }
    }

    /* ─── TX Info ────────────────────────────────────────── */
    .tx-box {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.7rem 0.9rem;
        margin-top: 0.75rem;
        display: none;
    }

    .tx-box.show { display: block; }

    .tx-label {
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .tx-hash-val {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        color: var(--accent-cyan);
        word-break: break-all;
        cursor: pointer;
    }

    .tx-hash-val:hover { text-decoration: underline; }

    /* ─── Spinning cog for confirming ────────────────────── */
    .fa-spin { animation: spin 1.2s linear infinite; }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
</style>

<div class="page-full" style="max-width:900px;">
    <div class="page-header" style="margin-bottom:1.25rem;">
        <h1 style="font-size:1.4rem; display:flex; align-items:center; gap:0.6rem;">
            <i class="fab fa-bitcoin" style="color:#f7931a;"></i> Bitcoin Payment
        </h1>
        <p style="font-size:0.85rem;">Complete your payment to activate your ad campaign.</p>
    </div>

    <div class="pay-wrapper">

        {{-- ── LEFT — Instructions & Address ── --}}
        <div>
            {{-- Status bar --}}
            <div class="pay-status-bar awaiting" id="status-bar">
                <span class="pay-status-icon" id="status-icon">
                    <i class="fa fa-clock" id="status-fa-icon"></i>
                </span>
                <div>
                    <div class="pay-status-label" id="status-label">Awaiting Payment</div>
                    <div class="pay-status-sub" id="status-sub">We are watching the blockchain for your payment.</div>
                </div>
            </div>

            {{-- Confirmed overlay --}}
            <div class="confirmed-overlay" id="confirmed-view">
                <div class="confirmed-icon">
                    <i class="fa fa-check"></i>
                </div>
                <div style="font-size:1.15rem;font-weight:800;color:var(--text-primary);">Payment Confirmed!</div>
                <p style="font-size:0.82rem;color:var(--text-muted);max-width:320px;margin:0;">
                    Your Bitcoin payment has been confirmed on the blockchain.
                    Our team will review and activate your ad within 24 hours.
                </p>
                <a href="{{ route('advertise.create') }}" class="btn btn-primary" style="font-size:0.82rem;">
                    <i class="fa fa-plus" style="margin-right:0.35rem;"></i>Submit Another Ad
                </a>
            </div>

            {{-- Main payment card --}}
            <div class="pay-card" id="payment-card">
                <div class="pay-card-header">
                    <i class="fab fa-bitcoin"></i> Send Exact Amount
                </div>
                <div class="pay-card-body">

                    {{-- Ad summary --}}
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;margin-bottom:1rem;padding-bottom:0.9rem;border-bottom:1px solid var(--border-color);">
                        <div>
                            <div style="font-size:0.7rem;color:var(--text-muted);margin-bottom:0.15rem;display:flex;align-items:center;gap:0.3rem;">
                                <i class="fa fa-ad" style="font-size:0.65rem;"></i> Ad Campaign
                            </div>
                            <div style="font-size:0.9rem;font-weight:700;color:var(--text-primary);">{{ $ad->title }}</div>
                            @if($ad->package_tier)
                                <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.15rem;">
                                    <i class="fa fa-box" style="font-size:0.6rem;"></i>
                                    {{ ucfirst($ad->package_tier) }} Package &middot; ${{ $ad->price_usd }} USD
                                </div>
                            @endif
                        </div>
                        <div class="ref-badge">
                            <i class="fa fa-hashtag" style="font-size:0.65rem;opacity:0.5;"></i>
                            {{ $payment->payment_ref }}
                        </div>
                    </div>

                    {{-- BTC amount --}}
                    <div class="amount-row">
                        <span class="amount-btc-main">
                            <i class="fab fa-bitcoin"></i>
                            {{ rtrim(number_format((float)$payment->amount_btc, 8, '.', ''), '0') }}
                        </span>
                        <span class="amount-usd-sub">≈ ${{ number_format((float)$payment->amount_usd, 2) }} USD</span>
                    </div>

                    {{-- Rate / timer --}}
                    <div style="font-size:0.7rem;color:var(--text-muted);margin-bottom:1rem;display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap;">
                        <i class="fa fa-lock" style="font-size:0.65rem;"></i>
                        Rate locked at ${{ number_format((float)$payment->btc_rate_snapshot, 0) }}/BTC.
                        Expires in <strong id="timer-val" class="timer-val">24:00:00</strong>.
                    </div>

                    {{-- Payment address --}}
                    <div class="address-label">
                        <i class="fab fa-bitcoin" style="margin-right:0.2rem;color:#f7931a;"></i>
                        Bitcoin Address (BTC Mainnet only)
                    </div>
                    <div class="address-box" id="btc-address-display" onclick="copyAddress()">
                        {{ $payment->btc_address ?: '—' }}
                        <button class="address-copy-btn" tabindex="-1">
                            <i class="fa fa-copy" style="margin-right:0.2rem;"></i>COPY
                        </button>
                    </div>
                    <div class="copy-tip" id="copy-tip">
                        <i class="fa fa-hand-pointer" style="font-size:0.65rem;"></i>
                        Click address to copy to clipboard
                    </div>

                    {{-- TX detected --}}
                    <div class="tx-box" id="tx-box">
                        <div class="tx-label">
                            <i class="fa fa-link"></i> Transaction ID
                        </div>
                        <div class="tx-hash-val" id="tx-hash-val" onclick="openTxExplorer()" title="Click to view on mempool.space">—</div>
                    </div>

                    {{-- Steps --}}
                    <ul class="pay-steps" style="margin-top:1.1rem;">
                        <li>Open your Bitcoin wallet application</li>
                        <li>Scan the QR code <strong>or</strong> paste the address manually</li>
                        <li>Enter the <strong>exact BTC amount</strong> shown above</li>
                        <li>Confirm and broadcast the transaction</li>
                        <li>This page will update automatically when payment is detected</li>
                    </ul>

                    {{-- Warning --}}
                    <div style="background:rgba(248,81,73,.06);border:1px solid rgba(248,81,73,.2);border-radius:8px;padding:0.75rem;font-size:0.72rem;color:var(--text-muted);">
                        <strong style="color:#f85149;display:flex;align-items:center;gap:0.35rem;margin-bottom:0.3rem;">
                            <i class="fa fa-exclamation-triangle"></i> Important
                        </strong>
                        Send <strong>only Bitcoin (BTC)</strong> to this address.
                        Other assets sent here will be permanently lost.
                        Include memo <strong>{{ $payment->payment_ref }}</strong> if your wallet supports it.
                    </div>

                    {{-- Poll status --}}
                    <div class="poll-badge" id="poll-badge">
                        <div class="poll-dot"></div>
                        <i class="fa fa-satellite-dish" style="font-size:0.65rem;"></i>
                        Monitoring blockchain &middot; checks every 15s
                    </div>

                </div>
            </div>

            {{-- Network pills --}}
            <div class="network-info">
                <span class="net-pill"><i class="fa fa-link"></i> Bitcoin Mainnet</span>
                <span class="net-pill"><i class="fa fa-search"></i> mempool.space</span>
                <span class="net-pill"><i class="fa fa-clock"></i> ~10 min / confirmation</span>
            </div>
        </div>

        {{-- ── RIGHT — QR Code ── --}}
        <div class="pay-qr-col">
            <div class="pay-card">
                <div class="pay-card-header">
                    <i class="fa fa-qrcode"></i> Scan to Pay
                </div>
                <div class="qr-box">

                    {{-- QR code: generated server-side (SVG), no JavaScript/CDN needed --}}
                    <div id="qr-canvas-wrapper">
                        @if ($qrSvg)
                            <img src="{{ $qrSvg }}"
                                 alt="Bitcoin Payment QR Code"
                                 width="196" height="196"
                                 style="display:block;border-radius:4px;">
                        @else
                            <div class="qr-fallback">
                                <i class="fa fa-exclamation-circle" style="font-size:1.5rem;color:#f7931a;"></i>
                                <span>QR unavailable<br>Use address below</span>
                            </div>
                        @endif
                    </div>

                    <div class="qr-amount-tag">
                        <i class="fab fa-bitcoin"></i>
                        {{ rtrim(number_format((float)$payment->amount_btc, 8, '.', ''), '0') }}
                    </div>

                    <div class="qr-caption">
                        Scan with your Bitcoin wallet.<br>
                        <span style="display:inline-flex;align-items:center;gap:0.25rem;margin-top:0.2rem;">
                            <i class="fa fa-check-circle" style="color:#3fb950;font-size:0.7rem;"></i>
                            BIP21 format — includes address &amp; amount.
                        </span>
                    </div>
                </div>
            </div>

            {{-- Live rate --}}
            <div class="pay-card" style="margin-top:1rem;">
                <div class="pay-card-header" style="font-size:0.82rem;">
                    <i class="fab fa-bitcoin"></i> Live BTC Rate
                </div>
                <div class="pay-card-body" style="padding:0.85rem 1.1rem;">
                    <div style="font-size:1.15rem;font-weight:800;font-family:var(--font-mono);color:var(--text-primary);" id="live-rate-val">
                        ${{ number_format((float)$payment->btc_rate_snapshot, 0) }}
                    </div>
                    <div style="font-size:0.68rem;color:var(--text-muted);margin-top:0.2rem;display:flex;align-items:center;gap:0.3rem;">
                        <i class="fa fa-sync-alt" style="font-size:0.6rem;"></i>
                        USD / BTC &middot; refreshes every 60 s
                    </div>
                    <div style="margin-top:0.65rem;padding-top:0.65rem;border-top:1px solid var(--border-color);font-size:0.72rem;color:var(--text-muted);">
                        <i class="fa fa-lock" style="font-size:0.65rem;margin-right:0.2rem;color:#f7931a;"></i>
                        Your BTC amount is <strong style="color:var(--text-secondary);">locked</strong> at
                        ${{ number_format((float)$payment->btc_rate_snapshot, 0) }}.
                        Rate changes won't affect your payment.
                    </div>
                </div>
            </div>

            {{-- Help --}}
            <div class="pay-card" style="margin-top:1rem;">
                <div class="pay-card-body" style="padding:0.85rem 1.1rem;font-size:0.75rem;color:var(--text-muted);">
                    <strong style="color:var(--text-secondary);display:flex;align-items:center;gap:0.35rem;margin-bottom:0.4rem;">
                        <i class="fa fa-envelope"></i> Need help?
                    </strong>
                    <a href="mailto:treixnox@protonmail.com" style="color:var(--accent-blue);">treixnox@protonmail.com</a><br>
                    <span style="display:flex;align-items:center;gap:0.3rem;margin-top:0.4rem;">
                        <i class="fa fa-hashtag" style="font-size:0.65rem;"></i>
                        Reference: <strong style="font-family:var(--font-mono);color:var(--text-primary);">{{ $payment->payment_ref }}</strong>
                    </span>
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

// ── FA Icon map by status ─────────────────────────────────
const STATUS_CONFIG = {
    awaiting  : { faClass: 'fa fa-clock',              cls: 'awaiting',   sub: 'Monitoring the blockchain for your payment.' },
    detected  : { faClass: 'fa fa-eye',                cls: 'detected',   sub: 'Payment detected! Waiting for blockchain confirmation.' },
    confirming: { faClass: 'fa fa-cog fa-spin',        cls: 'confirming', sub: 'Transaction is being confirmed by the network.' },
    confirmed : { faClass: 'fa fa-check-circle',       cls: 'confirmed',  sub: 'Payment confirmed! Your ad is under review.' },
    expired   : { faClass: 'fa fa-hourglass-end',      cls: 'expired',    sub: 'Payment window expired. Contact us if you already sent BTC.' },
    overpaid  : { faClass: 'fa fa-exclamation-triangle', cls: 'overpaid', sub: 'Overpayment detected — please contact us.' },
};

function renderStatus(status, data) {
    const cfg     = STATUS_CONFIG[status] || STATUS_CONFIG.awaiting;
    const bar     = document.getElementById('status-bar');
    const iconEl  = document.getElementById('status-fa-icon');
    const labelEl = document.getElementById('status-label');
    const subEl   = document.getElementById('status-sub');

    // Swap class on bar
    Object.keys(STATUS_CONFIG).forEach(k => bar.classList.remove(k));
    bar.classList.add(cfg.cls);

    // Swap FA icon
    iconEl.className = cfg.faClass;

    labelEl.textContent = data.label || cfg.cls;
    subEl.textContent   = cfg.sub;

    // Confirmations
    if (data.confirmations > 0) {
        subEl.textContent += ` (${data.confirmations} confirmation${data.confirmations > 1 ? 's' : ''})`;
    }

    // TX hash
    if (data.tx_hash) {
        txHashKnown = data.tx_hash;
        document.getElementById('tx-box').classList.add('show');
        document.getElementById('tx-hash-val').textContent = data.tx_hash;
    }

    // Confirmed: show success screen
    if (status === 'confirmed') {
        document.getElementById('payment-card').style.display = 'none';
        document.getElementById('confirmed-view').classList.add('show');
        stopAll();
    }

    // Expired: stop polling
    if (status === 'expired') {
        stopAll();
        document.getElementById('poll-badge').style.display = 'none';
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
