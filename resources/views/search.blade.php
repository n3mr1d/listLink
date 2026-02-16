<x-app.layouts title="{{ $query ? 'Search results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    {{-- ═══ Search Hero ═══ --}}
    <div class="search-hero flex flex-col items-center">
        <div class="search-hero-inner  flex flex-col items-center">
            <div class="search-hero-logo ">

            </div>
            <x-app.logo class="w-55 h-55 " />
            <h1 class="search-hero-title">Hidden Line Search</h1>
            <p class="search-hero-subtitle">Search across {{ number_format($totalLinks) }} verified .onion links in the
                Tor directory</p>

            <form action="{{ route('search.index') }}" method="GET" class="search-hero-form">
                <div class="search-input-wrap">
                    <span class="fa fa-search search-input-icon"></span>
                    <input type="text" name="q" value="{{ $query }}"
                        placeholder="Search for .onion links, services, or keywords..." autofocus autocomplete="off">
                    <button type="submit" class="search-submit-btn">Search</button>
                </div>

                {{-- Quick Filters Row --}}
                <div class="search-quick-filters">
                    <div class="search-filter-group">
                        <label for="search-category">Category:</label>
                        <select name="category" id="search-category">
                            <option value="all" {{ $categoryFilter === 'all' ? 'selected' : '' }}>All Categories
                            </option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->value }}" {{ $categoryFilter === $cat->value ? 'selected' : '' }}>
                                    {{ $cat->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="search-filter-group">
                        <label for="search-uptime">Status:</label>
                        <select name="uptime" id="search-uptime">
                            <option value="all" {{ $uptimeFilter === 'all' ? 'selected' : '' }}>Any Status</option>
                            <option value="online" {{ $uptimeFilter === 'online' ? 'selected' : '' }}>● Online</option>
                            <option value="offline" {{ $uptimeFilter === 'offline' ? 'selected' : '' }}>○ Offline
                            </option>
                            <option value="timeout" {{ $uptimeFilter === 'timeout' ? 'selected' : '' }}>◌ Timeout
                            </option>
                            <option value="unknown" {{ $uptimeFilter === 'unknown' ? 'selected' : '' }}>? Unknown
                            </option>
                        </select>
                    </div>

                    <div class="search-filter-group">
                        <label for="search-sort">Sort by:</label>
                        <select name="sort" id="search-sort">
                            <option value="relevance" {{ $sortBy === 'relevance' ? 'selected' : '' }}>Relevance
                            </option>
                            <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="most_checked" {{ $sortBy === 'most_checked' ? 'selected' : '' }}>Most
                                Checked
                            </option>
                            <option value="recently_checked" {{ $sortBy === 'recently_checked' ? 'selected' : '' }}>
                                Recently Checked</option>
                            <option value="title_asc" {{ $sortBy === 'title_asc' ? 'selected' : '' }}>Title A→Z
                            </option>
                            <option value="title_desc" {{ $sortBy === 'title_desc' ? 'selected' : '' }}>Title Z→A
                            </option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ Results Area ═══ --}}
    @if ($query)
        @if ($links && $links instanceof \Illuminate\Pagination\LengthAwarePaginator && $links->total() > 0)

            <div class="search-results-layout">
                {{-- Main Results --}}
                <div class="search-results-main">
                    {{-- Results Header --}}
                    <div class="search-results-header">
                        <div class="search-results-info">
                            <span class="search-results-count">{{ $links->total() }}
                                result{{ $links->total() !== 1 ? 's' : '' }}</span>
                            <span class="search-results-query">for "<strong>{{ e($query) }}</strong>"</span>
                            @if ($searchTime)
                                <span class="search-results-time">({{ $searchTime }}ms)</span>
                            @endif
                        </div>
                        @if ($categoryFilter !== 'all' || $uptimeFilter !== 'all' || $sortBy !== 'relevance')
                            <a href="{{ route('search.index', ['q' => $query]) }}" class="search-clear-filters">Clear
                                filters</a>
                        @endif
                    </div>

                    {{-- Active Filter Tags --}}
                    @if ($categoryFilter !== 'all' || $uptimeFilter !== 'all')
                        <div class="search-active-filters">
                            @if ($categoryFilter !== 'all')
                                @php
                                    $catEnum = \App\Enum\Category::tryFrom($categoryFilter);
                                @endphp
                                <span class="filter-tag">
                                    Category: {{ $catEnum ? $catEnum->label() : $categoryFilter }}
                                    <a
                                        href="{{ route('search.index', array_merge(request()->query(), ['category' => 'all'])) }}">&times;</a>
                                </span>
                            @endif
                            @if ($uptimeFilter !== 'all')
                                <span class="filter-tag">
                                    Status: {{ ucfirst($uptimeFilter) }}
                                    <a
                                        href="{{ route('search.index', array_merge(request()->query(), ['uptime' => 'all'])) }}">&times;</a>
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Search Result Cards --}}
                    <div class="search-results-list">
                        @foreach ($links as $link)
                            <div class="search-result-card">
                                <div class="search-result-top">
                                    <div class="search-result-title-row">
                                        <h3 class="search-result-title">
                                            <a href="{{ route('link.show', $link->slug) }}"
                                                style="font-weight:700;">{{ $link->title }}</a>
                                        </h3>
                                        <span class="uptime-badge {{ $link->uptime_status->cssClass() }}">
                                            {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                                        </span>
                                    </div>
                                    <div class="search-result-url">
                                        <span class="onion-v3-shorthand">{{ $link->url }}</span>
                                        <span class="geo-tag" style="margin-left:0.5rem;"><i class="fas fa-globe"></i> Global</span>
                                    </div>
                                </div>

                                @if ($link->description)
                                    <p class="search-result-desc">{{ Str::limit($link->description, 200) }}</p>
                                @endif

                                <div class="search-result-meta">
                                    <a href="{{ route('category.show', $link->category->value) }}" class="search-result-category">
                                        {{ $link->category->label() }}
                                    </a>
                                    <span class="search-result-meta-item">
                                        &#128336; {{ $link->created_at->diffForHumans() }}
                                    </span>
                                    @if ($link->last_check)
                                        <span class="search-result-meta-item">
                                            &#9745; Checked {{ $link->last_check->diffForHumans() }}
                                        </span>
                                    @endif
                                    @if ($link->check_count > 0)
                                        <span class="search-result-meta-item">
                                            {{ $link->check_count }} check{{ $link->check_count > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                    @if ($link->user)
                                        <span class="search-result-meta-item">
                                            by {{ $link->user->username }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($links->hasPages())
                        <div class="pagination">
                            {{ $links->links('pagination.simple') }}
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="search-results-sidebar">
                    {{-- Category Breakdown --}}
                    @if (count($categoryBreakdown) > 0)
                        <div class="sidebar-card">
                            <div class="sidebar-card-header">Results by Category</div>
                            <div class="sidebar-card-body" style="padding:0;">
                                <ul class="categories-list">
                                    @foreach ($categoryBreakdown as $catValue => $count)
                                        @php
                                            $catObj = \App\Enum\Category::tryFrom($catValue);
                                        @endphp
                                        @if ($catObj)
                                            <li>
                                                <a
                                                    href="{{ route('search.index', ['q' => $query, 'category' => $catValue, 'sort' => $sortBy, 'uptime' => $uptimeFilter]) }}">
                                                    {{ $catObj->label() }}
                                                    <span class="count">{{ $count }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Search Tips --}}
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">Search Tips</div>
                        <div class="sidebar-card-body" style="font-size:0.8rem;color:var(--text-secondary);line-height:1.6;">
                            <ul style="list-style:none;display:flex;flex-direction:column;gap:0.4rem;">
                                <li>&#9679; Use keywords from the service name</li>
                                <li>&#9679; Try searching by .onion URL</li>
                                <li>&#9679; Filter by category to narrow results</li>
                                <li>&#9679; Use status filter to find online sites</li>
                                <li>&#9679; Minimum 2 characters required</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Directory Stats --}}
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">Directory Stats</div>
                        <div class="sidebar-card-body">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                                <div class="text-center">
                                    <div style="font-size:1.2rem;font-weight:700;color:var(--accent-green);">
                                        {{ number_format($totalLinks) }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.65rem;text-transform:uppercase;">Total
                                        Links
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div style="font-size:1.2rem;font-weight:700;color:var(--accent-cyan);">
                                        {{ number_format($onlineLinks) }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.65rem;text-transform:uppercase;">Online
                                        Now</div>
                                </div>
                            </div>
                        </div>
        @else
                        {{-- No Results --}}
                        <div class="search-no-results">
                            <div class="search-no-results-icon">&#128533;</div>
                            <h2>No results found</h2>
                            <p>No .onion links match "<strong>{{ e($query) }}</strong>"</p>

                            <div class="search-no-results-tips">
                                <h3>Suggestions:</h3>
                                <ul>
                                    <li>Check your spelling</li>
                                    <li>Try using different or more general keywords</li>
                                    <li>Remove filters to broaden your search</li>
                                    <li>Search by partial .onion URL instead</li>
                                </ul>
                            </div>

                            @if ($categoryFilter !== 'all' || $uptimeFilter !== 'all')
                                <a href="{{ route('search.index', ['q' => $query]) }}" class="btn btn-secondary"
                                    style="margin-top:1rem;">
                                    Clear All Filters &amp; Retry
                                </a>
                            @endif

                            {{-- Browse categories instead --}}
                            <div class="search-browse-categories">
                                <h3>Or browse by category:</h3>
                                <div class="search-category-chips">
                                    @foreach ($categories as $cat)
                                        <a href="{{ route('category.show', $cat->value) }}"
                                            class="category-chip">{{ $cat->label() }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
    @else
                    {{-- Default state (no query yet) --}}
                    <div class="search-empty-state">
                        {{-- Popular Categories --}}
                        @if (count($popularCategories) > 0)
                            <div class="search-popular-section">
                                <h2 class="search-section-title">Popular Categories</h2>
                                <div class="search-popular-grid">
                                    @foreach ($popularCategories as $catValue => $count)
                                        @php
                                            $catObj = \App\Enum\Category::tryFrom($catValue);
                                        @endphp
                                        @if ($catObj)
                                            <a href="{{ route('category.show', $catObj->value) }}" class="search-popular-card">
                                                <div class="search-popular-count">{{ $count }}</div>
                                                <div class="search-popular-label">{{ $catObj->label() }}</div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Browse All Categories --}}
                        <div class="search-popular-section">
                            <h2 class="search-section-title">Browse All Categories</h2>
                            <div class="search-category-chips" style="justify-content:center;">
                                @foreach ($categories as $cat)
                                    <a href="{{ route('category.show', $cat->value) }}"
                                        class="category-chip">{{ $cat->label() }}</a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Search Tips --}}
                        <div class="search-tips-section">
                            <h2 class="search-section-title">How to Search</h2>
                            <div class="search-tips-grid">
                                <div class="search-tip-card">
                                    <div class="search-tip-icon fa fa-search"></div>
                                    <h3>Keywords</h3>
                                    <p>Search by service name, description, or keywords. Minimum 2 characters.</p>
                                </div>
                                <div class="search-tip-card">
                                    <div class="search-tip-icon fa fa-link"></div>
                                    <h3>URL Search</h3>
                                    <p>Paste a partial .onion URL to find a specific service in the directory.</p>
                                </div>
                                <div class="search-tip-card">
                                    <div class="search-tip-icon">&#9881;</div>
                                    <h3>Filters</h3>
                                    <p>Use category and status filters to narrow down your search results.</p>
                                </div>
                                <div class="search-tip-card">
                                    <div class="search-tip-icon">&#8645;</div>
                                    <h3>Sorting</h3>
                                    <p>Sort by relevance, date, title, or uptime check frequency.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Stats Bar --}}
                        <div class="search-stats-bar">
                            <div class="search-stats-item">
                                <span class="search-stats-value">{{ number_format($totalLinks) }}</span>
                                <span class="search-stats-label">Indexed Links</span>
                            </div>
                            <div class="search-stats-divider"></div>
                            <div class="search-stats-item">
                                <span class="search-stats-value">{{ number_format($onlineLinks) }}</span>
                                <span class="search-stats-label">Currently Online</span>
                            </div>
                            <div class="search-stats-divider"></div>
                            <div class="search-stats-item">
                                <span class="search-stats-value">{{ count($categories) }}</span>
                                <span class="search-stats-label">Categories</span>
                            </div>

                        </div>
                    </div>
                @endif

</x-app.layouts>