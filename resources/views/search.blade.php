<x-app.layouts
    title="{{ $query ? 'Results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">


    {{-- ═══ Search Hero (Shown when no query) ═══ --}}
    
    <div class="py-12 flex flex-col items-center">
        <div class="w-full max-w-[600px] flex flex-col items-center text-center">
            <x-app.logo class="mb-6 scale-125" />
            <h1 class="text-2xl font-extrabold text-white mb-1">Hidden Line</h1>
            <p class="text-gh-dim mb-8 text-sm">Indexed Onion Pages: {{ number_format($indexedCount) }}</p>

            <form action="{{ route('search.index') }}" method="GET" class="w-full">
                <div class="relative flex items-center bg-gh-bar-bg border border-gh-border rounded-lg px-5 py-3 shadow-md focus-within:ring-2 focus-within:ring-gh-accent focus-within:bg-gh-bg">
                    <span class="text-gh-dim mr-3 text-lg select-none">&#128269;</span>
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search the onion network..." class="flex-grow bg-transparent border-none text-white text-base outline-none">
                    <button type="submit" class="bg-gh-accent text-gh-bg px-5 py-2 rounded font-bold ml-2 hover:bg-blue-400">Search</button>
                </div>
            </form>
        </div>
    </div>

        {{-- Stats Bar (Default state) --}}
        <div class="max-w-[800px] mx-auto w-full bg-gh-bar-bg border border-gh-border rounded-2xl p-8 mt-10 flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-gh-border">
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
        </div>

    {{-- ═══ Header Banner Ads ═══ --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div class="relative w-full max-w-[728px] h-[90px] mx-auto mb-6 rounded-md overflow-hidden border border-gh-border bg-gh-bg">
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
                        class="flex w-full h-full items-center justify-center bg-gradient-to-br from-[#1a2332] to-gh-bg no-underline font-bold text-white">
                        {{ $headerAd->title }}
                    </a>
                @endif
            </div>
        @endforeach
    @endif
    @if($query)
        {{-- ── Results Area ── --}}
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 mt-6">
            @if ($links && $links->total() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
                    {{-- Main Results --}}
                    <div class="flex flex-col">
                        {{-- Results Header --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 pb-4 border-b border-gh-border gap-4">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-white font-bold">{{ number_format($links->total()) }} results</span>
                                <span class="text-gh-dim">for "<strong>{{ e($query) }}</strong>"</span>
                                @if ($searchTime)
                                    <span class="text-xs text-gh-dim italic">({{ $searchTime }}ms)</span>
                                @endif
                            </div>
                        </div>

                        {{-- Result Cards --}}
                        <div class="flex flex-col gap-6">
                            @foreach ($links as $link)
                                <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-6 transition-all hover:border-gh-accent group">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-start justify-between gap-4">
                                            <h3 class="text-xl font-bold m-0 leading-tight">
                                                <a href="{{ route('link.show', $link->slug) }}" class="text-gh-accent no-underline hover:underline">{{ $link->title }}</a>
                                            </h3>
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500/10 text-green-500 border border-green-500/20' : 'bg-red-500/10 text-red-500 border border-red-500/20' }}">
                                                {{ $link->uptime_status->label() }}
                                            </span>
                                        </div>
                                        <div class="font-mono text-xs text-gh-dim truncate">{{ $link->url }}</div>
                                    </div>

                                    @if ($link->description)
                                        <p class="text-gh-text/80 text-sm leading-relaxed mt-3 mb-0">{{ Str::limit($link->description, 200) }}</p>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-white/5 text-[0.7rem] text-gh-dim">
                                        <span class="text-gh-accent font-medium">{{ $link->category->label() }}</span>
                                        <span>&#128337; {{ $link->created_at->diffForHumans() }}</span>
                                        @if($link->last_check)
                                            <span>&#9989; Checked {{ $link->last_check->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-10">
                            {{ $links->links('pagination.simple') }}
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="flex flex-col gap-6">
                        {{-- Category breakdown --}}
                        @if(count($categoryBreakdown) > 0)
                            <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden">
                                <div class="px-4 py-2.5 bg-white/5 border-b border-gh-border text-[0.7rem] font-bold text-gh-dim uppercase">By Category</div>
                                <div class="divide-y divide-white/5">
                                    @foreach($categoryBreakdown as $catVal => $count)
                                        @php $cat = \App\Enum\Category::tryFrom($catVal); @endphp
                                        @if($cat)
                                            <a href="{{ route('search.index', ['q' => $query, 'category' => $catVal]) }}" class="flex justify-between items-center px-4 py-2.5 text-xs text-gh-dim hover:text-gh-accent no-underline transition-colors">
                                                {{ $cat->label() }}
                                                <span class="text-[0.65rem] opacity-50">{{ $count }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Inline Ads --}}
                        <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-4 text-center">
                            <p class="text-[0.65rem] font-bold text-gh-dim uppercase mb-2">Want to list here?</p>
                            <a href="{{ route('advertise.create') }}" class="block w-full bg-gh-accent text-gh-bg py-2 rounded-md font-bold text-xs no-underline">Advertise</a>
                        </div>
                    </div>
                </div>
            @else
                {{-- No Results --}}
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="text-6xl mb-6">😕</div>
                    <h2 class="text-2xl font-bold text-white mb-2">No results found</h2>
                    <p class="text-gh-dim mb-8">No matching onion services for "<strong>{{ e($query) }}</strong>"</p>
                    <a href="{{ route('search.index') }}" class="bg-gh-bar-bg border border-gh-border text-white px-6 py-2.5 rounded-lg font-medium hover:bg-gh-border transition-colors">Return to Search</a>
                </div>
            @endif
        </div>
    @endif

</x-app.layouts>