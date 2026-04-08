<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    {{-- ═══════════════════════════════════════════════════
         HERO — Centered search engine (Google-style)
    ════════════════════════════════════════════════════ --}}
    <div class="min-h-[calc(100vh-56px)] flex items-center justify-content-center px-6 pb-20 pt-12">
        <div class="w-full max-w-[640px] mx-auto text-center">

            {{-- Brand --}}
            <div class="mb-10">
                <x-app.logo class="w-[72px] h-[72px] block mx-auto mb-4" />
                <h1 class="text-[2.75rem] font-bold text-white tracking-tight leading-none mb-2">Hidden Line</h1>
                <p class="text-sm text-[var(--text-muted)]">
                    Privacy-first Tor directory &mdash; {{ number_format($stats['online_links'] ?? 0) }} active services
                </p>
            </div>

            {{-- Search bar --}}
            <form action="{{ route('search.index') }}" method="GET" class="w-full" id="main-search-form">
                <div class="flex items-center gap-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-full px-6 py-3 mb-5 transition-colors duration-150 focus-within:border-[var(--accent-blue)]">
                    <svg class="w-[18px] h-[18px] text-[var(--text-muted)] shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input id="main-search-input" type="text" name="q"
                        placeholder="Search the Onion network&hellip;"
                        aria-label="Search onion services" autofocus autocomplete="off" spellcheck="false"
                        class="flex-1 bg-transparent border-none outline-none text-[var(--text-primary)] text-base placeholder:text-[var(--text-muted)]">
                </div>
                <div class="flex justify-center gap-3 flex-wrap">
                    <button type="submit" id="search-submit-btn"
                        class="inline-block px-6 py-2.5 rounded-md text-sm font-medium bg-[var(--bg-secondary)] text-[var(--text-primary)] border border-[var(--border-color)] cursor-pointer transition-colors duration-150 hover:bg-[var(--bg-hover)] hover:border-[var(--accent-blue)] hover:text-white">
                        Onion Search
                    </button>
                    <a href="{{ route('link.random') }}" id="feeling-lucky-btn"
                        class="inline-block px-6 py-2.5 rounded-md text-sm font-medium bg-[var(--bg-secondary)] text-[var(--text-primary)] border border-[var(--border-color)] no-underline transition-colors duration-150 hover:bg-[var(--bg-hover)] hover:border-[var(--border-color)] hover:text-white">
                        I&rsquo;m Feeling Lucky
                    </a>
                </div>
            </form>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         STATS — Network overview
    ════════════════════════════════════════════════════ --}}
    <section class="border-t border-[var(--border-color)] bg-[var(--bg-secondary)] py-10 px-6" id="stats-section">
        <div class="max-w-[960px] mx-auto">

            <h2 class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-6">Network Overview</h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {{-- Total Links --}}
                <div class="bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-lg p-5 text-center">
                    <div class="text-2xl font-bold text-[var(--accent-green)] leading-none mb-1">
                        {{ number_format($stats['total_links'] ?? 0) }}
                    </div>
                    <div class="text-[0.7rem] font-semibold uppercase tracking-wide text-[var(--text-muted)]">
                        Total Links
                    </div>
                </div>

                {{-- Online Now --}}
                <div class="bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-lg p-5 text-center">
                    <div class="text-2xl font-bold text-[var(--accent-cyan)] leading-none mb-1">
                        {{ number_format($stats['online_links'] ?? 0) }}
                    </div>
                    <div class="text-[0.7rem] font-semibold uppercase tracking-wide text-[var(--text-muted)]">
                        Online Now
                    </div>
                </div>

                {{-- Categories --}}
                <div class="bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-lg p-5 text-center">
                    <div class="text-2xl font-bold text-[var(--accent-purple)] leading-none mb-1">
                        {{ $stats['categories'] ?? 0 }}
                    </div>
                    <div class="text-[0.7rem] font-semibold uppercase tracking-wide text-[var(--text-muted)]">
                        Categories
                    </div>
                </div>

                {{-- Indexed Pages --}}
                <div class="bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-lg p-5 text-center">
                    <div class="text-2xl font-bold text-[var(--accent-blue)] leading-none mb-1">
                        {{ number_format($stats['indexed_count'] ?? 0) }}
                    </div>
                    <div class="text-[0.7rem] font-semibold uppercase tracking-wide text-[var(--text-muted)]">
                        Indexed Pages
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════
         ADS — Full-width vertical flex, bottom
    ════════════════════════════════════════════════════ --}}
    @if(isset($headerAds) && $headerAds->count() > 0)
    <section class="border-t border-[var(--border-color)] py-8 px-6" id="sponsored-ads" aria-label="Sponsored advertisements">
        <div class="max-w-[960px] mx-auto">
            <p class="mb-4 text-[0.7rem] font-semibold uppercase tracking-wider text-[var(--text-muted)]">Sponsored</p>
            <div class="flex flex-col gap-4 w-full">
                @foreach ($headerAds as $headerAd)
                    <a href="{{ $headerAd->url }}" id="ad-banner-{{ $loop->index }}" target="_blank"
                        rel="noopener noreferrer nofollow"
                        class="block w-full h-[90px] rounded-md overflow-hidden border border-[var(--border-color)] no-underline">
                        @if($headerAd->banner_path)
                            <img src="{{ asset('storage/' . $headerAd->banner_path) }}"
                                alt="{{ $headerAd->title }}" loading="lazy"
                                class="w-full h-full object-cover block">
                        @else
                            <div class="w-full h-full bg-[var(--bg-secondary)] flex flex-col items-center justify-center gap-1">
                                <span class="text-base font-semibold text-[var(--text-primary)]">{{ $headerAd->title }}</span>
                                <span class="text-xs text-[var(--text-muted)]">Sponsored</span>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</x-app.layouts>