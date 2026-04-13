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
        $defaultDescription = "Hidden Line — The elite privacy-focused directory for the Tor network. Explore " . number_format($totalActiveNodes) . "+ verified .onion services with high-precision uptime monitoring and deep indexing. No JavaScript. No tracking. Pure performance.";
    @endphp
    <meta name="description" content="{{ $description ?? $defaultDescription }}">
    <meta name="keywords"
        content="tor directory, onion links, darknet search, privacy, anonymity, hidden services, verified onion">
    <meta name="author" content="Hidden Line">
    <meta name="robots" content="index, follow">

    {{-- Geo Tags --}}
    <meta name="geo.region" content="ID" />
    <meta name="geo.placename" content="Jakarta" />

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Verified Tor Directory' }} | Hidden Line">
    <meta property="og:description"
        content="{{ $description ?? 'Discover verified .onion services on the Tor network. Secure, private, and always updated.' }}">
    <meta property="og:image" content="{{ asset('favicon-32x32.png') }}">

    {{-- Tor Integration --}}

    <meta http-equiv="onion-location" content="{{config("app.url")}}" />
    <title>{{ $title ?? 'Directory' }} - {{ config('app.name', 'Hidden Line') }}</title>

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
                        <span style="white-space:nowrap;">Hidden Line</span>
                    </a>
                    <div style="display:flex;align-items:center;gap:.35rem;background:rgba(35,134,54,0.1);border:1px solid rgba(35,134,54,0.3);padding:.15rem .45rem;border-radius:2rem;margin-left:.25rem;" title="Live Verified Nodes">
                        <div style="width:5px;height:5px;background:#3fb950;border-radius:50%;box-shadow:0 0 5px #3fb950;"></div>
                        <span style="font-size:.62rem;font-weight:800;color:#3fb950;font-family:monospace;">{{ number_format($totalActiveNodes) }}</span>
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
                <span style="color:#fff;font-weight:700;font-size:.9rem;">Hidden Line</span>
                <p style="margin:.2rem 0 0;font-size:.7rem;color:var(--color-gh-dim);">&copy; 2024 - {{ date('Y') }}
                    Privacy First Directory.</p>
            </div>
            <nav style="display:flex;flex-wrap:wrap;gap:.75rem 1.25rem;align-items:center;">
                <a href="{{ route('home') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Home</a>
                <a href="{{ route('offline') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Offline Services</a>
                <a href="{{ route('about') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">About</a>
                <a href="{{ route('support.index') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Support</a>
                <a href="{{ route('advertise.create') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">Advertise</a>
                <a href="{{ route('gpg') }}"
                    style="color:var(--color-gh-dim);text-decoration:none;font-size:.78rem;">GPG Public</a>
            </nav>
            <a href="mailto:treixnox@protonmail.com"
                style="color:var(--color-gh-dim);text-decoration:none;font-size:.72rem;">
                <span
                    style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#4ade80;margin-right:.35rem;"></span>treixnox@protonmail.com
            </a>
        </div>
    </footer>
</body>

</html>
