<x-app.layouts title="{{ $category->label() }} .Onion Links"
    description="Browse all verified Tor hidden services in the {{ $category->label() }} category. Updated daily with uptime status.">

    <div class="category-page-wrapper">
        {{-- Header Section --}}
        <header class="category-header">
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a>
                <span class="sep">/</span>
                <span class="active">{{ $category->label() }}</span>
            </nav>
            <div class="header-main">
                <div class="category-icon-large">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="header-text">
                    <h1 class="premium-title">{{ $category->label() }}</h1>
                    <p class="category-subtitle">Showing all verified hidden services listed under the <strong>{{ $category->label() }}</strong> directory.</p>
                </div>
            </div>
        </header>

        <div class="category-layout">
            {{-- Main Content: Link Grid --}}
            <main class="category-main">
                <div class="results-meta-line">
                    <i class="fas fa-info-circle"></i> We found {{ number_format($links->total()) }} active services in this category.
                </div>

                @if($links->count() > 0)
                    <div class="category-links-grid">
                        @foreach($links as $link)
                            <div class="category-result-card {{ $link->is_featured ? 'featured' : '' }}">
                                @if($link->is_featured)
                                    <span class="featured-ribbon">FEATURED</span>
                                @endif
                                <div class="card-body">
                                    <div class="card-top-meta">
                                        <span class="status-pill {{ $link->uptime_status->cssClass() }}">
                                            {{ $link->uptime_status->label() }}
                                        </span>
                                        <span class="time-meta"><i class="far fa-clock"></i> {{ $link->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="card-title">
                                        <a href="{{ route('link.show', $link->slug) }}">{{ $link->title }}</a>
                                    </h3>
                                    <div class="card-url">
                                        <span class="onion-v3-shorthand">{{ $link->url }}</span>
                                    </div>
                                    <p class="card-desc">
                                        {{ Str::limit($link->description ?? 'No description provided. This is a verified hidden service in the ' . $category->label() . ' category.', 120) }}
                                    </p>
                                    <div class="card-footer">
                                        <a href="{{ route('link.show', $link->slug) }}" class="card-btn">View Details</a>
                                        <a href="{{ $link->url }}" target="_blank" class="card-btn-icon" title="Visit Site"><i class="fas fa-external-link-alt"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrap">
                        {{ $links->links('pagination.simple') }}
                    </div>
                @else
                    <div class="empty-state-card">
                        <i class="fas fa-search-minus"></i>
                        <h3>Nothing Here Yet</h3>
                        <p>We haven't indexed any active links in this category. Be the first to submit a high-quality link!</p>
                        <a href="{{ route('submit.create') }}" class="btn-primary-pill">Submit a Link</a>
                    </div>
                @endif
            </main>

            {{-- Sidebar: All Categories --}}
            <aside class="category-sidebar">
                <div class="sidebar-widget">
                    <h4>All Categories</h4>
                    <div class="sidebar-category-list">
                        @foreach($categories as $cat)
                            <a href="{{ route('category.show', $cat->value) }}" 
                               class="sidebar-cat-item {{ $cat->value === $category->value ? 'active' : '' }}">
                                <span class="cat-label">{{ $cat->label() }}</span>
                                @if($cat->value === $category->value)
                                    <i class="fas fa-chevron-right"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="sidebar-ad-minimal">
                    <p>Grow your service</p>
                    <a href="{{ route('advertise.create') }}">Advertise Here</a>
                </div>
            </aside>
        </div>
    </div>

    <style>
        :root {
            --cat-bg: #0d1117;
            --cat-card: #161b22;
            --cat-border: #30363d;
            --cat-accent: #58a6ff;
            --cat-text: #c9d1d9;
            --cat-text-dim: #8b949e;
        }

        .category-page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* Header */
        .category-header { margin-bottom: 3rem; }
        .breadcrumb-nav { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2rem; font-size: 0.85rem; color: var(--cat-text-dim); }
        .breadcrumb-nav a { color: var(--cat-text-dim); text-decoration: none; }
        .breadcrumb-nav a:hover { color: var(--cat-accent); }
        .breadcrumb-nav .sep { opacity: 0.4; }
        .breadcrumb-nav .active { color: #fff; font-weight: 500; }

        .header-main { display: flex; align-items: center; gap: 2rem; }
        .category-icon-large { width: 80px; height: 80px; background: rgba(88, 166, 255, 0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--cat-accent); border: 1px solid rgba(88, 166, 255, 0.2); }
        .premium-title { font-size: 3rem; font-weight: 800; color: #fff; margin: 0; letter-spacing: -1.5px; }
        .category-subtitle { color: var(--cat-text-dim); font-size: 1.1rem; margin: 0.5rem 0 0 0; }

        /* Layout */
        .category-layout { display: grid; grid-template-columns: 1fr 300px; gap: 3rem; }

        .results-meta-line { color: var(--cat-text-dim); font-size: 0.9rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; }

        /* Link Cards */
        .category-links-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }

        .category-result-card { background: var(--cat-card); border: 1px solid var(--cat-border); border-radius: 16px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; }
        .category-result-card:hover { transform: translateY(-5px); border-color: var(--cat-accent); box-shadow: 0 12px 40px rgba(0,0,0,0.3); }

        .featured { border-color: #fcc934; background: linear-gradient(135deg, #1c2128 0%, #161b22 100%); }
        .featured-ribbon { position: absolute; top: 12px; right: -25px; background: #fcc934; color: #000; font-size: 0.65rem; font-weight: 800; padding: 4px 30px; transform: rotate(45deg); }

        .card-body { padding: 1.75rem; }
        .card-top-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .status-pill { font-size: 0.7rem; font-weight: 700; padding: 2px 10px; border-radius: 20px; text-transform: uppercase; background: rgba(0,0,0,0.2); }
        .status-pill.uptime-online { color: #81c995; border: 1px solid #81c995; }
        .status-pill.uptime-offline { color: #f28b82; border: 1px solid #f28b82; }

        .time-meta { font-size: 0.75rem; color: var(--cat-text-dim); }
        .card-title { font-size: 1.25rem; margin: 0 0 0.5rem 0; }
        .card-title a { color: #fff; text-decoration: none; }
        .card-title a:hover { color: var(--cat-accent); }

        .card-url { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: var(--cat-accent); margin-bottom: 1rem; opacity: 0.8; }
        .card-desc { font-size: 0.9rem; color: #bdc1c6; line-height: 1.5; margin-bottom: 1.5rem; height: 3rem; overflow: hidden; }

        .card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--cat-border); padding-top: 1.25rem; }
        .card-btn { font-size: 0.85rem; font-weight: 600; color: var(--cat-accent); text-decoration: none; }
        .card-btn:hover { text-decoration: underline; }
        .card-btn-icon { color: var(--cat-text-dim); font-size: 0.9rem; transition: color 0.2s; }
        .card-btn-icon:hover { color: #fff; }

        /* Sidebar */
        .sidebar-widget { background: var(--cat-card); border: 1px solid var(--cat-border); border-radius: 16px; padding: 1.5rem; }
        .sidebar-widget h4 { font-size: 1rem; color: #fff; margin-bottom: 1.5rem; }

        .sidebar-category-list { display: flex; flex-direction: column; gap: 0.5rem; }
        .sidebar-cat-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: 10px; text-decoration: none; color: var(--cat-text-dim); font-size: 0.9rem; transition: all 0.2s; }
        .sidebar-cat-item:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .sidebar-cat-item.active { background: var(--cat-accent); color: #0d1117; font-weight: 700; }

        .sidebar-ad-minimal { margin-top: 2rem; text-align: center; border: 1px dashed var(--cat-border); border-radius: 12px; padding: 1.5rem; }
        .sidebar-ad-minimal p { font-size: 0.8rem; color: var(--cat-text-dim); margin-bottom: 0.5rem; }
        .sidebar-ad-minimal a { font-size: 0.9rem; font-weight: 700; color: var(--cat-accent); text-decoration: none; }

        .empty-state-card { text-align: center; padding: 5rem 2rem; background: var(--cat-card); border-radius: 16px; border: 1px solid var(--cat-border); }
        .empty-state-card i { font-size: 3rem; color: var(--cat-text-dim); margin-bottom: 1.5rem; opacity: 0.5; }
        .empty-state-card h3 { color: #fff; margin-bottom: 0.75rem; }
        .empty-state-card p { color: var(--cat-text-dim); margin-bottom: 2rem; }

        .btn-primary-pill { background: var(--cat-accent); color: #0d1117; text-decoration: none; padding: 0.75rem 2rem; border-radius: 50px; font-weight: 700; display: inline-block; }

        .pagination-wrap { margin-top: 3rem; }

        @media (max-width: 900px) {
            .category-layout { grid-template-columns: 1fr; }
            .premium-title { font-size: 2.2rem; }
            .category-icon-large { width: 60px; height: 60px; font-size: 1.8rem; }
            .category-links-grid { grid-template-columns: 1fr; }
        }
    </style>