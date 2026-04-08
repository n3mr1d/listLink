<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    <div class="google-home-container">
        <!-- Main Search Area -->
        <main class="google-main">
            <div class="google-logo-box">
                <x-app.logo class="google-logo" />
                <h1 class="google-title">Hidden Line</h1>
            </div>

            <div class="google-search-box">
                <form action="{{ route('search.index') }}" method="GET" class="google-search-form">
                    <div class="google-search-bar">
                        <i class="fas fa-search google-search-icon"></i>
                        <input type="text" name="q" placeholder="Search the dark web..." aria-label="Search" autofocus autocomplete="off">
                    </div>
                    <div class="google-search-buttons">
                        <button type="submit" class="google-btn">Search Onion</button>
                        <a href="{{ route('submit.create') }}" class="google-btn">Submit Link</a>
                    </div>
                </form>
            </div>

            <!-- Stats (Minimalist) -->
            <div class="google-stats">
                <span>{{ number_format($stats['total_links']) }} Verified Links</span> &middot; 
                <span class="text-green-400">{{ number_format($stats['online_links']) }} Online Now</span> &middot; 
                <span>{{ number_format($stats['indexed_count']) }} Indexed Pages</span>
            </div>
        </main>

        <!-- Footer Area -->
        <footer class="google-footer">
            {{-- Sponsored / Featured Links (Minimal Text Row) --}}
            @if (isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                <div class="footer-sponsored">
                    <span class="footer-label">Featured:</span>
                    @foreach ($sponsoredLinks->take(4) as $sponsoredLink)
                        <a href="{{ $sponsoredLink->url }}" target="_blank" class="footer-sponsored-link">{{ $sponsoredLink->title }}</a>
                    @endforeach
                </div>
            @endif

            {{-- Categories (Minimal Text Row) --}}
            <div class="footer-categories">
                <span class="footer-label">Browse:</span>
                @foreach ($categories->take(8) as $category)
                    <a href="{{ route('category.show', $category->value) }}" class="footer-link">{{ $category->label() }}</a>
                @endforeach
                <a href="#" class="footer-link">More...</a>
            </div>

            {{-- Banner Ads --}}
            @if (isset($headerAds) && $headerAds->count() > 0)
                <div class="footer-ads">
                    <span class="footer-label ad-label">Sponsored Ads</span>
                    <div class="footer-ads-grid">
                        @foreach ($headerAds as $headerAd)
                            <a href="{{ $headerAd->url }}" class="footer-ad-link" target="_blank">
                                @if ($headerAd->banner_path)
                                    <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}">
                                @else
                                    <div class="footer-ad-placeholder">{{ $headerAd->title }}</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </footer>
    </div>

    <style>
        /* Hide layout elements not needed for minimalist home */
        .site-footer { display: none !important; }
        .site-logo { opacity: 0; pointer-events: none; } /* Hide top-left logo keep spacing */
        .site-header { background: transparent !important; border-bottom: none !important; position: absolute; width: 100%; top: 0; }
        .main-content { padding: 0 !important; }

        /* Google-like Minimalist Styles */
        :root {
            --gh-bg: #0d1117; /* Keep the dark theme */
            --gh-text: #e6edf3;
            --gh-dim: #7d8590;
            --gh-border: #30363d;
            --gh-bar-bg: #161b22;
            --gh-btn-bg: #21262d;
            --gh-btn-hover: #30363d;
            --gh-accent: #58a6ff;
            --gh-sponsored: #d29922;
        }

        body {
            background-color: var(--gh-bg);
            color: var(--gh-text);
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Adjust x-app.layouts top navigation if any to not overlap heavily or hide if possible */
        nav, header.main-header {
            background: transparent !important;
            border-bottom: none !important;
            box-shadow: none !important;
            position: absolute;
            top: 0;
            width: 100%;
        }

        .google-home-container {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 60px); /* Account for navbar if any */
            align-items: center;
            justify-content: space-between;
            padding-top: 10vh; /* Push content down */
            box-sizing: border-box;
        }

        .google-main {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            flex: 1;
            padding: 0 20px;
        }

        .google-logo-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            animation: gh-fade 0.6s ease-out;
        }

        @keyframes gh-fade {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .google-logo {
            width: 90px;
            height: 90px;
            margin-bottom: 10px;
            filter: drop-shadow(0 0 10px rgba(88, 166, 255, 0.15));
        }

        .google-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -1.5px;
            color: #fff;
        }

        .google-search-box {
            width: 100%;
            max-width: 580px;
            margin-bottom: 30px;
        }

        .google-search-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .google-search-bar {
            width: 100%;
            display: flex;
            align-items: center;
            background: var(--gh-bar-bg);
            border: 1px solid var(--gh-border);
            border-radius: 24px;
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: box-shadow 0.2s, border-color 0.2s;
        }

        .google-search-bar:hover,
        .google-search-bar:focus-within {
            box-shadow: 0 0 0 1px transparent, 0 4px 12px rgba(0,0,0,0.4);
            border-color: #484f58;
            background: #0d1117;
        }

        .google-search-icon {
            color: var(--gh-dim);
            font-size: 1.1rem;
            margin-right: 12px;
        }

        .google-search-bar input {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--gh-text);
            font-size: 1.1rem;
            outline: none;
            padding: 5px 0;
            line-height: normal;
        }

        .google-search-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .google-btn {
            background-color: var(--gh-btn-bg);
            color: var(--gh-text);
            border: 1px solid var(--gh-border);
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .google-btn:hover {
            background-color: var(--gh-btn-hover);
            border-color: #8b949e;
            color: #fff;
        }

        .google-stats {
            color: var(--gh-dim);
            font-size: 0.85rem;
            margin-top: 20px;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .text-green-400 {
            color: #4ade80;
        }

        /* Footer / Bottom Elements */
        .google-footer {
            width: 100%;
            background: var(--gh-bar-bg);
            border-top: 1px solid var(--gh-border);
            padding: 15px 30px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 0.85rem;
        }

        .footer-sponsored, .footer-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: center;
        }

        .footer-label {
            color: var(--gh-dim);
            margin-right: -5px;
        }

        .footer-sponsored-link {
            color: var(--gh-sponsored);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-sponsored-link:hover {
            text-decoration: underline;
        }

        .footer-link {
            color: var(--gh-accent);
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        /* Minimal Ad Row */
        .footer-ads {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
            border-top: 1px dashed var(--gh-border);
            padding-top: 12px;
        }

        .ad-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .footer-ads-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .footer-ad-link {
            display: block;
            height: 40px;
            border: 1px solid var(--gh-border);
            border-radius: 4px;
            overflow: hidden;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .footer-ad-link:hover {
            opacity: 1;
        }

        .footer-ad-link img {
            height: 100%;
            width: auto;
            max-width: 150px;
            object-fit: contain;
            background: #0d1117;
        }

        .footer-ad-placeholder {
            height: 100%;
            width: 150px;
            background: #0d1117;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gh-dim);
            font-size: 0.75rem;
        }

        @media (max-width: 600px) {
            .google-title { font-size: 2.2rem; }
            .google-search-box { padding: 0 10px; }
            .google-stats { font-size: 0.75rem; flex-wrap: wrap; justify-content: center;}
            .google-footer { padding: 15px 10px; }
            .footer-sponsored, .footer-categories { justify-content: center; }
        }
    </style>
</x-app.layouts>
