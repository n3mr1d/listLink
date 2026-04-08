<x-app.layouts
    title="{{ $query ? 'Results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    {{-- ══════════════════════════════════════════════════════
         SEARCH ENGINE
    ══════════════════════════════════════════════════════ --}}
    <div class="min-h-[50vh] pb-8">

        {{-- ── Search header ───────────────────────────────── --}}
        <div class="pt-8 px-4 sm:px-6 max-w-[800px] mx-auto">
            <form action="{{ route('search.index') }}" method="GET" id="se-search-form">
                @if(!$query)
                {{-- No query → centered hero search --}}
                <div class="flex flex-col items-center text-center py-12 pb-8">
                    <x-app.logo class="w-[60px] h-[60px] block mb-4" />
                    <h1 class="text-[2.2rem] font-bold text-white tracking-tight leading-none mb-1">Onion Search</h1>
                    <p class="text-sm text-[var(--text-muted)] mb-8">
                        {{ number_format($totalLinks) }} links &middot; {{ number_format($onlineLinks) }} online
                    </p>
                    <div class="w-full max-w-[600px] flex items-center gap-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-full px-5 py-3 transition-colors duration-150 focus-within:border-[var(--accent-blue)]">
                        <svg class="w-[17px] h-[17px] text-[var(--text-muted)] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input id="se-input" type="text" name="q" value="{{ $query }}"
                            placeholder="Search the Onion network&hellip;" aria-label="Search onion services"
                            autofocus autocomplete="off" spellcheck="false"
                            class="flex-1 bg-transparent border-none outline-none text-[var(--text-primary)] text-base placeholder:text-[var(--text-muted)] min-w-0">
                        <button type="submit" id="se-submit-btn"
                            class="bg-transparent border-none text-[var(--accent-blue)] text-sm font-semibold cursor-pointer whitespace-nowrap hover:text-white">
                            Search
                        </button>
                    </div>
                </div>
                @else
                {{-- Has query → compact search bar --}}
                <div class="w-full flex items-center gap-3 bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-md px-5 py-2.5 transition-colors duration-150 focus-within:border-[var(--accent-blue)]">
                    <svg class="w-[17px] h-[17px] text-[var(--text-muted)] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input id="se-input" type="text" name="q" value="{{ $query }}"
                        placeholder="Search the Onion network&hellip;" aria-label="Search onion services"
                        autocomplete="off" spellcheck="false"
                        class="flex-1 bg-transparent border-none outline-none text-[var(--text-primary)] text-base placeholder:text-[var(--text-muted)] min-w-0">
                    <button type="submit" id="se-submit-btn"
                        class="bg-transparent border-none text-[var(--accent-blue)] text-sm font-semibold cursor-pointer whitespace-nowrap hover:text-white">
                        Search
                    </button>
                </div>

                {{-- Filters --}}
                <div class="flex items-center flex-wrap gap-2 pt-3">
                    <select name="sort" id="se-sort-select" onchange="this.form.submit()"
                        class="bg-[var(--bg-secondary)] border border-[var(--border-color)] text-[var(--text-primary)] rounded-md px-2.5 py-1 text-[0.82rem] cursor-pointer outline-none focus:border-[var(--accent-blue)]">
                        <option value="relevance" {{ $sortBy === 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="most_checked" {{ $sortBy === 'most_checked' ? 'selected' : '' }}>Most Checked</option>
                        <option value="recently_checked" {{ $sortBy === 'recently_checked' ? 'selected' : '' }}>Recently Checked</option>
                    </select>

                    <select name="category" id="se-category-select" onchange="this.form.submit()"
                        class="bg-[var(--bg-secondary)] border border-[var(--border-color)] text-[var(--text-primary)] rounded-md px-2.5 py-1 text-[0.82rem] cursor-pointer outline-none focus:border-[var(--accent-blue)]">
                        <option value="all" {{ $categoryFilter === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}" {{ $categoryFilter === $cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
                        @endforeach
                    </select>

                    <select name="uptime" id="se-uptime-select" onchange="this.form.submit()"
                        class="bg-[var(--bg-secondary)] border border-[var(--border-color)] text-[var(--text-primary)] rounded-md px-2.5 py-1 text-[0.82rem] cursor-pointer outline-none focus:border-[var(--accent-blue)]">
                        <option value="all" {{ $uptimeFilter === 'all' ? 'selected' : '' }}>Any Status</option>
                        <option value="online" {{ $uptimeFilter === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ $uptimeFilter === 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>

                    @if($categoryFilter !== 'all' || $uptimeFilter !== 'all' || $sortBy !== 'relevance')
                        <a href="{{ route('search.index', ['q' => $query]) }}" id="se-clear-filters"
                            class="text-[0.8rem] text-[var(--text-muted)] no-underline px-2.5 py-1 hover:text-[var(--accent-red)]">
                            Clear filters
                        </a>
                    @endif
                </div>
                @endif
            </form>
        </div>

        {{-- ── Sponsor banner ────────────────────────────── --}}
        @if(isset($headerAds) && $headerAds->count() > 0)
        @php $ad = $headerAds->first(); @endphp
        <div class="max-w-[800px] mx-auto mt-4 px-4 sm:px-6 flex items-center gap-3" id="se-sponsor-banner">
            <span class="text-[0.65rem] font-bold uppercase tracking-wider text-[var(--text-muted)] whitespace-nowrap">Sponsored</span>
            <a href="{{ $ad->url }}" target="_blank" rel="noopener noreferrer nofollow" id="se-sponsor-link"
                class="flex items-center gap-2 no-underline bg-[var(--bg-secondary)] border border-[var(--border-color)] rounded-md px-4 py-2 flex-1 min-w-0 overflow-hidden">
                @if($ad->banner_path)
                    <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" loading="lazy"
                        class="h-10 w-auto object-contain block">
                @else
                    <span class="text-sm font-semibold text-[var(--text-primary)] whitespace-nowrap overflow-hidden text-ellipsis">{{ $ad->title }}</span>
                    <span class="text-xs text-[var(--text-muted)] whitespace-nowrap">Premium</span>
                @endif
            </a>
        </div>
        @endif

        {{-- ── Results ───────────────────────────────────── --}}
        @if($query)
        <div class="max-w-[800px] mx-auto px-4 sm:px-6">

            @if($links && $links->total() > 0)
                {{-- Result count --}}
                <p class="text-[0.82rem] text-[var(--text-muted)] mt-0 mb-5" id="se-result-count">
                    <strong class="text-[var(--text-primary)]">{{ number_format($links->total()) }}</strong>
                    result{{ $links->total() !== 1 ? 's' : '' }} for
                    &ldquo;{{ e($query) }}&rdquo;
                    @if($searchTime)
                        <span class="text-[0.78rem]">({{ $searchTime }}ms)</span>
                    @endif
                </p>

                {{-- Result cards --}}
                <div class="flex flex-col" id="se-result-list">
                    @foreach($links as $link)
                    <div class="py-4 border-b border-[var(--border-color)] last:border-b-0" id="sr-{{ $link->id }}">
                        {{-- Title + badge --}}
                        <div class="flex items-center gap-3 flex-wrap mb-0.5">
                            <h3 class="text-base font-semibold m-0">
                                <a href="{{ route('link.show', $link->slug) }}"
                                    class="text-[var(--accent-blue)] no-underline hover:underline">{{ $link->title }}</a>
                            </h3>
                            <span class="uptime-badge {{ $link->uptime_status->cssClass() }}">
                                {{ $link->uptime_status->label() }}
                            </span>
                        </div>
                        {{-- URL --}}
                        <span class="text-[0.78rem] text-[var(--accent-green)] font-mono break-all block">{{ $link->url }}</span>
                        {{-- Description --}}
                        @if($link->description)
                        <p class="mt-2 mb-2.5 text-[0.88rem] text-[var(--text-secondary)] leading-relaxed">{{ Str::limit($link->description, 200) }}</p>
                        @endif
                        {{-- Meta --}}
                        <div class="flex items-center flex-wrap gap-1.5 text-[0.78rem] text-[var(--text-muted)]">
                            <a href="{{ route('category.show', $link->category->value) }}"
                                class="text-[var(--accent-blue)] no-underline font-medium hover:underline">{{ $link->category->label() }}</a>
                            <span class="opacity-50">&middot;</span>
                            <span>{{ $link->created_at->diffForHumans() }}</span>
                            @if($link->last_check)
                            <span class="opacity-50">&middot;</span>
                            <span>Checked {{ $link->last_check->diffForHumans() }}</span>
                            @endif
                            @if($link->user)
                            <span class="opacity-50">&middot;</span>
                            <span>by {{ $link->user->username }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($links->hasPages())
                <div class="mt-8" id="se-pagination">
                    {{ $links->links('pagination.simple') }}
                </div>
                @endif

            @else
                {{-- No results --}}
                <div class="pt-8" id="se-no-results">
                    <p class="text-base text-[var(--text-primary)] mb-4">
                        No results found for &ldquo;<strong>{{ e($query) }}</strong>&rdquo;
                    </p>
                    <ul class="list-disc pl-6 text-[0.88rem] text-[var(--text-muted)] leading-8">
                        <li>Check your spelling</li>
                        <li>Try more general keywords</li>
                        <li>Search by partial .onion URL</li>
                        @if($categoryFilter !== 'all' || $uptimeFilter !== 'all')
                        <li><a href="{{ route('search.index', ['q' => $query]) }}" class="text-[var(--accent-blue)]">Remove filters and retry</a></li>
                        @endif
                    </ul>
                    <div class="mt-6">
                        <p class="text-[0.82rem] text-[var(--text-muted)] mb-2.5">Browse categories:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($categories as $cat)
                            <a href="{{ route('category.show', $cat->value) }}"
                                class="bg-[var(--bg-secondary)] text-[var(--text-primary)] border border-[var(--border-color)] rounded-full px-3 py-1 text-[0.8rem] no-underline hover:border-[var(--accent-blue)] hover:text-[var(--accent-blue)]">
                                {{ $cat->label() }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
        @endif

    </div>

    {{-- ══════════════════════════════════════════════════════
         DIRECTORY — User-submitted links (non-anonymous)
    ══════════════════════════════════════════════════════ --}}
    <section class="bg-[var(--bg-secondary)] border-t border-[var(--border-color)] py-10 px-4 sm:px-6" id="directory-menu" aria-label="Link Directory">
        <div class="max-w-[1100px] mx-auto">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white mb-0.5">Link Directory</h2>
                    <p class="text-[0.82rem] text-[var(--text-muted)] m-0">Verified links submitted by registered members</p>
                </div>
                <a href="{{ route('submit.create') }}" id="dir-submit-link"
                    class="inline-block bg-[var(--bg-primary)] text-[var(--accent-blue)] border border-[var(--accent-blue)] rounded-md px-4 py-2 text-[0.85rem] font-semibold no-underline whitespace-nowrap hover:bg-[var(--accent-blue)] hover:text-white">
                    + Submit a Link
                </a>
            </div>

            {{-- Category tabs --}}
            <div class="flex flex-nowrap overflow-x-auto gap-1 mb-6 pb-1 scrollbar-hide" id="dir-category-tabs">
                <a href="{{ request()->fullUrlWithQuery(['dir_page' => null, 'dir_cat' => null]) }}"
                   class="inline-block px-3.5 py-1.5 rounded-full text-[0.8rem] font-medium no-underline whitespace-nowrap border transition-colors duration-150 {{ !request('dir_cat') ? 'text-[var(--accent-blue)] border-[var(--accent-blue)] bg-[rgba(74,158,255,0.08)]' : 'text-[var(--text-muted)] border-transparent hover:text-[var(--text-primary)] hover:border-[var(--border-color)]' }}"
                   id="dir-tab-all">All</a>
                @foreach($categories as $cat)
                <a href="{{ request()->fullUrlWithQuery(['dir_page' => null, 'dir_cat' => $cat->value]) }}"
                   class="inline-block px-3.5 py-1.5 rounded-full text-[0.8rem] font-medium no-underline whitespace-nowrap border transition-colors duration-150 {{ request('dir_cat') === $cat->value ? 'text-[var(--accent-blue)] border-[var(--accent-blue)] bg-[rgba(74,158,255,0.08)]' : 'text-[var(--text-muted)] border-transparent hover:text-[var(--text-primary)] hover:border-[var(--border-color)]' }}"
                   id="dir-tab-{{ $cat->value }}">{{ $cat->label() }}</a>
                @endforeach
            </div>

            {{-- Directory table --}}
            @if($directoryLinks->count() > 0)
            <div class="overflow-x-auto border border-[var(--border-color)] rounded-md">
                <table class="w-full border-collapse text-sm" id="dir-link-table">
                    <thead>
                        <tr>
                            <th class="text-left px-4 py-2.5 text-[0.72rem] font-semibold uppercase tracking-wider text-[var(--text-muted)] bg-[var(--bg-primary)] border-b border-[var(--border-color)] whitespace-nowrap">Service</th>
                            <th class="text-left px-4 py-2.5 text-[0.72rem] font-semibold uppercase tracking-wider text-[var(--text-muted)] bg-[var(--bg-primary)] border-b border-[var(--border-color)] whitespace-nowrap">Category</th>
                            <th class="text-left px-4 py-2.5 text-[0.72rem] font-semibold uppercase tracking-wider text-[var(--text-muted)] bg-[var(--bg-primary)] border-b border-[var(--border-color)] whitespace-nowrap">Status</th>
                            <th class="text-left px-4 py-2.5 text-[0.72rem] font-semibold uppercase tracking-wider text-[var(--text-muted)] bg-[var(--bg-primary)] border-b border-[var(--border-color)] whitespace-nowrap">Last Checked</th>
                            <th class="text-left px-4 py-2.5 text-[0.72rem] font-semibold uppercase tracking-wider text-[var(--text-muted)] bg-[var(--bg-primary)] border-b border-[var(--border-color)] whitespace-nowrap">Added by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($directoryLinks as $dlink)
                        <tr id="dir-row-{{ $dlink->id }}"
                            class="{{ request('dir_cat') && request('dir_cat') !== $dlink->category->value ? 'hidden' : '' }} hover:bg-[var(--bg-hover)]">
                            {{-- Service --}}
                            <td class="px-4 py-3 border-b border-[var(--border-light)] align-top min-w-[200px] max-w-[360px]">
                                <div class="flex flex-col gap-0.5">
                                    <a href="{{ route('link.show', $dlink->slug) }}"
                                        class="text-[var(--accent-blue)] font-semibold text-[0.92rem] no-underline break-words hover:underline">{{ $dlink->title }}</a>
                                    @if($dlink->description)
                                    <span class="text-[0.78rem] text-[var(--text-muted)] leading-snug">{{ Str::limit($dlink->description, 72) }}</span>
                                    @endif
                                    <span class="text-[0.72rem] text-[var(--accent-green)] font-mono break-all">{{ Str::limit($dlink->url, 40) }}</span>
                                </div>
                            </td>
                            {{-- Category --}}
                            <td class="px-4 py-3 border-b border-[var(--border-light)] align-top">
                                <a href="{{ route('category.show', $dlink->category->value) }}"
                                    class="inline-block px-2.5 py-0.5 rounded-full text-[0.72rem] font-semibold text-[var(--text-muted)] border border-[var(--border-color)] no-underline whitespace-nowrap hover:border-[var(--accent-blue)] hover:text-[var(--accent-blue)]">
                                    {{ $dlink->category->label() }}
                                </a>
                            </td>
                            {{-- Status --}}
                            <td class="px-4 py-3 border-b border-[var(--border-light)] align-top">
                                @php $isOnline = $dlink->uptime_status->value === 'online'; @endphp
                                <span class="inline-flex items-center gap-1.5 text-[0.78rem] font-semibold whitespace-nowrap {{ $isOnline ? 'text-[var(--accent-green)]' : 'text-[var(--accent-red)]' }}">
                                    <span class="w-[7px] h-[7px] rounded-full shrink-0 {{ $isOnline ? 'bg-[var(--accent-green)]' : 'bg-[var(--accent-red)]' }}"></span>
                                    {{ $isOnline ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            {{-- Last Checked --}}
                            <td class="px-4 py-3 border-b border-[var(--border-light)] align-top text-[0.78rem] text-[var(--text-muted)] whitespace-nowrap">
                                @if($dlink->last_check)
                                    {{ $dlink->last_check->diffForHumans() }}
                                @else
                                    <span class="text-[var(--border-color)]">Not yet</span>
                                @endif
                            </td>
                            {{-- Added by --}}
                            <td class="px-4 py-3 border-b border-[var(--border-light)] align-top text-[0.78rem] text-[var(--text-muted)] whitespace-nowrap">
                                @if($dlink->user)
                                    {{ $dlink->user->username }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($directoryLinks->hasPages())
            <div class="mt-6" id="dir-pagination">
                {{ $directoryLinks->links('pagination.simple') }}
            </div>
            @endif

            @else
            <div class="text-center py-12 text-[0.9rem] text-[var(--text-muted)]" id="dir-empty">
                <p>No links in the directory yet. <a href="{{ route('submit.create') }}" class="text-[var(--accent-blue)]">Be the first to submit one.</a></p>
            </div>
            @endif

        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════
         BOTTOM ADS — Full-width vertical stack
    ══════════════════════════════════════════════════════ --}}
    @if(isset($headerAds) && $headerAds->count() > 1)
    <section class="border-t border-[var(--border-color)] py-8 px-4 sm:px-6" id="se-bottom-ads" aria-label="Sponsored ads">
        <div class="max-w-[1100px] mx-auto">
            <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-wider text-[var(--text-muted)]">Sponsored</p>
            <div class="flex flex-col gap-3">
                @foreach($headerAds->skip(1) as $bottomAd)
                <a href="{{ $bottomAd->url }}" target="_blank" rel="noopener noreferrer nofollow"
                    id="bottom-ad-{{ $loop->index }}"
                    class="block w-full h-[90px] border border-[var(--border-color)] rounded-md overflow-hidden no-underline">
                    @if($bottomAd->banner_path)
                        <img src="{{ asset('storage/' . $bottomAd->banner_path) }}" alt="{{ $bottomAd->title }}" loading="lazy"
                            class="w-full h-full object-cover block">
                    @else
                        <div class="flex flex-col items-center justify-center h-full bg-[var(--bg-secondary)] gap-1 text-[var(--text-primary)]">
                            <span class="font-semibold">{{ $bottomAd->title }}</span>
                            <small class="text-[var(--text-muted)] text-[0.72rem]">Sponsored</small>
                        </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</x-app.layouts>