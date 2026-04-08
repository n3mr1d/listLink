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
</head>

<body class="bg-gh-bg text-gh-text font-sans m-0 flex flex-col min-h-screen overflow-x-hidden">
    {{-- ═══ Header ═══ --}}
    <header class="sticky top-0 z-[1000] bg-gh-bg/80 backdrop-blur-xl border-b border-gh-border py-3">
        <div class="max-w-[1200px] mx-auto px-6 flex items-center justify-between gap-8">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 no-underline text-white font-bold text-xl shrink-0">
                <x-app.logo class="w-10 h-10" />
                <span class="hidden md:inline">Hidden Line</span>
            </a>

            @if(!request()->routeIs('home') && !request()->routeIs('search.index'))
                <form action="{{ route('search.index') }}" method="GET"
                    class="flex-grow max-w-[500px] relative bg-gh-bar-bg border border-gh-border rounded-md flex overflow-hidden">
                    <input type="text" name="q" placeholder="Search..." value="{{ request('q') }}"
                        class="bg-transparent border-none text-white px-4 py-2 w-full outline-none">
                    <button type="submit"
                        class="bg-gh-btn-bg border-none text-gh-dim px-4 cursor-pointer transition-colors hover:text-white"><i
                            class="fas fa-search"></i></button>
                </form>
            @else
                <div class="flex-grow"></div>
            @endif

            <x-app.navigation />
        </div>
    </header>


    {{-- ═══ Main Content ═══ --}}
    <main class="flex-grow pt-8">
        <div class="max-w-[1200px] mx-auto px-6">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div
                    class="bg-green-900/30 border border-green-700 text-green-400 px-4 py-3 rounded-md mb-6 transition-all">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('info'))
                <div class="bg-blue-900/30 border border-blue-700 text-blue-400 px-4 py-3 rounded-md mb-6 transition-all">
                    {{ session('info') }}
                </div>
            @endif
            @if (session('check_result'))
                <div class="bg-blue-900/30 border border-blue-700 text-blue-400 px-4 py-3 rounded-md mb-6 transition-all">
                    {{ session('check_result') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-900/30 border border-red-700 text-red-400 px-4 py-3 rounded-md mb-6 transition-all">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-900/30 border border-red-700 text-red-400 px-4 py-3 rounded-md mb-6 transition-all">
                    <ul class="m-0 pl-5 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    {{-- ═══ Modern Minimalist Footer ═══ --}}
    <footer class="w-full mt-16 border-t border-gh-border bg-gradient-to-b from-transparent to-gh-bar-bg/40">
        <div class="max-w-[1200px] mx-auto px-6 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">

                {{-- Brand Section --}}
                <div class="flex flex-col items-center md:items-start gap-2">
                    <div class="flex items-center gap-3 group cursor-default">

                        <span class="text-white font-extrabold tracking-wider text-lg">Hidden Line</span>
                    </div>
                    <p class="text-xs text-gh-dim/80 m-0">
                        &copy; 2024 - {{ date('Y') }} Privacy-First Directory.
                    </p>
                </div>

                {{-- Navigation --}}
                <nav class="flex items-center flex-wrap justify-center gap-x-8 gap-y-4">
                    <a href="{{ route('home') }}"
                        class="text-sm font-medium text-gh-dim hover:text-white transition-colors no-underline">Home</a>
                    <a href="{{ route('about') }}"
                        class="text-sm font-medium text-gh-dim hover:text-white transition-colors no-underline">About</a>
                    <a href="{{ route('support.index') }}"
                        class="text-sm font-medium text-gh-dim hover:text-white transition-colors no-underline">Support</a>
                    <a href="{{ route('advertise.create') }}"
                        class="text-sm font-medium text-gh-dim hover:text-white transition-colors no-underline">Advertise</a>
                </nav>

                {{-- Contact Tag --}}
                <div class="flex items-center">
                    <a href="mailto:treixnox@protonmail.com"
                        class="group flex items-center gap-3 px-4 py-2 rounded-full bg-gh-bar-bg border border-gh-border hover:border-gh-accent/50 transition-colors no-underline">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span
                            class="text-xs font-semibold text-gh-text group-hover:text-white transition-colors">treixnox@protonmail.com</span>
                    </a>
                </div>

            </div>
        </div>
    </footer>
</body>

</html>