<x-app.layouts
    title="{{ $query ? 'Results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    {{-- ══════════════════════════════════════════════════════
         SEARCH ENGINE AREA
    ══════════════════════════════════════════════════════ --}}
    <div class="se-page">

        {{-- ── Search bar (always visible top) ──────────────── --}}
        <div class="se-search-header">
            <form action="{{ route('search.index') }}" method="GET" class="se-search-form" id="se-search-form">
                @if(!$query)
                {{-- No query: centered big search --}}
                <div class="se-hero-wrap">
                    <x-app.logo class="se-hero-logo" />
                    <h1 class="se-hero-title">Onion Search</h1>
                    <p class="se-hero-sub">
                        {{ number_format($totalLinks) }} links &middot; {{ number_format($onlineLinks) }} online
                    </p>
                    <div class="se-bar se-bar-large">
                        <svg class="se-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input
                            id="se-input"
                            type="text"
                            name="q"
                            value="{{ $query }}"
                            placeholder="Search the Onion network&hellip;"
                            aria-label="Search onion services"
                            autofocus
                            autocomplete="off"
                            spellcheck="false"
                        >
                        <button type="submit" class="se-submit-btn" id="se-submit-btn">Search</button>
                    </div>
                    {{-- Filters row (only visible when search active) --}}
                </div>
                @else
                {{-- Has query: compact top bar --}}
                <div class="se-bar se-bar-compact">
                    <svg class="se-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input
                        id="se-input"
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Search the Onion network&hellip;"
                        aria-label="Search onion services"
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <button type="submit" class="se-submit-btn" id="se-submit-btn">Search</button>
                </div>

                {{-- Filter chips --}}
                <div class="se-filters">
                    <select name="sort" class="se-select" id="se-sort-select" onchange="this.form.submit()">
                        <option value="relevance" {{ $sortBy === 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="newest"    {{ $sortBy === 'newest'    ? 'selected' : '' }}>Newest</option>
                        <option value="oldest"    {{ $sortBy === 'oldest'    ? 'selected' : '' }}>Oldest</option>
                        <option value="most_checked" {{ $sortBy === 'most_checked' ? 'selected' : '' }}>Most Checked</option>
                        <option value="recently_checked" {{ $sortBy === 'recently_checked' ? 'selected' : '' }}>Recently Checked</option>
                    </select>

                    <select name="category" class="se-select" id="se-category-select" onchange="this.form.submit()">
                        <option value="all" {{ $categoryFilter === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}" {{ $categoryFilter === $cat->value ? 'selected' : '' }}>
                                {{ $cat->label() }}
                            </option>
                        @endforeach
                    </select>

                    <select name="uptime" class="se-select" id="se-uptime-select" onchange="this.form.submit()">
                        <option value="all"     {{ $uptimeFilter === 'all'     ? 'selected' : '' }}>Any Status</option>
                        <option value="online"  {{ $uptimeFilter === 'online'  ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ $uptimeFilter === 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>

                    @if($categoryFilter !== 'all' || $uptimeFilter !== 'all' || $sortBy !== 'relevance')
                        <a href="{{ route('search.index', ['q' => $query]) }}" class="se-clear-btn" id="se-clear-filters-btn">Clear filters</a>
                    @endif
                </div>
                @endif
            </form>
        </div>

        {{-- ── Sponsor banner (single, compact, non-disruptive) ── --}}
        @if(isset($headerAds) && $headerAds->count() > 0)
        @php $ad = $headerAds->first(); @endphp
        <div class="se-sponsor-bar" id="se-sponsor-banner">
            <span class="se-sponsor-label">Sponsored</span>
            <a href="{{ $ad->url }}" target="_blank" rel="noopener noreferrer nofollow" class="se-sponsor-link" id="se-sponsor-link">
                @if($ad->banner_path)
                    <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" loading="lazy" class="se-sponsor-img">
                @else
                    <span class="se-sponsor-title">{{ $ad->title }}</span>
                    <span class="se-sponsor-sub">Premium Service</span>
                @endif
            </a>
        </div>
        @endif

        {{-- ── Results / Empty state ──────────────────────────── --}}
        @if($query)
            <div class="se-results-wrap">

                @if($links && $links->total() > 0)
                    {{-- Result count --}}
                    <p class="se-result-count" id="se-result-count">
                        <strong>{{ number_format($links->total()) }}</strong>
                        result{{ $links->total() !== 1 ? 's' : '' }} for
                        &ldquo;{{ e($query) }}&rdquo;
                        @if($searchTime)
                            <span class="se-result-time">({{ $searchTime }}ms)</span>
                        @endif
                    </p>

                    {{-- Result cards --}}
                    <div class="se-result-list" id="se-result-list">
                        @foreach($links as $link)
                        <div class="se-result-card" id="sr-{{ $link->id }}">
                            <div class="se-result-head">
                                <div class="se-result-title-row">
                                    <h3 class="se-result-title">
                                        <a href="{{ route('link.show', $link->slug) }}">{{ $link->title }}</a>
                                    </h3>
                                    <span class="se-uptime-badge {{ $link->uptime_status->cssClass() }}">
                                        {{ $link->uptime_status->label() }}
                                    </span>
                                </div>
                                <span class="se-result-url">{{ $link->url }}</span>
                            </div>
                            @if($link->description)
                            <p class="se-result-desc">{{ Str::limit($link->description, 200) }}</p>
                            @endif
                            <div class="se-result-meta">
                                <a href="{{ route('category.show', $link->category->value) }}" class="se-result-cat">{{ $link->category->label() }}</a>
                                <span class="se-result-meta-dot">&middot;</span>
                                <span class="se-result-age">{{ $link->created_at->diffForHumans() }}</span>
                                @if($link->last_check)
                                <span class="se-result-meta-dot">&middot;</span>
                                <span class="se-result-age">Checked {{ $link->last_check->diffForHumans() }}</span>
                                @endif
                                @if($link->user)
                                <span class="se-result-meta-dot">&middot;</span>
                                <span class="se-result-by">by {{ $link->user->username }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($links->hasPages())
                    <div class="se-pagination" id="se-pagination">
                        {{ $links->links('pagination.simple') }}
                    </div>
                    @endif

                @else
                    {{-- No results --}}
                    <div class="se-no-results" id="se-no-results">
                        <p class="se-no-results-msg">No results found for &ldquo;<strong>{{ e($query) }}</strong>&rdquo;</p>
                        <ul class="se-suggestions">
                            <li>Check your spelling</li>
                            <li>Try more general keywords</li>
                            <li>Search by partial .onion URL</li>
                            @if($categoryFilter !== 'all' || $uptimeFilter !== 'all')
                            <li><a href="{{ route('search.index', ['q' => $query]) }}">Remove filters and retry</a></li>
                            @endif
                        </ul>
                        <div class="se-browse-cats">
                            <p class="se-browse-cats-label">Browse categories:</p>
                            <div class="se-chip-row">
                                @foreach($categories as $cat)
                                <a href="{{ route('category.show', $cat->value) }}" class="se-chip">{{ $cat->label() }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif

    </div>{{-- /.se-page --}}

    {{-- ══════════════════════════════════════════════════════
         DIRECTORY MENU — User-submitted links only
    ══════════════════════════════════════════════════════ --}}
    <section class="dir-section" id="directory-menu" aria-label="Link Directory">
        <div class="dir-container">

            <div class="dir-header">
                <div>
                    <h2 class="dir-title">Link Directory</h2>
                    <p class="dir-sub">Verified links submitted by registered members</p>
                </div>
                <a href="{{ route('submit.create') }}" class="dir-submit-btn" id="dir-submit-link">+ Submit a Link</a>
            </div>

            {{-- Category filter tabs --}}
            <div class="dir-tabs" id="dir-category-tabs" role="navigation" aria-label="Filter directory by category">
                <a href="{{ request()->fullUrlWithQuery(['dir_page' => null, 'dir_cat' => null]) }}"
                   class="dir-tab {{ !request('dir_cat') ? 'dir-tab-active' : '' }}"
                   id="dir-tab-all">All</a>
                @foreach($categories as $cat)
                <a href="{{ request()->fullUrlWithQuery(['dir_page' => null, 'dir_cat' => $cat->value]) }}"
                   class="dir-tab {{ request('dir_cat') === $cat->value ? 'dir-tab-active' : '' }}"
                   id="dir-tab-{{ $cat->value }}">{{ $cat->label() }}</a>
                @endforeach
            </div>

            {{-- Directory table --}}
            @php
                $filteredDir = $directoryLinks;
            @endphp

            @if($directoryLinks->count() > 0)
            <div class="dir-table-wrap">
                <table class="dir-table" id="dir-link-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Last Checked</th>
                            <th>Added by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($directoryLinks as $dlink)
                        <tr id="dir-row-{{ $dlink->id }}" class="{{ request('dir_cat') && request('dir_cat') !== $dlink->category->value ? 'dir-row-hidden' : '' }}">
                            <td class="dir-td-main">
                                <a href="{{ route('link.show', $dlink->slug) }}" class="dir-link-title">{{ $dlink->title }}</a>
                                @if($dlink->description)
                                <span class="dir-link-desc">{{ Str::limit($dlink->description, 72) }}</span>
                                @endif
                                <span class="dir-link-url">{{ Str::limit($dlink->url, 40) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('category.show', $dlink->category->value) }}" class="dir-cat-badge">
                                    {{ $dlink->category->label() }}
                                </a>
                            </td>
                            <td>
                                @php $isOnline = $dlink->uptime_status->value === 'online'; @endphp
                                <span class="dir-status {{ $isOnline ? 'dir-status-up' : 'dir-status-down' }}">
                                    <span class="dir-status-dot"></span>
                                    {{ $isOnline ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="dir-td-time">
                                @if($dlink->last_check)
                                    {{ $dlink->last_check->diffForHumans() }}
                                @else
                                    <span class="dir-never">Not yet</span>
                                @endif
                            </td>
                            <td class="dir-td-user">
                                @if($dlink->user)
                                    {{ $dlink->user->username }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Directory pagination --}}
            @if($directoryLinks->hasPages())
            <div class="dir-pagination" id="dir-pagination">
                {{ $directoryLinks->links('pagination.simple') }}
            </div>
            @endif

            @else
            <div class="dir-empty" id="dir-empty">
                <p>No links in the directory yet. <a href="{{ route('submit.create') }}">Be the first to submit one.</a></p>
            </div>
            @endif

        </div>{{-- /.dir-container --}}
    </section>

    {{-- ══════════════════════════════════════════════════════
         BOTTOM ADS — Full-width vertical stack
    ══════════════════════════════════════════════════════ --}}
    @if(isset($headerAds) && $headerAds->count() > 1)
    <section class="se-bottom-ads" id="se-bottom-ads" aria-label="Sponsored ads">
        <div class="dir-container">
            <p class="se-sponsor-label">Sponsored</p>
            <div class="se-bottom-ads-stack">
                @foreach($headerAds->skip(1) as $bottomAd)
                <a href="{{ $bottomAd->url }}" target="_blank" rel="noopener noreferrer nofollow"
                   class="se-bottom-ad-item" id="bottom-ad-{{ $loop->index }}">
                    @if($bottomAd->banner_path)
                        <img src="{{ asset('storage/' . $bottomAd->banner_path) }}" alt="{{ $bottomAd->title }}" loading="lazy">
                    @else
                        <div class="se-bottom-ad-placeholder">
                            <span>{{ $bottomAd->title }}</span>
                            <small>Sponsored</small>
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
       SEARCH PAGE — /search
       Minimalist, Tor-friendly, fast first paint
    ===================================================== */

    :root {
        --se-bg:        #181a1b;
        --se-surface:   #1f2123;
        --se-border:    #2e3135;
        --se-text:      #d4d8dd;
        --se-muted:     #6e7681;
        --se-accent:    #4a9eff;
        --se-green:     #3fb950;
        --se-red:       #f85149;
        --se-gold:      #d4a022;
        --se-radius:    6px;
    }

    /* ── Page shell ────────────────────────────────────── */
    .se-page {
        background: var(--se-bg);
        min-height: 50vh;
        padding-bottom: 2rem;
    }

    /* ── Search header ─────────────────────────────────── */
    .se-search-header {
        padding: 2rem 1.5rem 0;
        max-width: 800px;
        margin: 0 auto;
    }

    /* ── Hero (no-query state) ─────────────────────────── */
    .se-hero-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 3rem 0 2rem;
    }

    .se-hero-logo {
        width: 60px;
        height: 60px;
        display: block;
        margin-bottom: 1rem;
    }

    .se-hero-title {
        margin: 0 0 0.4rem;
        font-size: 2.2rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: -0.5px;
    }

    .se-hero-sub {
        margin: 0 0 2rem;
        font-size: 0.88rem;
        color: var(--se-muted);
    }

    /* ── Search bar ────────────────────────────────────── */
    .se-bar {
        display: flex;
        align-items: center;
        background: var(--se-surface);
        border: 1px solid var(--se-border);
        border-radius: 50px;
        overflow: hidden;
        transition: border-color 0.15s;
    }

    .se-bar:focus-within {
        border-color: var(--se-accent);
    }

    .se-bar-large {
        width: 100%;
        max-width: 600px;
        padding: 0.75rem 1.25rem;
        gap: 0.75rem;
    }

    .se-bar-compact {
        width: 100%;
        padding: 0.6rem 1.25rem;
        gap: 0.75rem;
        border-radius: var(--se-radius);
    }

    .se-icon {
        width: 17px;
        height: 17px;
        color: var(--se-muted);
        flex-shrink: 0;
    }

    .se-bar input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: var(--se-text);
        font-size: 1rem;
        min-width: 0;
    }

    .se-bar input::placeholder { color: var(--se-muted); }

    .se-submit-btn {
        background: transparent;
        border: none;
        color: var(--se-accent);
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0 0.25rem;
        white-space: nowrap;
    }

    .se-submit-btn:hover { color: #fff; }

    /* ── Filters ───────────────────────────────────────── */
    .se-filters {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 0.75rem 0 0;
    }

    .se-select {
        background: var(--se-surface);
        border: 1px solid var(--se-border);
        color: var(--se-text);
        border-radius: var(--se-radius);
        padding: 0.3rem 0.6rem;
        font-size: 0.82rem;
        cursor: pointer;
        outline: none;
    }

    .se-select:focus { border-color: var(--se-accent); }

    .se-clear-btn {
        font-size: 0.8rem;
        color: var(--se-muted);
        text-decoration: none;
        padding: 0.3rem 0.6rem;
    }

    .se-clear-btn:hover { color: var(--se-red); }

    /* ── Sponsor bar ───────────────────────────────────── */
    .se-sponsor-bar {
        max-width: 800px;
        margin: 1rem auto;
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .se-sponsor-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--se-muted);
        white-space: nowrap;
    }

    .se-sponsor-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        background: var(--se-surface);
        border: 1px solid var(--se-border);
        border-radius: var(--se-radius);
        padding: 0.5rem 1rem;
        flex: 1;
        min-width: 0;
        overflow: hidden;
    }

    .se-sponsor-img {
        height: 40px;
        width: auto;
        object-fit: contain;
        display: block;
    }

    .se-sponsor-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--se-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .se-sponsor-sub {
        font-size: 0.75rem;
        color: var(--se-muted);
        white-space: nowrap;
    }

    /* ── Results ───────────────────────────────────────── */
    .se-results-wrap {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .se-result-count {
        font-size: 0.82rem;
        color: var(--se-muted);
        margin: 0 0 1.25rem;
    }

    .se-result-time { font-size: 0.78rem; }

    .se-result-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .se-result-card {
        padding: 1.1rem 0;
        border-bottom: 1px solid var(--se-border);
    }

    .se-result-card:last-child { border-bottom: none; }

    .se-result-head { margin-bottom: 0.4rem; }

    .se-result-title-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 0.2rem;
    }

    .se-result-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
    }

    .se-result-title a {
        color: var(--se-accent);
        text-decoration: none;
    }

    .se-result-title a:hover { text-decoration: underline; }

    .se-uptime-badge {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 50px;
        white-space: nowrap;
    }

    /* Reuse existing uptime badge CSS classes from global styles */
    .badge-online  { color: var(--se-green); background: rgba(63,185,80,.12); border: 1px solid rgba(63,185,80,.25); }
    .badge-offline { color: var(--se-red);   background: rgba(248,81,73,.1);  border: 1px solid rgba(248,81,73,.2); }
    .badge-unknown { color: var(--se-muted); background: rgba(110,118,129,.1);border: 1px solid rgba(110,118,129,.2); }

    .se-result-url {
        font-size: 0.78rem;
        color: var(--se-green);
        font-family: monospace;
        word-break: break-all;
        display: block;
    }

    .se-result-desc {
        margin: 0.5rem 0 0.6rem;
        font-size: 0.88rem;
        color: var(--se-text);
        line-height: 1.55;
    }

    .se-result-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.35rem;
        font-size: 0.78rem;
        color: var(--se-muted);
    }

    .se-result-cat {
        color: var(--se-accent);
        text-decoration: none;
        font-weight: 500;
    }

    .se-result-cat:hover { text-decoration: underline; }
    .se-result-meta-dot { opacity: 0.5; }
    .se-result-by { color: var(--se-muted); }

    /* Pagination */
    .se-pagination {
        margin: 2rem 0 0;
    }

    /* No results */
    .se-no-results {
        padding: 2rem 0;
    }

    .se-no-results-msg {
        font-size: 1rem;
        color: var(--se-text);
        margin-bottom: 1rem;
    }

    .se-suggestions {
        list-style: disc;
        padding-left: 1.5rem;
        font-size: 0.88rem;
        color: var(--se-muted);
        line-height: 2;
    }

    .se-suggestions a { color: var(--se-accent); }

    .se-browse-cats { margin-top: 1.5rem; }

    .se-browse-cats-label {
        font-size: 0.82rem;
        color: var(--se-muted);
        margin: 0 0 0.6rem;
    }

    .se-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .se-chip {
        background: var(--se-surface);
        color: var(--se-text);
        border: 1px solid var(--se-border);
        border-radius: 50px;
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
        text-decoration: none;
    }

    .se-chip:hover { border-color: var(--se-accent); color: var(--se-accent); }

    /* ══════════════════════════════════════════════════════
       DIRECTORY SECTION
    ══════════════════════════════════════════════════════ */
    .dir-section {
        background: var(--se-surface);
        border-top: 1px solid var(--se-border);
        padding: 2.5rem 1.5rem 3rem;
    }

    .dir-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .dir-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .dir-title {
        margin: 0 0 0.25rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
    }

    .dir-sub {
        margin: 0;
        font-size: 0.82rem;
        color: var(--se-muted);
    }

    .dir-submit-btn {
        display: inline-block;
        background: var(--se-bg);
        color: var(--se-accent);
        border: 1px solid var(--se-accent);
        border-radius: var(--se-radius);
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .dir-submit-btn:hover { background: var(--se-accent); color: #fff; }

    /* ── Category tabs ─────────────────────────────────── */
    .dir-tabs {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 0.25rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.25rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .dir-tabs::-webkit-scrollbar { display: none; }

    .dir-tab {
        display: inline-block;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        color: var(--se-muted);
        border: 1px solid transparent;
        white-space: nowrap;
        transition: color 0.15s, border-color 0.15s;
    }

    .dir-tab:hover { color: var(--se-text); border-color: var(--se-border); }

    .dir-tab-active {
        color: var(--se-accent);
        border-color: var(--se-accent);
        background: rgba(74, 158, 255, 0.08);
    }

    /* ── Directory table ───────────────────────────────── */
    .dir-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--se-border);
        border-radius: var(--se-radius);
    }

    .dir-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .dir-table th {
        text-align: left;
        padding: 0.65rem 1rem;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--se-muted);
        background: var(--se-bg);
        border-bottom: 1px solid var(--se-border);
        white-space: nowrap;
    }

    .dir-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--se-border);
        vertical-align: top;
        color: var(--se-text);
    }

    .dir-table tbody tr:last-child td { border-bottom: none; }

    .dir-table tbody tr:hover td { background: rgba(255,255,255,0.02); }

    .dir-row-hidden { display: none; }

    /* Main cell */
    .dir-td-main {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        min-width: 200px;
        max-width: 360px;
    }

    .dir-link-title {
        color: var(--se-accent);
        font-weight: 600;
        text-decoration: none;
        font-size: 0.92rem;
        word-break: break-word;
    }

    .dir-link-title:hover { text-decoration: underline; }

    .dir-link-desc {
        font-size: 0.78rem;
        color: var(--se-muted);
        line-height: 1.4;
    }

    .dir-link-url {
        font-size: 0.72rem;
        color: var(--se-green);
        font-family: monospace;
        word-break: break-all;
    }

    /* Category badge */
    .dir-cat-badge {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--se-muted);
        border: 1px solid var(--se-border);
        text-decoration: none;
        white-space: nowrap;
    }

    .dir-cat-badge:hover { border-color: var(--se-accent); color: var(--se-accent); }

    /* Status */
    .dir-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .dir-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .dir-status-up   { color: var(--se-green); }
    .dir-status-up   .dir-status-dot { background: var(--se-green); }
    .dir-status-down { color: var(--se-red); }
    .dir-status-down .dir-status-dot { background: var(--se-red); }

    .dir-td-time,
    .dir-td-user {
        font-size: 0.78rem;
        color: var(--se-muted);
        white-space: nowrap;
    }

    .dir-never { color: var(--se-border); }

    /* Dir pagination */
    .dir-pagination { margin-top: 1.5rem; }

    /* Empty */
    .dir-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--se-muted);
        font-size: 0.9rem;
    }

    .dir-empty a { color: var(--se-accent); }

    /* ── Bottom ads ────────────────────────────────────── */
    .se-bottom-ads {
        background: var(--se-bg);
        border-top: 1px solid var(--se-border);
        padding: 2rem 1.5rem;
    }

    .se-bottom-ads-stack {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .se-bottom-ad-item {
        display: block;
        width: 100%;
        height: 90px;
        border: 1px solid var(--se-border);
        border-radius: var(--se-radius);
        overflow: hidden;
        text-decoration: none;
    }

    .se-bottom-ad-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .se-bottom-ad-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: var(--se-surface);
        gap: 0.25rem;
        color: var(--se-text);
    }

    .se-bottom-ad-placeholder small { color: var(--se-muted); font-size: 0.72rem; }

    /* ── Responsive ────────────────────────────────────── */
    @media (max-width: 640px) {
        .se-search-header { padding: 1.5rem 1rem 0; }
        .se-hero-wrap { padding: 2rem 0 1.5rem; }
        .se-hero-title { font-size: 1.8rem; }
        .se-bar-large { padding: 0.65rem 1rem; }
        .se-results-wrap, .se-sponsor-bar { padding: 0 1rem; }
        .dir-section { padding: 2rem 1rem; }
        .dir-header { flex-direction: column; align-items: flex-start; }

        /* Stack table cells on mobile */
        .dir-table thead { display: none; }
        .dir-table tbody td { display: block; border: none; padding: 0.25rem 1rem; }
        .dir-table tbody td:first-child { padding-top: 0.85rem; }
        .dir-table tbody td:last-child  { padding-bottom: 0.85rem; border-bottom: 1px solid var(--se-border); }
        .dir-table tbody tr:last-child td:last-child { border-bottom: none; }
        .dir-td-main { max-width: 100%; }
    }
    </style>

</x-app.layouts>