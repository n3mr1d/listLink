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
    <meta name="description"
        content="{{ $description ?? 'Hidden Line — Privacy-focused directory of Tor .onion websites. No JavaScript. No tracking. High uptime verification.' }}">
    <meta name="keywords"
        content="tor directory, onion links, darknet search, privacy, anonymity, hidden services, verified onion">
    <meta name="author" content="Hidden Line">
    <meta name="robots" content="index, follow">

    {{-- Geo Tags (Global/Privacy focus) --}}
    <meta name="geo.region" content="ID" />
    <meta name="geo.placename" content="Jakarta" />
    <meta name="geo.position" content="-6.2088;106.8456" />
    <meta name="ICBM" content="-6.2088, 106.8456" />

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Verified Tor Directory' }} | Hidden Line">
    <meta property="og:description"
        content="{{ $description ?? 'Discover verified .onion services on the Tor network. Secure, private, and always updated.' }}">
    <meta property="og:image" content="{{ asset('favicon-32x32.png') }}">

    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? 'Verified Tor Directory' }} | Hidden Line">
    <meta property="twitter:description"
        content="{{ $description ?? 'Explore the Tor network with confidence. Verified links and uptime monitoring.' }}">
    <meta property="twitter:image" content="{{ asset('favicon-32x32.png') }}">

    {{-- Tor Integration --}}
    @if(request()->getHost() !== 'hiddenline.onion')
        <meta http-equiv="Onion-Location"
            content="hidlisnonhc6ogbdlx3f4jpln43hyzvn6tbzvfqgv727v3kar3so3dad.onion{{ request()->getRequestUri() }}">
    @endif

    <title>{{ $title ?? 'Directory' }} - {{ config('app.name', 'Hidden Line') }}</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('icon/css/all.css') }}">

    <style>
        /* Complex Friendly Link CSS Helpers */
        .onion-v3 {
            font-family: var(--font-mono);
            word-break: break-all;
            background: rgba(0, 0, 0, 0.05);
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 0.9em;
            color: var(--accent-blue);
        }

        .onion-v3-shorthand {
            font-family: var(--font-mono);
            max-width: 15ch;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
            vertical-align: bottom;
        }

        .geo-tag {
            font-size: 0.7rem;
            color: var(--text-muted);
            opacity: 0.6;
        }

        /* ═══ New Minimalist Layout Styles ═══ */
        .site-header {
            background: rgba(13, 17, 23, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #30363d;
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.75rem 0;
        }
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }
        .site-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .header-search-bar {
            flex-grow: 1;
            max-width: 500px;
            position: relative;
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 6px;
            display: flex;
            overflow: hidden;
        }
        .header-search-bar input {
            background: transparent;
            border: none;
            color: #fff;
            padding: 0.5rem 1rem;
            width: 100%;
            outline: none;
        }
        .header-search-bar button {
            background: #21262d;
            border: none;
            color: #8b949e;
            padding: 0 1rem;
            cursor: pointer;
            transition: color 0.2s;
        }
        .header-search-bar button:hover { color: #fff; }

        .top-nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-shrink: 0;
        }
        .nav-link {
            color: #8b949e;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-link:hover, .nav-link.active { color: #58a6ff; }
        
        .nav-btn {
            background: transparent;
            border: none;
            color: #8b949e;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0;
        }
        .nav-btn:hover { color: #f85149; }

        .btn-primary-sm {
            background: #238636;
            color: #fff !important;
            padding: 0.4rem 1rem;
            border-radius: 6px;
        }
        .btn-primary-sm:hover { background: #2ea043; }

        @media (max-width: 768px) {
            .header-search-bar, .logo-text { display: none; }
            .top-nav { gap: 1rem; }
        }
    </style>
</head>

<body>
    {{-- ═══ Header ═══ --}}
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('home') }}" class="site-logo">
                <x-app.logo class="w-10 h-10" />
                <span class="logo-text">Hidden Line</span>
            </a>
            
            @if(!request()->routeIs('home'))
            <form action="{{ route('search.index') }}" method="GET" class="header-search-bar">
                <input type="text" name="q" placeholder="Search..." value="{{ request('q') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            @endif

            <nav class="top-nav">
                <a href="{{ route('search.index') }}" class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}">Search</a>
                <a href="{{ route('submit.create') }}" class="nav-link {{ request()->routeIs('submit.*') ? 'active' : '' }}">Submit</a>
                <a href="{{ route('advertise.create') }}" class="nav-link {{ request()->routeIs('advertise.*') ? 'active' : '' }}">Ads</a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login.form') }}" class="nav-link">Login</a>
                    <a href="{{ route('register.form') }}" class="nav-link btn-primary-sm">Join</a>
                @endauth
            </nav>
        </div>
    </header>


    {{-- ═══ Main Content ═══ --}}
    <main class="main-content">
        <div class="container">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
            @if (session('check_result'))
                <div class="alert alert-info">{{ session('check_result') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
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
    <footer class="site-footer">
        <div class="container">
            <div class="footer-inner">
                <div>&copy; 2024 - {{ date('Y') }} Hidden Line — Privacy-First Tor Directory</div>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">
                    Ads & Suggestions: <a href="mailto:treixnox@protonmail.com"
                        style="color:var(--text-secondary);">treixnox@protonmail.com</a>
                </div>
                <div class="footer-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('about') }}">About</a>
                    <a href="{{ route('support.index') }}">Support</a>
                    <a href="{{ route('advertise.create') }}">Advertise</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>