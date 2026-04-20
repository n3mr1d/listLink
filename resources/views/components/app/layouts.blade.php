<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO & Standard Meta --}}
    @php
        $totalActiveNodes = \App\Models\Link::active()->count();
        $defaultDescription = config('app.name') . " — The elite privacy-focused directory for the Tor network. Explore " . number_format($totalActiveNodes) . "+ verified .onion services with high-precision uptime monitoring and deep indexing. No JavaScript. No tracking. Pure performance.";
    @endphp
    <meta name="description" content="{{ $description ?? $defaultDescription }}">
    <meta name="keywords"
        content="tor directory, onion links, darknet search, privacy, anonymity, hidden services, verified onion">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">

    {{-- Geo Tags --}}
    <meta name="geo.region" content="ID" />
    <meta name="geo.placename" content="Jakarta" />

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Verified Tor Directory' }} | {{ config('app.name') }}">
    <meta property="og:description"
        content="{{ $description ?? 'Discover verified .onion services on the Tor network. Secure, private, and always updated.' }}">
    <meta property="og:image" content="{{ asset('favicon-32x32.png') }}">

    {{-- Tor Integration --}}
    <meta http-equiv="onion-location" content="{{config("app.url")}}" />
    <title>{{ $title ?? 'Directory' }} - {{ config('app.name') }}</title>

    {{-- AdMate System --}}
    <script src="http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion/js/get-banners.js"></script>
    <script>
        async function getBanners(url) {
            const domain = window.location.hostname;

            if (!url.endsWith('/')) url += '/';
            url += domain;

            if (typeof url === 'string' && url.includes('admate3wrcqo2qeuok36b4wncwv7k6deei6riq2w62s36htgyahsaaqd')) {
                url = url.replace(
                    /admate3wrcqo2qeuok36b4wncwv7k6deei6riq2w62s36htgyahsaaqd/g,
                    'admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd'
                );
            }

            let type = '468-60';
            let count = 10;

            const typeMatch = url.match(/\/type\/([0-9\-]+)/);
            if (typeMatch) {
                type = typeMatch[1];
            }

            const countMatch = url.match(/\/count\/(\d+)/);
            if (countMatch) {
                count = Math.min(10, Math.max(1, parseInt(countMatch[1], 10)));
            }

            let idPrefix = 'banner-place-468-';
            let width = 468;
            let height = 60;

            if (type === '285-200') {
                idPrefix = 'banner-place-285-';
                width = 285;
                height = 200;
            }

            try {
                const response = await fetch(url, { cache: 'no-store', credentials: 'same-origin' });
                if (!response.ok) {
                    throw new Error('Failed to fetch banners JSON');
                }

                const data = await response.json();
                if (!Array.isArray(data)) {
                    throw new Error('Invalid JSON format');
                }

                for (let i = 1; i <= count; i++) {
                    const banner = data[i - 1]; // Index 0-based for data
                    if (!banner || !banner.src) continue;

                    const container = document.getElementById(`${idPrefix}${i}`);
                    if (!container) continue;

                    const bannerUrl = banner.src;
                    const bannerAlt = banner.alt || 'Banner';
                    const bannerHref = banner.href || '#';

                    container.innerHTML = `
<div style="position: relative; width: ${width}px; height: ${height}px; overflow: hidden; display: inline-block; margin: 5px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 15px rgba(0,0,0,0.5); background: #000;">
    <a href="${bannerHref}" target="_blank" rel="noopener noreferrer" class="adm-banner-link">
        <img src="${bannerUrl}" alt="${bannerAlt}" width="${width}" height="${height}" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: contain; display: block;">
    </a>
    <a href="http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion" target="_blank" style="position: absolute; top: 0; right: 0; background: rgba(0,0,0,0.7); color: #fff; padding: 2px 6px; font-size: 10px; text-decoration: none; border-bottom-left-radius: 6px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">AdMate</a>
</div>
`;
                }
            } catch (err) {
                console.error('getBanners error:', err);
            }
        }
    </script>

    {{-- ONE stylesheet only, no CDN fonts or icon libs --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Mobile nav toggle ── */
        #nav-toggle {
            display: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-wrap: wrap;
            }

            .nav-links {
                display: none;
                flex-direction: column;
                align-items: stretch;
                gap: 0;
                padding: 0.5rem 0 1rem;
                width: 100%;
                order: 3;
            }

            .nav-links a,
            .nav-links form {
                padding: .75rem 0;
                border-bottom: 1px solid rgba(48, 54, 61, 0.5);
                width: 100%;
            }

            .nav-links a:last-child {
                border-bottom: none;
            }

            #nav-toggle:checked~.nav-links {
                display: flex;
            }

            .hamburger-btn {
                display: flex !important;
            }

            .header-search {
                display: none;
            }
        }

        .hamburger-btn {
            display: none;
            cursor: pointer;
            padding: .35rem;
            transition: opacity 0.2s;
        }

        .hamburger-btn:hover {
            opacity: 0.7;
        }

        /* ── Header search responsive ── */
        .header-search {
            flex-grow: 1;
            max-width: 400px;
            margin: 0 1rem;
        }
    </style>
</head>

<body class="bg-gh-bg text-gh-text font-sans m-0 flex flex-col min-h-screen overflow-x-hidden">

    {{-- ═══ Header ═══ --}}
    <header
        style="position:sticky;top:0;z-index:100;background:var(--color-gh-bg);border-bottom:1px solid var(--color-gh-border);">
        <div style="max-width:1100px;margin:0 auto;padding:.65rem 1rem;">
            {{-- Top row: logo + search + hamburger --}}
            <div class="nav-container" style="display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:.6rem;">
                    <a href="{{ route('home') }}"
                        style="display:flex;align-items:center;gap:.5rem;text-decoration:none;color:#fff;font-weight:800;font-size:1rem;flex-shrink:0;">
                        <x-app.logo style="height:1.75rem;" />
                        <span style="white-space:nowrap;">{{ config('app.name') }}</span>
                    </a>
                    <div style="display:flex;align-items:center;gap:.35rem;background:rgba(35,134,54,0.1);border:1px solid rgba(35,134,54,0.3);padding:.15rem .45rem;border-radius:2rem;margin-left:.25rem;"
                        title="Live Verified Nodes">
                        <div
                            style="width:5px;height:5px;background:#3fb950;border-radius:50%;box-shadow:0 0 5px #3fb950;">
                        </div>
                        <span
                            style="font-size:.62rem;font-weight:800;color:#3fb950;font-family:monospace;">{{ number_format($totalActiveNodes) }}</span>
                    </div>
                </div>

                {{-- Search bar (hidden on mobile) --}}
                @if(!request()->routeIs('home') && !request()->routeIs('search.index'))
                    <form action="{{ route('search.index') }}" method="GET" class="header-search">
                        <div
                            style="display:flex;align-items:center;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.45rem;overflow:hidden;">
                            <span style="padding:0 .75rem;color:var(--color-gh-dim);display:flex;align-items:center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="M21 21l-4.35-4.35" />
                                </svg>
                            </span>
                            <input type="text" name="q" placeholder="Search verified nodes..." value="{{ request('q') }}"
                                style="background:transparent;border:none;color:#fff;padding:.45rem 0;width:100%;outline:none;font-size:.85rem;">
                        </div>
                    </form>
                @else
                    <div style="flex:1;"></div>
                @endif

                {{-- Hamburger (mobile only) --}}
                <label for="nav-toggle" class="hamburger-btn" style="color:var(--color-gh-dim);"
                    aria-label="Toggle navigation">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                </label>

                {{-- Nav (desktop inline) --}}
                <input type="checkbox" id="nav-toggle">
                <nav class="nav-links">
                    <a href="{{ route('directory') }}"
                        style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;{{ request()->routeIs('directory') ? 'color:var(--color-gh-accent);' : '' }}">Directory</a>
                    <a href="{{ route('submit.create') }}"
                        style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;{{ request()->routeIs('submit.*') ? 'color:var(--color-gh-accent);' : '' }}">Submit</a>
                    <a href="{{ route('advertise.create') }}"
                        style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;{{ request()->routeIs('advertise.*') ? 'color:var(--color-gh-accent);' : '' }}">Ads</a>
                    <a href="{{ route('support.index') }}"
                        style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;{{ request()->routeIs('support.*') ? 'color:var(--color-gh-accent);' : '' }}">Support</a>
                    <a href="{{ route('leaderboard') }}"
                        style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;{{ request()->routeIs('leaderboard') ? 'color:var(--color-gh-accent);' : '' }}">Elite</a>

                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                                style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;{{ request()->routeIs('admin.*') ? 'color:var(--color-gh-accent);' : '' }}">Admin</a>
                        @endif
                        <a href="{{ route('dashboard') }}"
                            style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;{{ request()->routeIs('dashboard') ? 'color:var(--color-gh-accent);' : '' }}">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit"
                                style="background:none;border:none;color:var(--color-accent-red);cursor:pointer;font-size:.82rem;padding:0;font-family:inherit;font-weight:600;">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login.form') }}"
                            style="color:var(--color-gh-dim);text-decoration:none;font-size:.82rem;font-weight:600;">Login</a>
                        <a href="{{ route('register.form') }}"
                            style="color:var(--color-gh-accent);text-decoration:none;font-size:.82rem;font-weight:700;border:1px solid var(--color-gh-accent);padding:.35rem .85rem;border-radius:.35rem;text-align:center;white-space:nowrap;">Join
                            Our Team</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- ═══ Main Content ═══ --}}
    <main style="flex:1;padding-top:1.5rem;">
        <div style="max-width:1100px;margin:0 auto;padding:0 1rem;">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div
                    style="background:rgba(35,134,54,.15);border:1px solid rgba(35,134,54,.4);color:#3fb950;padding:.65rem 1rem;border-radius:.4rem;margin-bottom:1rem;font-size:.82rem;">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('info'))
                <div
                    style="background:rgba(88,166,255,.12);border:1px solid rgba(88,166,255,.3);color:var(--color-gh-accent);padding:.65rem 1rem;border-radius:.4rem;margin-bottom:1rem;font-size:.82rem;">
                    {{ session('info') }}
                </div>
            @endif
            @if (session('check_result'))
                <div
                    style="background:rgba(88,166,255,.12);border:1px solid rgba(88,166,255,.3);color:var(--color-gh-accent);padding:.65rem 1rem;border-radius:.4rem;margin-bottom:1rem;font-size:.82rem;">
                    {{ session('check_result') }}
                </div>
            @endif
            @if (session('error'))
                <div
                    style="background:rgba(248,81,73,.12);border:1px solid rgba(248,81,73,.3);color:#f85149;padding:.65rem 1rem;border-radius:.4rem;margin-bottom:1rem;font-size:.82rem;">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div
                    style="background:rgba(248,81,73,.12);border:1px solid rgba(248,81,73,.3);color:#f85149;padding:.65rem 1rem;border-radius:.4rem;margin-bottom:1rem;font-size:.82rem;">
                    <ul style="margin:0;padding-left:1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    {{-- ═══ Footer ═══ --}}
    <footer style="margin-top:3rem;border-top:1px solid var(--color-gh-border);padding:1.5rem 1rem;">
        <div
            style="max-width:1100px;margin:0 auto;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:1rem;">
            <div>
                <span style="color:#fff;font-weight:700;font-size:.9rem;">{{ config('app.name') }}</span>
                <p style="margin:.2rem 0 0;font-size:.7rem;color:var(--color-gh-dim);">&copy; 2024 - {{ date('Y') }}
                    Privacy First Directory.</p>
            </div>
            <nav style="display:flex;flex-wrap:wrap;gap:.75rem 1.25rem;align-items:center;">
                <a href="{{ route('home') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Home</a>
                <a href="{{ route('offline') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Offline Link</a>
                <a href="{{ route('about') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">About</a>
                <a href="{{ route('support.index') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Support</a>
                <a href="{{ route('advertise.create') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Advertise</a>
                <a href="{{ route('leaderboard') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Leaderboard</a>
                <a href="{{ route('gpg') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">GPG Public</a>
            </nav>
            <a href="mailto:{{ config('site.contact_email') }}"
                style="color:var(--color-gh-dim);text-decoration:none;font-size:.72rem;">
                {{ config('site.contact_email') }}
            </a>
        </div>
    </footer>
    <script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,5021655,4,0,0,0,00010000']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?5021655&101" alt="" border="0"></a></noscript>
</body>

</html>
