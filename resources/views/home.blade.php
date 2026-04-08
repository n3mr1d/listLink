<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    <div class="home-hero">
        <div class="hero-content">
            <div class="hero-logo-wrapper">
                <x-app.logo class="hero-logo" />
                <h1 class="hero-title">Hidden Line</h1>
            </div>

            <div class="hero-search-wrapper">
                <form action="{{ route('search.index') }}" method="GET" class="main-search-form">
                    <div class="search-input-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="q" placeholder="Search the dark web..." aria-label="Search" autofocus>
                    </div>
                    <div class="search-buttons">
                        <button type="submit" class="hero-btn">Search Onion</button>
                        <a href="{{ route('submit.create') }}" class="hero-btn btn-outline">Submit Link</a>
                    </div>
                </form>
            </div>


        </div>

        {{-- Categories Grid --}}
        <div class="home-sections-container">

            <div class="dual-sections">


                {{-- Sponsored / Featured --}}
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
                                        <a href="{{ $sponsoredLink->url }}" target="_blank"
                                            class="link-name sponsored-link-text">{{ $sponsoredLink->title }}</a>
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

        {{-- Banner Ads --}}
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div class="bottom-ads-wrapper">
                @foreach ($headerAds as $headerAd)
                    <div class="ad-banner-minimal">
                        <span class="minimal-sponsored">SPONSORED</span>
                        <a href="{{ $headerAd->url }}" class="ad-link">
                            @if ($headerAd->banner_path)
                                <a href="{{ $headerAd->url }}" style="display:block; width:100%; height:100%;">
                                    <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}"
                                        style="width:100%; height:100%; object-fit:cover;">
                                </a>
                            @else
                                <a href="{{ $headerAd->url }}"
                                    style="display:flex; width:100%; height:100%; align-items:center; justify-content:center; background:linear-gradient(135deg, #1a2332 0%, #0d1117 100%); text-decoration:none;">
                                    <span style="font-size:1.2rem; font-weight:700; color:#fff;">{{ $headerAd->title }}</span>
                                </a>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        <style>
            :root {
                --hero-bg: #0d1117;
                --search-bg: #161b22;
                --search-border: #30363d;
                --search-focus: #1f6feb;
                --text-main: #c9d1d9;
                --text-dim: #8b949e;
                --accent: #58a6ff;
            }

            .home-hero {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 60vh;
                text-align: center;
                padding: 2rem 1rem;
                animation: fadeIn 0.8s ease-out;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .hero-logo-wrapper {
                margin-bottom: 2rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .hero-logo {
                width: 120px;
                height: 120px;
                filter: drop-shadow(0 0 15px rgba(88, 166, 255, 0.2));
            }

            .hero-title {
                font-size: 3rem;
                font-weight: 800;
                background: linear-gradient(135deg, #fff 0%, #8b949e 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin: 0;
                letter-spacing: -1px;
            }

            .hero-search-wrapper {
                width: 100%;
                max-width: 650px;
                margin-bottom: 3rem;
            }

            .search-input-container {
                position: relative;
                background: var(--search-bg);
                border: 1px solid var(--search-border);
                border-radius: 50px;
                padding: 0.5rem 1.5rem;
                display: flex;
                align-items: center;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            }

            .search-input-container:focus-within {
                border-color: var(--search-focus);
                box-shadow: 0 0 0 3px rgba(31, 111, 235, 0.2), 0 8px 30px rgba(0, 0, 0, 0.3);
                transform: translateY(-2px);
            }

            .search-icon {
                color: var(--text-dim);
                margin-right: 1rem;
            }

            .main-search-form input {
                background: transparent;
                border: none;
                color: #fff;
                width: 100%;
                font-size: 1.1rem;
                padding: 0.75rem 0;
                outline: none;
            }

            .search-buttons {
                display: flex;
                justify-content: center;
                gap: 1rem;
                margin-top: 1.5rem;
            }

            .hero-btn {
                padding: 0.6rem 1.5rem;
                border-radius: 6px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                text-decoration: none;
                font-size: 0.95rem;
                background: #21262d;
                color: var(--text-main);
                border: 1px solid var(--search-border);
            }

            .hero-btn:hover {
                background: #30363d;
                border-color: #8b949e;
            }

            .btn-outline {
                background: transparent;
            }

            .hero-stats {
                display: flex;
                gap: 2rem;
                padding: 1rem 2rem;
                background: rgba(22, 27, 34, 0.5);
                border-radius: 12px;
                border: 1px solid var(--search-border);
                backdrop-filter: blur(10px);
            }

            .stat-item {
                display: flex;
                flex-direction: column;
            }

            .stat-value {
                font-size: 1.25rem;
                font-weight: 700;
                color: #fff;
            }

            .stat-label {
                font-size: 0.75rem;
                text-transform: uppercase;
                color: var(--text-dim);
                letter-spacing: 1px;
            }

            .stat-divider {
                width: 1px;
                background: var(--search-border);
            }

            .home-sections-container {
                max-width: 1100px;
                margin: 0 auto 4rem auto;
                padding: 0 1rem;
            }

            .home-section {
                background: #161b22;
                border: 1px solid var(--search-border);
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
                color: var(--text-main);
            }

            .section-header h2 i {
                color: var(--accent);
            }

            .categories-tag-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 0.75rem;
            }

            .category-tag {
                background: #0d1117;
                border: 1px solid var(--search-border);
                padding: 0.75rem;
                border-radius: 8px;
                text-align: center;
                text-decoration: none;
                color: var(--text-main);
                font-size: 0.9rem;
                transition: all 0.2s;
            }

            .category-tag:hover {
                border-color: var(--accent);
                background: rgba(88, 166, 255, 0.1);
                color: var(--accent);
            }

            .dual-sections {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            @media (max-width: 768px) {
                .dual-sections {
                    grid-template-columns: 1fr;
                }

                .hero-title {
                    font-size: 2.2rem;
                }

                .hero-stats {
                    flex-direction: column;
                    gap: 1rem;
                }

                .stat-divider {
                    width: 100%;
                    height: 1px;
                }
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
                border-bottom: 1px solid var(--search-border);
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

            .link-name:hover {
                color: var(--accent);
                text-decoration: underline;
            }

            .link-meta {
                font-size: 0.75rem;
                color: var(--text-dim);
            }

            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }

            .status-dot.uptime-online {
                background-color: #238636;
                box-shadow: 0 0 10px rgba(35, 134, 54, 0.5);
            }

            .status-dot.uptime-offline {
                background-color: #da3633;
            }

            .status-dot.uptime-unknown {
                background-color: #8b949e;
            }

            .sponsored-section {
                border: 1px solid rgba(210, 153, 34, 0.3);
                background: linear-gradient(180deg, rgba(210, 153, 34, 0.05) 0%, rgba(22, 27, 34, 1) 100%);
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

            .sponsored-link-text {
                color: #d29922;
            }

            .view-all {
                font-size: 0.8rem;
                color: var(--accent);
                text-decoration: none;
            }

            .bottom-ads-wrapper {
                max-width: 1100px;
                margin: 0 auto 4rem auto;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                padding: 0 1rem;
            }

            .ad-banner-minimal {
                position: relative;
                width: 100%;
                height: 90px;
                background: #161b22;
                border: 1px solid var(--search-border);
                border-radius: 8px;
                overflow: hidden;
            }

            .minimal-sponsored {
                position: absolute;
                top: 5px;
                right: 10px;
                font-size: 0.6rem;
                color: var(--text-dim);
                font-weight: 700;
                z-index: 2;
            }

            .ad-link {
                display: block;
                width: 100%;
                height: 100%;
            }

            .ad-link img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: opacity 0.3s;
            }

            .ad-link:hover img {
                opacity: 0.8;
            }

            .ad-placeholder {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, #0d1117, #161b22);
                color: #fff;
                font-weight: 700;
                font-size: 1.2rem;
            }
        </style>
</x-app.layouts>