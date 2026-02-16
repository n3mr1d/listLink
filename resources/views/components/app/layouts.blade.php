<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Hidden Line — Privacy-focused directory of Tor .onion websites. No JavaScript. No tracking.">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Directory' }} - {{ config('app.name', 'Hidden Line') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('icon/css/all.css') }}">
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