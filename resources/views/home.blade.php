<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    {{-- ═══════════════════════════════════════════════════
    HERO — Centered search (Google-style)
    ════════════════════════════════════════════════════ --}}
    <div class="hl-hero">
        <div class="hl-hero-inner">

            {{-- Brand --}}
            <div class="hl-brand">
                <x-app.logo class="hl-logo" />
                <h1 class="hl-site-name">Hidden Line</h1>
                <p class="hl-tagline">Privacy-first Tor directory &mdash;
                    {{ number_format($stats['online_links'] ?? 0) }} active services
                </p>
            </div>

            {{-- Search bar --}}
            <form action="{{ route('search.index') }}" method="GET" class="hl-search-form" id="main-search-form">
                <div class="hl-search-wrap">
                    <svg class="hl-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input id="main-search-input" type="text" name="q" placeholder="Search the Onion network&hellip;"
                        aria-label="Search onion services" autofocus autocomplete="off" spellcheck="false">
                </div>
                <div class="hl-search-buttons">
                    <button type="submit" class="hl-btn hl-btn-primary" id="search-submit-btn">Onion Search</button>
                    <a href="{{ route('link.random') }}" class="hl-btn hl-btn-ghost" id="feeling-lucky-btn">I&rsquo;m
                        Feeling Lucky</a>
                </div>
            </form>

        </div>
    </div>

    @if(isset($headerAds) && $headerAds->count() > 0)
        <section class="hl-ads-section" id="sponsored-ads" aria-label="Sponsored advertisements">
            <div class="hl-container">
                <p class="hl-ads-label">Sponsored</p>
                <div class="hl-ads-stack">
                    @foreach ($headerAds as $headerAd)
                        <a href="{{ $headerAd->url }}" class="hl-ad-banner" id="ad-banner-{{ $loop->index }}" target="_blank"
                            rel="noopener noreferrer nofollow">
                            @if($headerAd->banner_path)
                                <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}"
                                    loading="lazy">
                            @else
                                <div class="hl-ad-placeholder">
                                    <span class="hl-ad-placeholder-title">{{ $headerAd->title }}</span>
                                    <span class="hl-ad-placeholder-sub">Sponsored</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <style>
        /* =====================================================
       HIDDEN LINE — HOME PAGE STYLES
       Goal: Minimal, fast, Tor-friendly. No heavy effects.
    ===================================================== */

        /* ── Variables ─────────────────────────────────────── */
        :root {
            --hl-bg: #181a1b;
            --hl-surface: #1f2123;
            --hl-border: #2e3135;
            --hl-text: #d4d8dd;
            --hl-text-muted: #6e7681;
            --hl-accent: #4a9eff;
            --hl-accent-dim: #1e3a5c;
            --hl-green: #3fb950;
            --hl-red: #f85149;
            --hl-gold: #d4a022;
            --hl-radius: 6px;
            --hl-radius-lg: 10px;
            --hl-font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* ── Reset / Base ──────────────────────────────────── */
        .hl-hero,
        .hl-section,
        .hl-ads-section {
            box-sizing: border-box;
        }

        /* ── Hero ──────────────────────────────────────────── */
        .hl-hero {
            min-height: calc(100vh - 56px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1.5rem 5rem;

        }

        .hl-hero-inner {
            width: 100%;
            max-width: 640px;
            text-align: center;
        }

        /* Brand */
        .hl-brand {
            margin-bottom: 2.5rem;
        }

        .hl-logo {
            width: 72px;
            height: 72px;
            display: block;
            margin: 0 auto 1rem;
        }

        .hl-site-name {
            margin: 0 0 0.5rem;
            font-size: 2.75rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .hl-tagline {
            margin: 0;
            font-size: 0.9rem;
            color: var(--hl-text-muted);
        }

        /* Search form */
        .hl-search-form {
            width: 100%;
        }

        .hl-search-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--hl-surface);
            border: 1px solid var(--hl-border);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            margin-bottom: 1.25rem;
            transition: border-color 0.15s;
        }

        .hl-search-wrap:focus-within {
            border-color: var(--hl-accent);
        }

        .hl-search-icon {
            width: 18px;
            height: 18px;
            color: var(--hl-text-muted);
            flex-shrink: 0;
        }

        .hl-search-wrap input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--hl-text);
            font-size: 1rem;
            font-family: var(--hl-font);
        }

        .hl-search-wrap input::placeholder {
            color: var(--hl-text-muted);
        }

        /* Buttons */
        .hl-search-buttons {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .hl-btn {
            display: inline-block;
            padding: 0.6rem 1.4rem;
            border-radius: var(--hl-radius);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            font-family: var(--hl-font);
            transition: background 0.15s, border-color 0.15s;
        }

        .hl-btn-primary {
            background: var(--hl-surface);
            color: var(--hl-text);
            border-color: var(--hl-border);
        }

        .hl-btn-primary:hover {
            background: #2a2d32;
            border-color: var(--hl-accent);
            color: #fff;
        }

        .hl-btn-ghost {
            background: var(--hl-surface);
            color: var(--hl-text);
            border-color: var(--hl-border);
        }

        .hl-btn-ghost:hover {
            background: #2a2d32;
            border-color: #3e4248;
            color: #fff;
        }

        /* ── Section / Container ───────────────────────────── */
        .hl-section {
            padding: 3rem 1.5rem;
            background: var(--hl-surface);
            border-top: 1px solid var(--hl-border);
        }

        .hl-container {
            max-width: 960px;
            margin: 0 auto;
        }

        /* ── Block ─────────────────────────────────────────── */
        .hl-block {
            margin-bottom: 2.5rem;
        }

        .hl-block-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--hl-text-muted);
            margin: 0 0 1rem;
        }

        /* Category pills */
        .hl-category-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .hl-pill {
            background: var(--hl-bg);
            color: var(--hl-text);
            border: 1px solid var(--hl-border);
            border-radius: 50px;
            padding: 0.35rem 0.9rem;
            font-size: 0.82rem;
            text-decoration: none;
            transition: border-color 0.15s, color 0.15s;
        }

        .hl-pill:hover {
            border-color: var(--hl-accent);
            color: var(--hl-accent);
        }

        /* ── Directory Grid ────────────────────────────────── */
        .hl-dir-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
        }

        /* Directory card */
        .hl-dir-card {
            background: var(--hl-bg);
            border: 1px solid var(--hl-border);
            border-radius: var(--hl-radius-lg);
            padding: 1.5rem;
        }

        .hl-dir-card-featured {
            border-color: rgba(212, 160, 34, 0.35);
        }

        .hl-dir-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .hl-dir-card-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
        }

        .hl-dir-card-title svg {
            width: 16px;
            height: 16px;
            color: var(--hl-accent);
            flex-shrink: 0;
        }

        .hl-dir-card-more {
            font-size: 0.8rem;
            color: var(--hl-accent);
            text-decoration: none;
        }

        .hl-dir-card-more:hover {
            text-decoration: underline;
        }

        /* Directory list */
        .hl-dir-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .hl-dir-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 0;
            border-bottom: 1px solid var(--hl-border);
        }

        .hl-dir-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .hl-dir-item:first-child {
            padding-top: 0;
        }

        .hl-dir-item-info {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            min-width: 0;
        }

        .hl-dir-item-title {
            color: var(--hl-text);
            font-size: 0.92rem;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .hl-dir-item-title:hover {
            color: var(--hl-accent);
        }

        .hl-featured-title {
            color: var(--hl-gold) !important;
        }

        .hl-dir-item-desc {
            font-size: 0.78rem;
            color: var(--hl-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .hl-dir-item-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.3rem;
            flex-shrink: 0;
        }

        .hl-dir-item-checked {
            font-size: 0.72rem;
            color: var(--hl-text-muted);
            white-space: nowrap;
        }

        /* Status badges */
        .hl-status {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.55rem;
            border-radius: 50px;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .hl-status-up {
            color: var(--hl-green);
            background: rgba(63, 185, 80, 0.12);
            border: 1px solid rgba(63, 185, 80, 0.25);
        }

        .hl-status-down {
            color: var(--hl-red);
            background: rgba(248, 81, 73, 0.1);
            border: 1px solid rgba(248, 81, 73, 0.2);
        }

        /* Badge */
        .hl-badge-ad {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.55rem;
            border-radius: 3px;
            letter-spacing: 0.05em;
            color: var(--hl-gold);
            background: rgba(212, 160, 34, 0.12);
            border: 1px solid rgba(212, 160, 34, 0.3);
        }

        /* Verified icon */
        .hl-verified-icon {
            width: 14px;
            height: 14px;
            color: var(--hl-gold);
        }

        /* ── Ads Section ───────────────────────────────────── */
        .hl-ads-section {
            padding: 2rem 1.5rem 3rem;
        }

        .hl-ads-label {
            margin: 0 0 1rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--hl-text-muted);
        }

        .hl-ads-stack {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
        }

        .hl-ad-banner {
            display: block;
            width: 100%;
            height: 90px;
            border-radius: var(--hl-radius);
            overflow: hidden;
            border: 1px solid var(--hl-border);
            text-decoration: none;
        }

        .hl-ad-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .hl-ad-placeholder {
            width: 100%;
            height: 100%;
            background: var(--hl-surface);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .hl-ad-placeholder-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--hl-text);
        }

        .hl-ad-placeholder-sub {
            font-size: 0.75rem;
            color: var(--hl-text-muted);
        }

        /* ── Responsive ────────────────────────────────────── */
        @media (max-width: 640px) {
            .hl-hero {
                padding: 2rem 1.25rem 4rem;
                min-height: auto;
            }

            .hl-site-name {
                font-size: 2rem;
            }

            .hl-logo {
                width: 56px;
                height: 56px;
            }

            .hl-dir-grid {
                grid-template-columns: 1fr;
            }

            .hl-search-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .hl-btn {
                text-align: center;
            }

            .hl-section {
                padding: 2rem 1.25rem;
            }
        }
    </style>

</x-app.layouts>