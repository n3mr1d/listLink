<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Ad Request</title>
    <style>
        body { margin: 0; padding: 0; background: #0d1117; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 32px 16px; }
        .card { background: #161b22; border: 1px solid #30363d; border-radius: 8px; overflow: hidden; }
        .header { background: #1c2128; border-bottom: 1px solid #30363d; padding: 20px 24px; display: flex; align-items: center; gap: 12px; }
        .header-badge { background: rgba(248,81,73,.15); border: 1px solid rgba(248,81,73,.3); color: #f85149; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; padding: 3px 10px; border-radius: 20px; }
        .header-title { color: #e6edf3; font-size: 16px; font-weight: 700; margin: 0; }
        .body { padding: 24px; }
        .alert { background: rgba(247,147,26,.08); border: 1px solid rgba(247,147,26,.25); border-radius: 6px; padding: 14px 18px; margin-bottom: 20px; }
        .alert-text { color: #e6edf3; font-size: 13px; line-height: 1.6; margin: 0; }
        .section-label { font-size: 10px; font-weight: 800; color: #7d8590; text-transform: uppercase; letter-spacing: .12em; margin: 0 0 10px; }
        .detail-grid { background: #0d1117; border: 1px solid #30363d; border-radius: 6px; overflow: hidden; margin-bottom: 18px; }
        .detail-row { display: flex; border-bottom: 1px solid #21262d; }
        .detail-row:last-child { border-bottom: none; }
        .detail-key { width: 140px; min-width: 140px; padding: 10px 14px; font-size: 11px; font-weight: 700; color: #7d8590; text-transform: uppercase; letter-spacing: .06em; background: rgba(255,255,255,.02); }
        .detail-val { padding: 10px 14px; font-size: 13px; color: #e6edf3; word-break: break-all; }
        .detail-val a { color: #58a6ff; text-decoration: none; }
        .package-badge { display: inline-block; background: rgba(88,166,255,.12); border: 1px solid rgba(88,166,255,.25); color: #58a6ff; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px; }
        .status-badge { display: inline-block; background: rgba(248,81,73,.12); border: 1px solid rgba(248,81,73,.25); color: #f85149; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px; }
        .action-btn { display: inline-block; background: #58a6ff; color: #0d1117; font-size: 13px; font-weight: 800; padding: 10px 24px; border-radius: 6px; text-decoration: none; letter-spacing: .03em; }
        .footer { padding: 16px 24px; border-top: 1px solid #30363d; font-size: 11px; color: #7d8590; line-height: 1.5; }
        .mono { font-family: 'Courier New', monospace; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div>
                    <div class="header-badge">Admin Alert</div>
                    <p class="header-title" style="margin-top: 6px;">New Advertisement Request</p>
                </div>
            </div>

            <div class="body">
                <div class="alert">
                    <p class="alert-text">
                        &#9888; A new advertisement request has been submitted on <strong>{{ config('app.name') }}</strong>.
                        Please review it in the admin panel and approve or reject.
                    </p>
                </div>

                <p class="section-label">Advertiser Details</p>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-key">Contact</div>
                        <div class="detail-val">{{ $ad->contact_info }}</div>
                    </div>
                    @if($ad->user)
                    <div class="detail-row">
                        <div class="detail-key">Account</div>
                        <div class="detail-val">{{ $ad->user->username ?? $ad->user->email ?? 'N/A' }}</div>
                    </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-key">Submitted</div>
                        <div class="detail-val">{{ $ad->created_at->format('d M Y, H:i') }} UTC</div>
                    </div>
                </div>

                <p class="section-label">Advertisement Details</p>
                <div class="detail-grid">
                    <div class="detail-row">
                        <div class="detail-key">Title</div>
                        <div class="detail-val"><strong>{{ $ad->title }}</strong></div>
                    </div>
                    @if($ad->description)
                    <div class="detail-row">
                        <div class="detail-key">Description</div>
                        <div class="detail-val">{{ $ad->description }}</div>
                    </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-key">URL</div>
                        <div class="detail-val mono"><a href="{{ $ad->url }}">{{ $ad->url }}</a></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Type</div>
                        <div class="detail-val">{{ $ad->ad_type?->label() ?? $ad->ad_type }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Placement</div>
                        <div class="detail-val">{{ $ad->placement?->label() ?? $ad->placement }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Package</div>
                        <div class="detail-val">
                            @if($ad->package_tier)
                                <span class="package-badge">{{ ucfirst(str_replace('_', ' ', $ad->package_tier)) }}</span>
                            @else
                                <span style="color: #7d8590;">Not specified</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Price</div>
                        <div class="detail-val"><strong style="color: #f7931a;">${{ $ad->price_usd ?? 'N/A' }} USD</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-key">Status</div>
                        <div class="detail-val"><span class="status-badge">Pending Review</span></div>
                    </div>
                </div>

                <div style="text-align: center; padding-top: 8px;">
                    <a href="{{ url('/admin/ads') }}" class="action-btn">&#8594; Review in Admin Panel</a>
                </div>
            </div>

            <div class="footer">
                This is an automated notification from {{ config('app.name') }}. 
                Ad ID: <span class="mono">#{{ $ad->id }}</span> &middot; 
                Do not reply to this email.
            </div>
        </div>
    </div>
</body>
</html>
