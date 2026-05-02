<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ad Request Received</title>
    <style>
        body { margin: 0; padding: 0; background: #0d1117; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 32px 16px; }
        .card { background: #161b22; border: 1px solid #30363d; border-radius: 8px; overflow: hidden; }
        .header { background: #1c2128; border-bottom: 1px solid #30363d; padding: 20px 24px; }
        .header-badge { display: inline-block; background: rgba(74,222,128,.12); border: 1px solid rgba(74,222,128,.25); color: #4ade80; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px; }
        .header-title { color: #e6edf3; font-size: 18px; font-weight: 800; margin: 0; letter-spacing: -.02em; }
        .header-sub { color: #7d8590; font-size: 13px; margin: 4px 0 0; }
        .body { padding: 24px; }
        .greeting { font-size: 14px; color: #e6edf3; line-height: 1.6; margin-bottom: 20px; }
        .section-label { font-size: 10px; font-weight: 800; color: #7d8590; text-transform: uppercase; letter-spacing: .12em; margin: 0 0 10px; }
        .detail-grid { background: #0d1117; border: 1px solid #30363d; border-radius: 6px; overflow: hidden; margin-bottom: 20px; }
        .detail-row { display: flex; border-bottom: 1px solid #21262d; }
        .detail-row:last-child { border-bottom: none; }
        .detail-key { width: 130px; min-width: 130px; padding: 10px 14px; font-size: 11px; font-weight: 700; color: #7d8590; text-transform: uppercase; letter-spacing: .06em; background: rgba(255,255,255,.02); }
        .detail-val { padding: 10px 14px; font-size: 13px; color: #e6edf3; word-break: break-all; }
        .payment-card { background: rgba(247,147,26,.05); border: 1px solid rgba(247,147,26,.25); border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .payment-title { color: #f7931a; font-size: 15px; font-weight: 800; margin: 0 0 14px; display: flex; align-items: center; gap: 8px; }
        .btc-address-box { background: #0d1117; border: 1px solid rgba(247,147,26,.3); border-radius: 6px; padding: 12px 16px; margin: 12px 0; }
        .btc-address-label { font-size: 10px; font-weight: 800; color: #7d8590; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 6px; }
        .btc-address-val { font-family: 'Courier New', monospace; font-size: 13px; color: #f7931a; word-break: break-all; font-weight: 700; }
        .amount-box { background: rgba(74,222,128,.06); border: 1px solid rgba(74,222,128,.2); border-radius: 6px; padding: 12px 16px; margin: 12px 0; }
        .amount-label { font-size: 10px; font-weight: 800; color: #7d8590; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 6px; }
        .amount-val { font-size: 20px; font-weight: 900; color: #4ade80; font-family: 'Courier New', monospace; }
        .step-list { list-style: none; padding: 0; margin: 14px 0 0; }
        .step-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #e6edf3; line-height: 1.5; padding: 6px 0; }
        .step-num { min-width: 22px; height: 22px; border-radius: 50%; background: rgba(247,147,26,.15); border: 1px solid rgba(247,147,26,.3); color: #f7931a; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; margin-top: 1px; }
        .warning-box { background: rgba(248,81,73,.06); border: 1px solid rgba(248,81,73,.2); border-radius: 6px; padding: 12px 16px; font-size: 12px; color: #7d8590; line-height: 1.6; margin-top: 14px; }
        .warning-title { color: #f85149; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
        .ref-badge { display: inline-block; background: #0d1117; border: 1px solid #30363d; font-family: 'Courier New', monospace; font-size: 12px; color: #e6edf3; padding: 4px 10px; border-radius: 4px; }
        .info-box { background: rgba(88,166,255,.06); border: 1px solid rgba(88,166,255,.2); border-radius: 6px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #e6edf3; line-height: 1.6; }
        .footer { padding: 16px 24px; border-top: 1px solid #30363d; font-size: 11px; color: #7d8590; line-height: 1.5; }
        .contact-link { color: #58a6ff; text-decoration: none; }
        .mono { font-family: 'Courier New', monospace; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="header-badge">&#10003; Received</div>
                <p class="header-title">Ad Request Received!</p>
                <p class="header-sub">{{ config('app.name') }} — Advertising Platform</p>
            </div>

            <div class="body">
                <p class="greeting">
                    Your advertisement request has been received and is now <strong>pending review</strong>.
                    To activate your campaign, please complete payment via Bitcoin using the instructions below.
                </p>

                <p class="section-label">Your Ad Summary</p>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-key">Title</div>
                        <div class="detail-val"><strong>{{ $ad->title }}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">URL</div>
                        <div class="detail-val mono">{{ $ad->url }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Package</div>
                        <div class="detail-val">{{ $ad->package_tier ? ucfirst(str_replace('_', ' ', $ad->package_tier)) : 'Standard' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Duration</div>
                        <div class="detail-val">
                            @if($ad->package_tier && $pkg = \App\Enum\AdPackage::tryFrom($ad->package_tier))
                                {{ $pkg->durationDays() }} days
                            @else
                                30 days
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Amount Due</div>
                        <div class="detail-val"><strong style="color: #f7931a;">${{ $ad->price_usd ?? 'N/A' }} USD</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Status</div>
                        <div class="detail-val" style="color: #fdb147; font-weight: 700;">&#9679; Awaiting Payment</div>
                    </div>
                </div>

                {{-- Payment Instructions --}}
                <div class="payment-card">
                    <p class="payment-title">&#8383; Bitcoin Payment Instructions</p>

                    <div class="amount-box">
                        <div class="amount-label">Amount to Pay</div>
                        <div class="amount-val">${{ $ad->price_usd ?? 'N/A' }} <span style="font-size: 14px; color: #7d8590;">USD</span></div>
                        <div style="font-size: 11px; color: #7d8590; margin-top: 4px;">Exact BTC equivalent shown on the payment page</div>
                    </div>

                    <div class="btc-address-box">
                        <div class="btc-address-label">&#8383; Send Bitcoin (BTC) to this address</div>
                        <div class="btc-address-val">{{ config('services.btc_address') ?: 'Contact admin for BTC address' }}</div>
                    </div>

                    <ul class="step-list">
                        <li class="step-item">
                            <span class="step-num">1</span>
                            Open your Bitcoin wallet and choose "Send".
                        </li>
                        <li class="step-item">
                            <span class="step-num">2</span>
                            Paste the BTC address above and enter the exact USD amount in BTC equivalent.
                        </li>
                        <li class="step-item">
                            <span class="step-num">3</span>
                            <span>Include your reference code in the transaction memo (if supported):
                                <span class="ref-badge">AD-{{ str_pad($ad->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </span>
                        </li>
                        <li class="step-item">
                            <span class="step-num">4</span>
                            After sending, copy your transaction ID (TXID) from your wallet.
                        </li>
                        <li class="step-item">
                            <span class="step-num">5</span>
                            <span>Visit your <a href="{{ url('/payment/' . $ad->id) }}" class="contact-link" style="font-weight:700;">payment page</a> and paste the TXID to confirm your payment.</span>
                        </li>
                    </ul>

                    <div class="warning-box">
                        <div class="warning-title">&#9888; Important</div>
                        Send <strong>only Bitcoin (BTC)</strong> to this address — other cryptocurrencies will be permanently lost.
                        Your ad will be activated within <strong>24 hours</strong> after payment confirmation.
                    </div>
                </div>

                <div class="info-box">
                    &#10139; Need help or have questions? Contact us at
                    <a href="mailto:{{ config('site.contact_email') }}" class="contact-link">{{ config('site.contact_email') }}</a>.
                    Please reference <strong>AD-{{ str_pad($ad->id, 6, '0', STR_PAD_LEFT) }}</strong> in your message.
                </div>
            </div>

            <div class="footer">
                This is an automated confirmation from <strong>{{ config('app.name') }}</strong>.<br>
                Ad Reference: <span class="mono">AD-{{ str_pad($ad->id, 6, '0', STR_PAD_LEFT) }}</span> &middot;
                Submitted: {{ $ad->created_at->format('d M Y, H:i') }} UTC
            </div>
        </div>
    </div>
</body>
</html>
