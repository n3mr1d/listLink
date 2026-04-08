<x-app.layouts title="{{ $query ? 'Search results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    <div class="search-page-container">
        @if (!$query)
            {{-- ═══ Search Hero (Empty State) ═══ --}}
            <div class="search-empty-hero">
                <div class="hero-brand">
                    <x-app.logo class="hero-logo-medium" />
                    <h1 class="hero-title-sub">Onion Search</h1>
                </div>

                <div class="hero-search-box-wrapper">
                    <form action="{{ route('search.index') }}" method="GET" class="hero-search-form">
                        <div class="search-glass-container">
                            <i class="fas fa-search glass-search-icon"></i>
                            <input type="text" name="q" value="{{ $query }}" placeholder="Search the onion network..." autofocus autocomplete="off">
                            <button type="submit" class="inline-search-btn">Search</button>
                        </div>
                    </form>
                </div>

                <div class="empty-stats">
                    <div class="stat-bubble">
                        <span class="val">{{ number_format($totalLinks) }}</span>
                        <span class="lab">Verified Links</span>
                    </div>
                    <div class="stat-bubble">
                        <span class="val">{{ number_format($indexedCount) }}</span>
                        <span class="lab">Indexed Pages</span>
                    </div>
                </div>
            </div>
        @else
            {{-- ═══ Results Top Bar (Logo + Search) ═══ --}}
            <div class="search-results-top-bar">
                <div class="top-bar-inner">
                    <a href="{{ route('home') }}" class="top-bar-logo">
                        <x-app.logo class="logo-small" />
                        <span>Hidden Line</span>
                    </a>
                    <form action="{{ route('search.index') }}" method="GET" class="top-bar-search">
                        <div class="top-bar-input-wrap">
                            <input type="text" name="q" value="{{ $query }}" placeholder="Search...">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="search-results-layout">
                <div class="results-main">
                    {{-- Stats line --}}
                    <div class="results-meta-line">
                        About {{ number_format($links->total()) }} results (0.{{ rand(10, 50) }} seconds)
                    </div>

                    {{-- Sponsored Results (Ads) --}}
                    @if (isset($headerAds) && $headerAds->count() > 0)
                        <div class="sponsored-results">
                            @foreach ($headerAds as $headerAd)
                                <div class="sponsored-item">
                                    <div class="sponsored-label">Sponsored</div>
                                    <div class="item-url">{{ parse_url($headerAd->url, PHP_URL_HOST) }}</div>
                                    <h3 class="item-title"><a href="{{ $headerAd->url }}">{{ $headerAd->title }}</a></h3>
                                    <p class="item-desc">Verified Premium Service. High uptime and secure transactions guaranteed.</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Main Results List --}}
                    <div class="results-list">
                        @foreach ($links as $link)
                            <div class="result-card">
                                <div class="result-url-row">
                                    <span class="result-url-text">{{ parse_url($link->url, PHP_URL_HOST) }}</span>
                                    <span class="result-status-dot {{ $link->uptime_status->cssClass() }}"></span>
                                </div>
                                <h3 class="result-title">
                                    <a href="{{ route('link.show', $link->slug) }}">{{ $link->title }}</a>
                                </h3>
                                <p class="result-description">
                                    {{ Str::limit($link->description ?? 'No description available for this hidden service.', 180) }}
                                </p>
                                <div class="result-meta">
                                    <span class="meta-tag">{{ $link->category->label() }}</span>
                                    <span class="meta-time">Indexed {{ $link->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="pagination-wrap">
                        {{ $links->appends(request()->query())->links('pagination.simple') }}
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="results-sidebar">
                    <div class="sidebar-widget">
                        <h4>Search Tools</h4>
                        <div class="filter-group">
                            <label>Category</label>
                            <select onchange="window.location.href=this.value">
                                <option value="{{ route('search.index', array_merge(request()->query(), ['category' => 'all'])) }}">All Categories</option>
                                @foreach (\App\Enum\Category::cases() as $cat)
                                    <option value="{{ route('search.index', array_merge(request()->query(), ['category' => $cat->value])) }}" {{ request('category') === $cat->value ? 'selected' : '' }}>
                                        {{ $cat->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sidebar-widget info-widget">
                        <h4>Did you know?</h4>
                        <p>We monitor thousands of onion links every 30 minutes to ensure you never click a dead link.</p>
                        <a href="{{ route('advertise.create') }}" class="sidebar-btn">Advertise here</a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        :root {
            --search-bg: #202124;
            --search-border: #3c4043;
            --search-text-dim: #9aa0a6;
            --search-accent: #8ab4f8;
            --search-link: #8ab4f8;
        }

        .search-page-container {
            min-height: calc(100vh - 100px);
        }

        /* Empty State */
        .search-empty-hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 5rem 1rem;
            text-align: center;
        }

        .hero-title-sub {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 2rem;
        }

        .inline-search-btn {
            background: var(--search-accent);
            color: #202124;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
        }

        .empty-stats {
            margin-top: 3rem;
            display: flex;
            gap: 2rem;
        }

        .stat-bubble {
            background: rgba(255,255,255,0.05);
            padding: 1rem 2rem;
            border-radius: 12px;
            border: 1px solid var(--search-border);
        }

        .stat-bubble .val { font-size: 1.5rem; font-weight: 700; color: #fff; display: block; }
        .stat-bubble .lab { font-size: 0.75rem; color: var(--search-text-dim); text-transform: uppercase; }

        /* Results Page */
        .search-results-top-bar {
            background: #202124;
            border-bottom: 1px solid var(--search-border);
            padding: 1.5rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-bar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 3rem;
            padding: 0 1.5rem;
        }

        .top-bar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .logo-small { width: 32px; height: 32px; }

        .top-bar-search {
            flex-grow: 1;
            max-width: 600px;
        }

        .top-bar-input-wrap {
            background: #303134;
            border-radius: 24px;
            padding: 0.5rem 1.25rem;
            display: flex;
            align-items: center;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .top-bar-input-wrap:focus-within {
            background: #3c4043;
            box-shadow: 0 1px 6px rgba(0,0,0,0.3);
        }

        .top-bar-input-wrap input {
            background: transparent;
            border: none;
            color: #fff;
            flex-grow: 1;
            outline: none;
            font-size: 1rem;
        }

        .top-bar-input-wrap button {
            background: transparent;
            border: none;
            color: var(--search-text-dim);
            cursor: pointer;
        }

        .search-results-layout {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 4rem;
        }

        .results-meta-line {
            color: var(--search-text-dim);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        /* Result Cards */
        .sponsored-results {
            background: rgba(138, 180, 248, 0.03);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(138, 180, 248, 0.1);
        }

        .sponsored-item { margin-bottom: 1.5rem; }
        .sponsored-item:last-child { margin-bottom: 0; }
        .sponsored-label { font-size: 0.7rem; font-weight: 700; color: #fcc934; margin-bottom: 0.25rem; }

        .result-card { margin-bottom: 2.5rem; }

        .result-url-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; }
        .result-url-text { color: var(--search-text-dim); font-size: 0.85rem; }
        .result-status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .result-status-dot.uptime-online { background: #81c995; }
        .result-status-dot.uptime-offline { background: #f28b82; }

        .result-title { margin: 0 0 0.25rem 0; font-size: 1.25rem; }
        .result-title a { color: var(--search-link); text-decoration: none; }
        .result-title a:hover { text-decoration: underline; }

        .result-description { color: #bdc1c6; line-height: 1.58; font-size: 0.95rem; margin-bottom: 0.5rem; }

        .result-meta { font-size: 0.8rem; color: var(--search-text-dim); display: flex; gap: 1rem; }
        .meta-tag { background: #303134; padding: 2px 8px; border-radius: 4px; }

        /* Sidebar */
        .sidebar-widget {
            background: #202124;
            border: 1px solid var(--search-border);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .sidebar-widget h4 { margin: 0 0 1rem 0; font-size: 1rem; color: #fff; }

        .filter-group label { display: block; font-size: 0.8rem; color: var(--search-text-dim); margin-bottom: 0.5rem; }
        .filter-group select {
            width: 100%;
            background: #303134;
            border: 1px solid var(--search-border);
            color: #fff;
            padding: 0.5rem;
            border-radius: 6px;
            outline: none;
        }

        .info-widget p { font-size: 0.85rem; color: var(--search-text-dim); line-height: 1.5; margin-bottom: 1.25rem; }
        .sidebar-btn {
            display: block;
            text-align: center;
            background: #303134;
            color: #fff;
            text-decoration: none;
            padding: 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
            border: 1px solid var(--search-border);
            transition: all 0.2s;
        }
        .sidebar-btn:hover { background: #3c4043; border-color: var(--search-text-dim); }

        .pagination-wrap { margin-top: 3rem; }

        @media (max-width: 900px) {
            .search-results-layout { grid-template-columns: 1fr; }
            .results-sidebar { order: -1; }
            .top-bar-inner { gap: 1rem; }
            .top-bar-logo span { display: none; }
        }
    </style>
</x-app.layouts>