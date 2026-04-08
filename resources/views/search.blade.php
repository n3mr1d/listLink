<x-app.layouts
    title="{{ $query ? 'Results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    
    {{-- ═══ Search Hero (Only shown when no query) ═══ --}}
    <div class="py-16 flex flex-col items-center">
        <div class="w-full max-w-[600px] flex flex-col items-center text-center">
            <x-app.logo class="w-20 h-20 mb-6" />
            <h1 class="text-3xl font-extrabold text-white mb-2">Hidden Line</h1>
            <p class="text-gh-dim mb-10">We don't have any rules, search anything here with {{ number_format($indexedCount) }} indexed pages</p>

            <form action="{{ route('search.index') }}" method="GET" class="w-full">
                <div class="relative flex items-center bg-gh-bar-bg border border-gh-border rounded-full px-6 py-4 shadow-xl focus-within:ring-2 focus-within:ring-gh-accent focus-within:bg-gh-bg transition-all">
                    <i class="fas fa-search text-gh-dim mr-4 text-xl"></i>
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search the onion network..." class="flex-grow bg-transparent border-none text-white text-lg outline-none">
                    <button type="submit" class="bg-gh-accent text-gh-bg px-6 py-2 rounded-full font-bold ml-4 hover:bg-blue-400 transition-colors">Search</button>
                </div>
                @endif
            </form>
        </div>
    </div>
    
        <!-- Main Search Area -->
      
    {{-- Header Banner Ads --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div class="relative w-full max-w-[728px] h-[90px] mx-auto mb-6 rounded-md overflow-hidden border border-gh-border bg-gh-bg">
                {{-- Sponsored Label --}}
                <span class="absolute top-1.5 right-1.5 bg-black/70 text-gh-dim px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase z-10">
                    Sponsored
                </span>

                @if ($headerAd->banner_path)
                    <a href="{{ $headerAd->url }}" class="block w-full h-full">
                        <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}"
                            class="w-full h-full object-cover">
                    </a>
                @else
                    <a href="{{ $headerAd->url }}"
                        class="flex w-full h-full items-center justify-center bg-gradient-to-br from-[#1a2332] to-gh-bg no-underline">
                        <span class="text-xl font-bold text-white">{{ $headerAd->title }}</span>
                    </a>
                @endif

                {{-- Title/Premium Overlay --}}
                <div class="absolute bottom-0 left-0 w-full p-2 bg-gradient-to-t from-black/90 to-transparent flex justify-between items-end">
                    <div class="flex flex-col">
                        <a href="{{ $headerAd->url }}"
                            class="text-base font-bold text-white drop-shadow-md no-underline">{{ $headerAd->title }}</a>
                    </div>
                    <span class="bg-yellow-500/15 text-yellow-500 border border-yellow-500/30 px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase backdrop-blur-sm">
                        Premium
                    </span>
                </div>
            </div>
        @endforeach
    @endif

        {{-- ── Results ───────────────────────────────────── --}}
        @if($query)
        <div class="max-w-[800px] mx-auto px-4 sm:px-6">

            <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
                {{-- Main Results --}}
                <div class="flex flex-col">
                    {{-- Results Header --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 pb-4 border-b border-gh-border gap-4">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-white font-bold">{{ number_format($links->total()) }} result{{ $links->total() !== 1 ? 's' : '' }}</span>
                            <span class="text-gh-dim">for "<strong>{{ e($query) }}</strong>"</span>
                            @if ($searchTime)
                                <span class="text-xs text-gh-dim italic">({{ $searchTime }}ms)</span>
                            @endif
                        </div>
                        @if ($categoryFilter !== 'all' || $uptimeFilter !== 'all' || $sortBy !== 'relevance')
                            <a href="{{ route('search.index', ['q' => $query]) }}" class="text-xs text-gh-accent hover:underline">Clear filters</a>
                        @endif
                    </div>

                    {{-- Active Filter Tags --}}
                    @if ($categoryFilter !== 'all' || $uptimeFilter !== 'all')
                        <div class="flex flex-wrap gap-2 mb-6">
                            @if ($categoryFilter !== 'all')
                                @php
                                    $catEnum = \App\Enum\Category::tryFrom($categoryFilter);
                                @endphp
                                <span class="bg-gh-bar-bg border border-gh-border text-gh-text px-3 py-1 rounded-full text-xs flex items-center gap-2">
                                    Category: {{ $catEnum ? $catEnum->label() : $categoryFilter }}
                                    <a
                                        href="{{ route('search.index', array_merge(request()->query(), ['category' => 'all'])) }}" class="text-gh-dim hover:text-white">&times;</a>
                                </span>
                            @endif
                            @if ($uptimeFilter !== 'all')
                                <span class="bg-gh-bar-bg border border-gh-border text-gh-text px-3 py-1 rounded-full text-xs flex items-center gap-2">
                                    Status: {{ ucfirst($uptimeFilter) }}
                                    <a
                                        href="{{ route('search.index', array_merge(request()->query(), ['uptime' => 'all'])) }}" class="text-gh-dim hover:text-white">&times;</a>
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Search Result Cards --}}
                    <div class="flex flex-col gap-6">
                        @foreach ($links as $link)
                            <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-6 transition-all hover:bg-gh-bg hover:border-gh-accent group">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="text-xl font-bold m-0 leading-tight">
                                            <a href="{{ route('link.show', $link->slug) }}"
                                                class="text-gh-accent no-underline hover:underline">{{ $link->title }}</a>
                                        </h3>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase transition-colors {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500/10 text-green-500 border border-green-500/20' : ($link->uptime_status === \App\Enum\UptimeStatus::OFFLINE ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20') }}">
                                            {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs font-mono text-gh-dim">
                                        <span class="truncate max-w-[200px] md:max-w-none">{{ $link->url }}</span>
                                        <span class="flex items-center gap-1 px-1.5 py-0.5 bg-white/5 rounded"><i class="fas fa-globe"></i> Global</span>
                                    </div>
                                </div>

                                @if ($link->description)
                                    <p class="text-gh-text/80 text-[0.9rem] leading-relaxed mt-4 mb-4">{{ Str::limit($link->description, 200) }}</p>
                                @endif

                                <div class="flex flex-wrap items-center gap-y-2 gap-x-6 mt-2 pt-4 border-t border-white/5 text-[0.75rem] text-gh-dim">
                                    <a href="{{ route('category.show', $link->category->value) }}" class="text-gh-accent font-medium hover:underline">
                                        <i class="fas fa-folder text-[0.6rem] mr-1"></i> {{ $link->category->label() }}
                                    </a>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-clock text-[0.6rem]"></i> {{ $link->created_at->diffForHumans() }}
                                    </span>
                                    @if ($link->last_check)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-check-circle text-[0.6rem]"></i> Checked {{ $link->last_check->diffForHumans() }}
                                        </span>
                                    @endif
                                    @if ($link->check_count > 0)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-signal text-[0.6rem]"></i> {{ number_format($link->check_count) }} checks
                                        </span>
                                    @endif
                                    @if ($link->user)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-user text-[0.6rem]"></i> {{ $link->user->username }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($links->hasPages())
                        <div class="mt-12 flex justify-center">
                            {{ $links->links('pagination.simple') }}
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="flex flex-col gap-8">
                    {{-- Category Breakdown --}}
                    @if (count($categoryBreakdown) > 0)
                        <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
                            <div class="px-5 py-3 bg-white/5 border-b border-gh-border text-sm font-bold text-white uppercase tracking-wider">Results by Category</div>
                            <div class="p-0">
                                <ul class="list-none m-0 p-0">
                                    @foreach ($categoryBreakdown as $catValue => $count)
                                        @php
                                            $catObj = \App\Enum\Category::tryFrom($catValue);
                                        @endphp
                                        @if ($catObj)
                                            <li class="border-b border-white/5 last:border-0">
                                                <a
                                                    href="{{ route('search.index', ['q' => $query, 'category' => $catValue, 'sort' => $sortBy, 'uptime' => $uptimeFilter]) }}"
                                                    class="flex justify-between items-center px-5 py-3 text-sm text-gh-dim hover:bg-white/5 hover:text-gh-accent no-underline transition-colors">
                                                    {{ $catObj->label() }}
                                                    <span class="bg-gh-bg border border-gh-border px-2 py-0.5 rounded text-[0.65rem] font-bold text-gh-dim group-hover:border-gh-accent group-hover:text-gh-accent">{{ $count }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Search Tips --}}
                    <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
                        <div class="px-5 py-3 bg-white/5 border-b border-gh-border text-sm font-bold text-white uppercase tracking-wider">Search Tips</div>
                        <div class="p-5 text-xs text-gh-dim leading-relaxed">
                            <ul class="flex flex-col gap-3 list-none p-0 m-0">
                                <li class="flex items-start gap-2"><span class="text-gh-accent mt-0.5">•</span> Use keywords from the service name</li>
                                <li class="flex items-start gap-2"><span class="text-gh-accent mt-0.5">•</span> Try searching by .onion URL</li>
                                <li class="flex items-start gap-2"><span class="text-gh-accent mt-0.5">•</span> Filter by category to narrow results</li>
                                <li class="flex items-start gap-2"><span class="text-gh-accent mt-0.5">•</span> Use status filter to find online sites</li>
                                <li class="flex items-start gap-2"><span class="text-gh-accent mt-0.5">•</span> Minimum 2 characters required</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Directory Stats --}}
                    <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
                        <div class="px-5 py-3 bg-white/5 border-b border-gh-border text-sm font-bold text-white uppercase tracking-wider">Directory Stats</div>
                        <div class="p-5">
                            <div class="grid grid-cols-3 gap-2">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-400">
                                        {{ number_format($totalLinks) }}
                                    </div>
                                    <div class="text-gh-dim text-[0.6rem] uppercase font-bold tracking-tighter">Links</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-400">
                                        {{ number_format($onlineLinks) }}
                                    </div>
                                    <div class="text-gh-dim text-[0.6rem] uppercase font-bold tracking-tighter">Online</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-purple-400">
                                        {{ number_format($indexedCount) }}
                                    </div>
                                    <div class="text-gh-dim text-[0.6rem] uppercase font-bold tracking-tighter">Indexed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                        {{-- No Results --}}
                        <div class="flex flex-col items-center justify-center py-20 text-center animate-[gh-fade_0.6s_ease-out]">
                            <div class="text-6xl mb-6">😕</div>
                            <h2 class="text-2xl font-bold text-white mb-2">No results found</h2>
                            <p class="text-gh-dim mb-10">No .onion links match "<strong>{{ e($query) }}</strong>"</p>

                            <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-8 max-w-[500px] w-full text-left">
                                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                                    <i class="fas fa-lightbulb text-gh-accent"></i> Suggestions:
                                </h3>
                                <ul class="flex flex-col gap-3 list-none p-0 m-0 text-gh-dim text-sm">
                                    <li class="flex items-center gap-2"><span>•</span> Check your spelling</li>
                                    <li class="flex items-center gap-2"><span>•</span> Try using different or more general keywords</li>
                                    <li class="flex items-center gap-2"><span>•</span> Remove filters to broaden your search</li>
                                    <li class="flex items-center gap-2"><span>•</span> Search by partial .onion URL instead</li>
                                </ul>
                            </div>

                            @if ($categoryFilter !== 'all' || $uptimeFilter !== 'all')
                                <a href="{{ route('search.index', ['q' => $query]) }}" class="mt-8 bg-gh-btn-bg border border-gh-border text-gh-text px-6 py-2.5 rounded shadow-sm font-medium hover:bg-gh-btn-hover transition-colors">
                                    Clear All Filters & Retry
                                </a>
                            @endif

                            {{-- Browse categories instead --}}
                            <div class="mt-16 w-full max-w-[800px]">
                                <h3 class="text-white font-bold mb-6">Or browse by category:</h3>
                                <div class="flex flex-wrap justify-center gap-3">
                                    @foreach ($categories as $cat)
                                        <a href="{{ route('category.show', $cat->value) }}"
                                            class="bg-gh-bar-bg border border-gh-border text-gh-dim px-4 py-2 rounded-lg text-sm hover:border-gh-accent hover:text-gh-accent transition-all">{{ $cat->label() }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
    @else
                    {{-- Default state (no query yet) --}}
                  
                        {{-- Stats Bar --}}
                        <div class="w-full bg-gh-bar-bg border border-gh-border rounded-2xl p-8 flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-gh-border">
                            <div class="flex-1 text-center py-4 md:py-0">
                                <span class="block text-2xl font-bold text-white">{{ number_format($totalLinks) }}</span>
                                <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider font-bold">Directory Links</span>
                            </div>
                            <div class="flex-1 text-center py-4 md:py-0">
                                <span class="block text-2xl font-bold text-white">{{ number_format($indexedCount) }}</span>
                                <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider font-bold">Crawler Pages</span>
                            </div>
                            <div class="flex-1 text-center py-4 md:py-0">
                                <span class="block text-2xl font-bold text-green-400">{{ number_format($onlineLinks) }}</span>
                                <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider font-bold">Currently Online</span>
                            </div>
                            <div class="flex-1 text-center py-4 md:py-0">
                                <span class="block text-2xl font-bold text-blue-400">{{ count($categories) }}</span>
                                <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider font-bold">Categories</span>
                            </div>

                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</x-app.layouts>