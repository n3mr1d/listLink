<x-app.layouts title="Bitcoin Payment">

    <style>
        .pay-layout { display: grid; grid-template-columns: 1fr; gap: 1rem; }
        @media (min-width: 640px) { .pay-layout { grid-template-columns: 1fr 300px; } }
        .pay-card { border: 1px solid var(--color-gh-border); border-radius: .5rem; overflow: hidden; background: rgba(13, 17, 23, 0.4); }
        .pay-card-head { padding: .65rem 1rem; border-bottom: 1px solid var(--color-gh-border); display: flex; align-items: center; gap: .4rem; font-weight: 700; font-size: .82rem; color: #fff; }
        .step-num { width: 1.4rem; height: 1.4rem; border-radius: 50%; background: rgba(247,147,26,.1); color: #f7931a; display: inline-flex; align-items: center; justify-content: center; font-size: .62rem; font-weight: 800; border: 1px solid rgba(247,147,26,.2); flex-shrink: 0; }
    </style>

    <div style="max-width:860px;margin:0 auto;padding:1rem 0 3rem;">

        {{-- Header --}}
        <div style="margin-bottom:1.25rem;">
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .25rem;display:flex;align-items:center;gap:.5rem;">
                ₿ Bitcoin Payment
            </h1>
            <p style="color:var(--color-gh-dim);font-size:.82rem;margin:0;">Complete your payment to activate your ad campaign.</p>
        </div>

        {{-- Session Flash Messages --}}
        @if(session('info'))
            <div style="display:flex;align-items:center;gap:.75rem;padding:.7rem 1rem;border-radius:.4rem;margin-bottom:1rem;border:1px solid rgba(88,166,255,.3);background:rgba(88,166,255,.07);font-size:.82rem;color:var(--color-gh-accent);font-weight:700;">
                ℹ {{ session('info') }}
            </div>
        @endif
        @if(session('success'))
            <div style="display:flex;align-items:center;gap:.75rem;padding:.7rem 1rem;border-radius:.4rem;margin-bottom:1rem;border:1px solid rgba(74,222,128,.3);background:rgba(74,222,128,.07);font-size:.82rem;color:#4ade80;font-weight:700;">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="display:flex;align-items:center;gap:.75rem;padding:.7rem 1rem;border-radius:.4rem;margin-bottom:1rem;border:1px solid rgba(248,81,73,.3);background:rgba(248,81,73,.07);font-size:.82rem;color:#f85149;font-weight:700;">
                ⚠ {{ session('error') }}
            </div>
        @endif

        {{-- Static Status Info --}}
        <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;border-radius:.45rem;margin-bottom:1rem;border:1px solid rgba(247,147,26,.2);background:rgba(247,147,26,.05);">
            <span style="font-size:1.25rem;flex-shrink:0;">⏳</span>
            <div>
                <div style="font-weight:700;font-size:.85rem;color:#fff;line-height:1.2;">Status: {{ $payment->statusLabel() }}</div>
                <div style="font-size:.72rem;color:var(--color-gh-dim);margin-top:.15rem;">Please complete the transfer and submit your TXID below.</div>
            </div>
        </div>

        <div class="pay-layout">

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

                    {{-- Rate --}}
                    <div style="font-size:.68rem;color:rgba(125,133,144,.7);margin-bottom:.9rem;">
                        <span>🔒 Rate locked at <strong style="color:rgba(230,237,243,.8);">${{ number_format((float)$payment->btc_rate_snapshot, 0) }}/BTC</strong></span>
                    </div>

                    {{-- Address --}}
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block;font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.35rem;">₿ Bitcoin Address (BTC Mainnet)</label>
                        <div id="btc-address-display" onclick="copyAddress()"
                             style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.65rem .75rem;font-family:monospace;font-size:.72rem;color:var(--color-gh-accent);word-break:break-all;cursor:pointer;position:relative;">
                            {{ $payment->btc_address ?: '—' }}
                            <span style="position:absolute;top:50%;right:.6rem;transform:translateY(-50%);background:rgba(247,147,26,.15);border:1px solid rgba(247,147,26,.3);color:#f7931a;border-radius:.25rem;padding:.15rem .5rem;font-size:.6rem;font-weight:800;">COPY</span>
                        </div>
                    </div>

                    {{-- Steps --}}
                    <ul style="list-style:none;padding:0;margin:0 0 1.5rem;display:flex;flex-direction:column;gap:.55rem;">
                        @foreach(['Open your Bitcoin wallet application','Scan the QR or paste the address manually','Enter the exact BTC amount shown above','Confirm and broadcast the transaction','Paste your Transaction ID (TXID) below for manual verification'] as $i => $step)
                            <li style="display:flex;align-items:flex-start;gap:.55rem;font-size:.75rem;color:var(--color-gh-dim);line-height:1.4;">
                                <span class="step-num">{{ $i + 1 }}</span>{{ $step }}
                            </li>
                        @endforeach
                    </ul>

                    {{-- Warning --}}
                    <div style="background:rgba(248,81,73,.05);border:1px solid rgba(248,81,73,.2);border-radius:.4rem;padding:.65rem .75rem;font-size:.72rem;color:var(--color-gh-dim);line-height:1.6;">
                        <strong style="color:#f85149;display:block;margin-bottom:.3rem;font-size:.62rem;text-transform:uppercase;letter-spacing:.08em;">⚠ Important</strong>
                        Send <span style="color:#fff;font-weight:700;text-decoration:underline;text-decoration-color:rgba(248,81,73,.4);">only Bitcoin (BTC)</span>. Other assets will be permanently lost.
                    </div>
                </div>
            </div>

            {{-- RIGHT: QR & Help --}}
            <div style="display:flex;flex-direction:column;gap:.75rem;">

                {{-- QR --}}
                <div class="pay-card">
                    <div class="pay-card-head">▣ Scan to Pay</div>
                    <div style="padding:1rem;display:flex;flex-direction:column;align-items:center;gap:.75rem;">
                        <div style="background:#fff;padding:.75rem;border-radius:.5rem;">
                            @if ($qrSvg)
                                <img src="{{ $qrSvg }}" alt="Bitcoin Payment QR Code" width="180" height="180" style="display:block;border-radius:.25rem;">
                            @else
                                <div style="width:180px;height:180px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.6rem;font-size:.7rem;color:#f87171;text-align:center;padding:1rem;background:rgba(248,113,113,0.05);border:1px dashed rgba(248,113,113,0.3);border-radius:.4rem;">
                                    <span style="font-size:1.6rem;margin-bottom:.2rem;">⚠</span>
                                    @if(empty($payment->btc_address))
                                        <div style="font-weight:800;text-transform:uppercase;letter-spacing:.05em;">Address Missing</div>
                                    @else
                                        <div style="font-weight:800;text-transform:uppercase;letter-spacing:.05em;">QR Unavailable</div>
                                        <div style="color:var(--color-gh-dim);font-size:.62rem;line-height:1.4;margin-top:.2rem;">Please manual transfer to address below.</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div style="font-size:.68rem;color:var(--color-gh-dim);text-align:center;line-height:1.5;">
                            Scan with your Bitcoin wallet.
                        </div>
                    </div>
                </div>

                {{-- Manual TXID Submission --}}
                <div class="pay-card">
                    <div class="pay-card-head" style="background:rgba(247,147,26,.06);">
                        &#8383; Submit TXID
                    </div>
                    <div style="padding:.85rem 1rem;">
                        <form action="{{ route('payment.submit-txid', $payment->id) }}" method="POST">
                            @csrf
                            <input type="text" name="txid"
                                   value="{{ old('txid', $payment->tx_hash ?? '') }}"
                                   placeholder="Transaction ID (TXID)"
                                   style="width:100%;box-sizing:border-box;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;color:#fff;font-size:.75rem;font-family:monospace;outline:none;margin-bottom:.6rem;"
                                   required minlength="10" maxlength="200">
                            <button type="submit"
                                    style="width:100%;background:rgba(247,147,26,.9);color:#0d1117;border:none;border-radius:.4rem;padding:.5rem;font-size:.75rem;font-weight:900;cursor:pointer;text-transform:uppercase;">
                                Submit Verification
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Help --}}
                <div class="pay-card">
                    <div style="padding:.85rem 1rem;">
                        <div style="font-size:.78rem;font-weight:700;color:#fff;margin-bottom:.35rem;">✉ Need help?</div>
                        <a href="mailto:{{ config('site.contact_email') }}" style="color:var(--color-gh-accent);font-size:.75rem;font-weight:700;">{{ config('site.contact_email') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function copyAddress() {
            const addr = @json($payment->btc_address);
            navigator.clipboard.writeText(addr).then(() => {
                const display = document.getElementById('btc-address-display');
                const original = display.innerHTML;
                display.style.borderColor = '#4ade80';
                display.innerHTML = 'COPIED TO CLIPBOARD!';
                setTimeout(() => {
                    display.style.borderColor = '';
                    display.innerHTML = original;
                }, 2000);
            });
        }
    </script>
</x-app.layouts>
