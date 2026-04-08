<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    <div class="home-hero-container">
        <div class="home-hero-main">
            <div class="hero-brand">
                <x-app.logo class="hero-logo-large" />
                <h1 class="hero-title-main">Hidden Line</h1>
            </div>

            <div class="hero-search-box-wrapper">
                <form action="{{ route('search.index') }}" method="GET" class="hero-search-form">
                    <div class="search-glass-container">
                        <i class="fas fa-search glass-search-icon"></i>
                        <input type="text" name="q" placeholder="Search the Onion network..." aria-label="Search" autofocus autocomplete="off">
                        <div class="search-actions">
                            <i class="fas fa-keyboard keyboard-hint"></i>
                            <i class="fas fa-microphone-slash voice-disabled"></i>
                        </div>
                    </div>
                    <div class="search-btn-group">
                        <button type="submit" class="google-btn">Onion Search</button>
                        <a href="{{ route('link.random') }}" class="google-btn">I'm Feeling Lucky</a>
                    </div>
                </form>
            </div>

            <div class="hero-quick-stats">
                <p>Trusted by thousands. Explore <span>{{ number_format($stats['online_links'] ?? 0) }}</span> active onion services today.</p>
            </div>
        </div>

        <div class="scroll-indicator" onclick="document.getElementById('discover').scrollIntoView({behavior: 'smooth'})">
            <i class="fas fa-chevron-down"></i>
            <span>Discover More</span>
        </div>
    </div>

    {{-- Discover Section (Visible on Scroll) --}}
    <div class="discover-section" id="discover">
        <div class="discover-container">
            <div class="discover-grid">
                {{-- Categories --}}
                <div class="discover-card categories-card">
                    <div class="card-header">
                        <div>
                            <h3><i class="fas fa-th"></i> Browse by Category</h3>
                            <p>Explore the network by service type</p>
                        </div>
                    </div>
                    <div class="category-pills">
                        @foreach ($categories as $category)
                            <a href="{{ route('category.show', $category->value) }}" class="pill">
                                {{ $category->label() }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="discover-sub-grid">
                    {{-- New Arrivals --}}
                    @if (isset($recentlyAddedLinks) && $recentlyAddedLinks->count() > 0)
                        <div class="discover-card">
                            <div class="card-header">
                                <h3><i class="fas fa-bolt"></i> New Arrivals</h3>
                                <a href="{{ route('search.index', ['sort' => 'newest']) }}" class="card-link">View all</a>
                            </div>
                            <div class="mini-links">
                                @foreach ($recentlyAddedLinks as $link)
                                    <div class="mini-link-item">
                                        <div class="mini-info">
                                            <a href="{{ route('link.show', $link->slug) }}">{{ $link->title }}</a>
                                            <span>{{ $link->category->label() }}</span>
                                        </div>
                                        <div class="mini-status online"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Featured --}}
                    @if (isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                        <div class="discover-card sponsored-card">
                            <div class="card-header">
                                <h3><i class="fas fa-star"></i> Featured</h3>
                                <span class="badge-ads">OFFER</span>
                            </div>
                            <div class="mini-links">
                                @foreach ($sponsoredLinks as $sponsoredLink)
                                    <div class="mini-link-item">
                                        <div class="mini-info">
                                            <a href="{{ $sponsoredLink->url }}" target="_blank" class="featured-link-text">{{ $sponsoredLink->title }}</a>
                                            <span>Premium Service</span>
                                        </div>
                                        <i class="fas fa-check-circle verified-icon"></i>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Banner Ads --}}
            @if (isset($headerAds) && $headerAds->count() > 0)
                <div class="home-ad-strip">
                    @foreach ($headerAds as $headerAd)
                        <a href="{{ $headerAd->url }}" class="ad-strip-item">
                            @if ($headerAd->banner_path)
                                <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}">
                            @else
                                <div class="ad-strip-placeholder">
                                    <span>{{ $headerAd->title }}</span>
                                    <small>Sponsored</small>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        :root {
            --google-gray: #303134;
            --google-bg: #202124;
            --google-border: #3c4043;
            --google-text: #bdc1c6;
            --accent-blue: #8ab4f8;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        .home-hero-container {
            min-height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding-bottom: 4rem;
        }

        .home-hero-main {
            width: 100%;
            max-width: 700px;
            text-align: center;
            margin-top: -8vh;
            animation: heroFadeUp 0.8s ease-out;
        }

        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-brand {
            margin-bottom: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .hero-logo-large {
            width: 160px;
            height: 160px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 20px rgba(138, 180, 248, 0.15));
        }

        .hero-title-main {
            font-size: 3.8rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(180deg, #fff 0%, #bdc1c6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -2px;
        }

        .hero-search-box-wrapper {
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .search-glass-container {
            background: var(--glass-bg);
            border: 1px solid var(--google-border);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 0.85rem 1.75rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
        }

        .search-glass-container:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #5f6368;
        }

        .search-glass-container:focus-within {
            background: #303134;
            border-color: transparent;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            transform: translateY(-2px);
        }

        .glass-search-icon {
            color: #9aa0a6;
            font-size: 1.2rem;
        }

        .hero-search-form input {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.2rem;
            flex-grow: 1;
            padding: 0.5rem 0;
            outline: none;
        }

        .search-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            color: var(--accent-blue);
            font-size: 1.3rem;
        }

        .keyboard-hint, .voice-disabled {
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .keyboard-hint:hover { opacity: 1; }

        .search-btn-group {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .google-btn {
            background: #303134;
            border: 1px solid #303134;
            color: #e8eaed;
            padding: 0.7rem 1.5rem;
            border-radius: 4px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .google-btn:hover {
            border-color: #5f6368;
            background-color: #3c4043;
            color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .hero-quick-stats {
            margin-top: 2.5rem;
            color: #9aa0a6;
            font-size: 0.95rem;
        }

        .hero-quick-stats span {
            color: var(--accent-blue);
            font-weight: 700;
        }

        .scroll-indicator {
            position: absolute;
            bottom: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            color: #5f6368;
            font-size: 0.85rem;
            animation: bounce 2s infinite;
            cursor: pointer;
            transition: color 0.3s;
        }

        .scroll-indicator:hover {
            color: var(--accent-blue);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }

        /* Discover Section */
        .discover-section {
            padding: 6rem 1rem;
            background: #171717;
            border-top: 1px solid var(--google-border);
        }

        .discover-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .discover-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 2.5rem;
            margin-bottom: 3.5rem;
        }

        .discover-card {
            background: #202124;
            border: 1px solid var(--google-border);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .discover-card:hover {
            transform: translateY(-8px);
            border-color: #5f6368;
            box-shadow: 0 12px 40px rgba(0,0,0,0.3);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            color: #fff;
        }

        .card-header h3 i { color: var(--accent-blue); }

        .card-header p {
            margin: 0.5rem 0 0 0;
            font-size: 0.9rem;
            color: #9aa0a6;
        }

        .card-link {
            font-size: 0.85rem;
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .card-link:hover { text-decoration: underline; }

        .category-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .pill {
            background: #303134;
            color: #e8eaed;
            padding: 0.5rem 1.1rem;
            border-radius: 24px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid var(--google-border);
        }

        .pill:hover {
            background: var(--accent-blue);
            color: #202124;
            border-color: var(--accent-blue);
            transform: scale(1.05);
        }

        .discover-sub-grid {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .mini-links {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .mini-link-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--google-border);
        }

        .mini-link-item:last-child { border-bottom: none; padding-bottom: 0; }

        .mini-info { display: flex; flex-direction: column; }
        .mini-info a {
            color: #fff;
            font-weight: 500;
            text-decoration: none;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }
        .mini-info a:hover { color: var(--accent-blue); }
        .mini-info span { font-size: 0.8rem; color: #9aa0a6; }

        .mini-status { width: 10px; height: 10px; border-radius: 50%; }
        .mini-status.online { background: #81c995; box-shadow: 0 0 10px rgba(129, 201, 149, 0.4); }

        .featured-link-text { color: #fcc934 !important; }
        .badge-ads {
            background: rgba(252, 201, 52, 0.1);
            color: #fcc934;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #fcc934;
            font-weight: 700;
        }

        .verified-icon { color: #fcc934; font-size: 1rem; }

        .home-ad-strip {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .ad-strip-item {
            display: block;
            height: 120px;
            background: #202124;
            border: 1px solid var(--google-border);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            transition: all 0.3s;
        }

        .ad-strip-item:hover {
            border-color: var(--accent-blue);
            transform: scale(1.02);
        }

        .ad-strip-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ad-strip-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #fff;
            background: linear-gradient(45deg, #202124, #303134);
        }

        .ad-strip-placeholder span { font-weight: 600; font-size: 1.1rem; }
        .ad-strip-placeholder small { color: #9aa0a6; margin-top: 0.25rem; }

        @media (max-width: 900px) {
            .discover-grid { grid-template-columns: 1fr; }
            .hero-title-main { font-size: 2.8rem; }
            .hero-logo-large { width: 120px; height: 120px; }
            .home-hero-main { margin-top: -5vh; }
        }
    </style>
</x-app.layouts>