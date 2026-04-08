<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    {{-- Top Minimalist Search Engine Area --}}
    <div class="google-home-container">
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

        <div class="google-stats">
            <span>{{ number_format($stats['total_links']) }} Verified Links</span> &middot; 
            <span class="text-green-400">{{ number_format($stats['online_links']) }} Online Now</span> &middot; 
            <span>{{ number_format($stats['indexed_count']) }} Indexed Pages</span>
        </div>

        {{-- Banner Ads Right Under Search --}}
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div class="ads-under-search">
                <span class="ads-label">Sponsored Ads</span>
                <div class="ads-grid">
                    @foreach ($headerAds as $headerAd)
                        <a href="{{ $headerAd->url }}" class="ad-link-card" target="_blank">
                            @if ($headerAd->banner_path)
                                <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}">
                            @else
                                <div class="ad-placeholder-card">{{ $headerAd->title }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Restored Directory Menu Sections --}}
    <div class="home-sections-container">
        <section class="home-section">
            <div class="section-header">
                <h2><i class="fas fa-th-large"></i> Browse Categories</h2>
            </div>
            <div class="categories-tag-grid">
                @foreach ($categories as $category)
                    <a href="{{ route('category.show', $category->value) }}" class="category-tag">
                        {{ $category->label() }}
                    </a>
                @endforeach
            </div>
        </section>

        <div class="dual-sections">
            {{-- Recently Added --}}
            @if (isset($recentlyAddedLinks) && $recentlyAddedLinks->count() > 0)
                <section class="home-section compact">
                    <div class="section-header">
                        <h2><i class="fas fa-clock"></i> New Arrivals</h2>
                        <a href="{{ route('search.index', ['sort' => 'newest']) }}" class="view-all">See more</a>
                    </div>
                    <div class="compact-links-list">
                        @foreach ($recentlyAddedLinks as $link)
                            <div class="compact-link-item">
                                <div class="link-info">
                                    <a href="{{ route('link.show', $link->slug) }}" class="link-name">{{ $link->title }}</a>
                                    <span class="link-meta">{{ $link->category->label() }} • {{ $link->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="status-dot {{ $link->uptime_status->cssClass() }}" title="{{ $link->uptime_status->label() }}"></span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Sponsored / Featured Links from Directory --}}
            @if (isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                <section class="home-section compact sponsored-section">
                    <div class="section-header">
                        <h2><i class="fas fa-star"></i> Featured Services</h2>
                        <span class="sponsored-badge">Ads</span>
                    </div>
                    <div class="compact-links-list">
                        @foreach ($sponsoredLinks as $sponsoredLink)
                            <div class="compact-link-item">
                                <div class="link-info">
                                    <a href="{{ $sponsoredLink->url }}" target="_blank" class="link-name sponsored-link-text">{{ $sponsoredLink->title }}</a>
                                    <span class="link-meta">Verified Premium Service</span>
                                </div>
                                <i class="fas fa-crown text-amber-500"></i>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    <style>
        /* Base Styling & Tweaks */
        .site-footer { display: none !important; }
        .site-logo { opacity: 0; pointer-events: none; }
        .site-header { background: transparent !important; border-bottom: none !important; position: absolute; width: 100%; top: 0; }
        .main-content { padding: 0 !important; }

        :root {
            --gh-bg: #0d1117;
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* Top Minimal Search Engine Area */
        .google-home-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 12vh;
            padding-bottom: 3rem;
            width: 100%;
            border-bottom: 1px solid var(--gh-border);
            margin-bottom: 3rem;
            background: linear-gradient(180deg, #0d1117 0%, rgba(13,17,23,0.8) 100%);
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
            margin-bottom: 25px;
            padding: 0 15px;
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
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 2rem;
        }

        .text-green-400 {
            color: #4ade80;
        }

        /* Ads right under Search */
        .ads-under-search {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 800px;
            padding: 0 15px;
        }

        .ads-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--gh-dim);
            margin-bottom: 12px;
        }

        .ads-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            width: 100%;
        }

        .ad-link-card {
            display: block;
            height: 60px;
            border: 1px solid var(--gh-border);
            border-radius: 6px;
            overflow: hidden;
            transition: opacity 0.2s, transform 0.2s;
        }

        .ad-link-card:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            border-color: #484f58;
        }

        .ad-link-card img {
            height: 100%;
            width: auto;
            max-width: 320px;
            object-fit: cover;
        }

        .ad-placeholder-card {
            height: 100%;
            min-width: 200px;
            background: #161b22;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gh-dim);
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0 20px;
        }

        /* Restored Directory Menu Sections */
        .home-sections-container {
            max-width: 1100px;
            margin: 0 auto 4rem auto;
            padding: 0 1rem;
        }

        .home-section {
            background: #161b22;
            border: 1px solid var(--gh-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--gh-text);
        }

        .section-header h2 i {
            color: var(--gh-accent);
        }

        .categories-tag-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.75rem;
        }

        .category-tag {
            background: #0d1117;
            border: 1px solid var(--gh-border);
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: var(--gh-text);
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .category-tag:hover {
            border-color: var(--gh-accent);
            background: rgba(88, 166, 255, 0.1);
            color: var(--gh-accent);
        }

        .dual-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .dual-sections { grid-template-columns: 1fr; }
            .google-title { font-size: 2.2rem; }
        }

        .compact-links-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .compact-link-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gh-border);
        }

        .compact-link-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .link-info {
            display: flex;
            flex-direction: column;
        }

        .link-name {
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .link-name:hover { color: var(--gh-accent); text-decoration: underline; }
        .link-meta { font-size: 0.75rem; color: var(--gh-dim); }

        .status-dot { width: 8px; height: 8px; border-radius: 50%; }
        .uptime-online { background-color: #238636; box-shadow: 0 0 10px rgba(35, 134, 54, 0.5); }
        .uptime-offline { background-color: #da3633; }
        .uptime-unknown { background-color: #8b949e; }

        .sponsored-section {
            border: 1px solid rgba(210, 153, 34, 0.3);
            background: linear-gradient(180deg, rgba(210, 153, 34, 0.05) 0%, #161b22 100%);
        }

        .sponsored-badge {
            font-size: 0.6rem;
            background: rgba(210, 153, 34, 0.2);
            color: #d29922;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            border: 1px solid #d29922;
        }
        .sponsored-link-text { color: #d29922; }
        .view-all { font-size: 0.8rem; color: var(--gh-accent); text-decoration: none; }
    </style>
</x-app.layouts>
