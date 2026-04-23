<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email – {{ config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0d1117;
            font-family: ui-monospace, 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            color: #c9d1d9;
        }
        .wrap {
            max-width: 540px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .logo-row {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-title {
            font-size: 1.1rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -.02em;
        }
        .logo-sub {
            font-size: .6rem;
            color: #58a6ff;
            text-transform: uppercase;
            letter-spacing: .15em;
            margin-top: .2rem;
        }
        .card {
            border: 1px solid #30363d;
            border-radius: .6rem;
            overflow: hidden;
        }
        .card-head {
            background: #161b22;
            border-bottom: 1px solid #30363d;
            padding: 1rem 1.25rem;
        }
        .card-head-title {
            font-size: .65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #58a6ff;
            display: flex;
            align-items: center;
            gap: .4rem;
        }
        .card-body {
            padding: 1.5rem 1.25rem;
        }
        .greeting {
            font-size: .88rem;
            color: #fff;
            font-weight: 700;
            margin-bottom: .75rem;
        }
        .message {
            font-size: .78rem;
            line-height: 1.7;
            color: #8b949e;
            margin-bottom: 1.5rem;
        }
        .code-box {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: .5rem;
            padding: 1.25rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .code-label {
            font-size: .55rem;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: #8b949e;
            margin-bottom: .5rem;
        }
        .code-value {
            font-size: 2rem;
            font-weight: 900;
            color: #58a6ff;
            letter-spacing: .35em;
        }
        .divider {
            text-align: center;
            color: #30363d;
            font-size: .65rem;
            margin: 1.25rem 0;
        }
        .btn {
            display: block;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            background: #58a6ff;
            color: #0d1117;
            text-align: center;
            text-decoration: none;
            padding: .7rem 1rem;
            border-radius: .45rem;
            font-size: .72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .url-fallback {
            margin-top: 1rem;
            font-size: .6rem;
            color: #8b949e;
            word-break: break-all;
        }
        .warning-box {
            border: 1px solid rgba(248,113,113,.2);
            border-radius: .45rem;
            padding: .85rem 1rem;
            margin-top: 1.25rem;
        }
        .warning-title {
            font-size: .55rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #f87171;
            margin-bottom: .3rem;
        }
        .warning-text {
            font-size: .68rem;
            color: #8b949e;
            line-height: 1.6;
        }
        .footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: .6rem;
            color: #8b949e;
            line-height: 1.7;
        }
        .onion-badge {
            display: inline-block;
            background: rgba(74,222,128,.08);
            border: 1px solid rgba(74,222,128,.25);
            color: #4ade80;
            font-size: .55rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: .15rem .5rem;
            border-radius: .3rem;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo-row">
            <div class="logo-title">{{ config('app.name') }}</div>
            <div class="logo-sub">
                @if($isOnion)
                    Tor Hidden Service
                @else
                    Clearnet Mirror
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div class="card-head-title">
                    ✉ Email Verification Request
                    @if($isOnion)
                        &nbsp;<span class="onion-badge">Onion</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="greeting">Hello, {{ $username }}</div>
                <p class="message">
                    You registered on <strong style="color:#fff;">{{ config('app.name') }}</strong> and provided this email address for verification.
                    Use the code below to verify your email, or click the instant activation link.
                </p>

                {{-- OTP Code --}}
                <div class="code-box">
                    <div class="code-label">Your Verification Code</div>
                    <div class="code-value">{{ $verifyCode }}</div>
                </div>

                <div class="divider">— or click the link below —</div>

                {{-- Activation Button --}}
                <a href="{{ $verifyUrl }}" class="btn">Activate Account Instantly</a>

                <div class="url-fallback">
                    If the button above does not work, copy and paste this URL into your browser:<br>
                    <span style="color:#58a6ff;">{{ $verifyUrl }}</span>
                </div>

                <div class="warning-box">
                    <div class="warning-title">⚠ Security Notice</div>
                    <div class="warning-text">
                        This verification link expires in <strong style="color:#fff;">24 hours</strong>.
                        If you did not register on {{ config('app.name') }}, please disregard this email.
                        We will never ask for your password.
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            This message was sent by <strong style="color:#fff;">{{ config('app.name') }}</strong><br>
            You are receiving this because you (or someone else) signed up using this address.<br>
            <span style="opacity:.5;">No reply to this address — it is not monitored.</span>
        </div>
    </div>
</body>
</html>
