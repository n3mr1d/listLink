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
    </style>
</head>

<body>
    {{-- ═══ Header ═══ --}}
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('home') }}" class="site-logo">
                <x-app.logo class="w-20 h-20" />
                Hidden Line
            </a>
            <form action="{{ route('search.index') }}" method="GET" class="search-bar">
                <input type="text" name="q" placeholder="Search .onion links..." value="{{ request('q') }}">
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    {{-- ═══ Navigation ═══ --}}
    <nav class="main-nav">
        <div class="container">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('submit.create') }}"
                class="nav-link {{ request()->routeIs('submit.*') ? 'active' : '' }}">Submit Link</a>
            <a href="{{ route('support.index') }}"
                class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}">Support</a>
            <a href="{{ route('advertise.create') }}"
                class="nav-link {{ request()->routeIs('advertise.*') ? 'active' : '' }}">Advertise</a>
            <a href="{{ route('search.index') }}" class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}">
                Search Engine
            </a>
            <div class="nav-right">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;">Logout
                            ({{ auth()->user()->username }})
                        </button>
                    </form>
                @else
                    <a href="{{ route('login.form') }}"
                        class="nav-link {{ request()->routeIs('login.*') ? 'active' : '' }}">Login</a>
                    <a href="{{ route('register.form') }}"
                        class="nav-link {{ request()->routeIs('register.*') ? 'active' : '' }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

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
                <div>&copy; {{ date('Y') }} Hidden Line — Privacy-First Tor Directory</div>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">
                    Ads & Suggestions: <a href="mailto:treixnox@protonmail.com"
                        style="color:var(--text-secondary);">treixnox@protonmail.com</a>
                </div>
                <div class="footer-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('support.index') }}">Support</a>
                    <a href="{{ route('advertise.create') }}">Advertise</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>