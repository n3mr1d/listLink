<x-app.layouts title="{{ $query ? 'Results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">
        {{-- ═══ Global Banner Area (Ads at the very top) ═══ --}}
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div class="flex flex-col gap-4 mb-8">
                @foreach ($headerAds as $ad)
                    <div class="relative w-full max-w-[970px] mx-auto h-[90px] rounded-xl overflow-hidden border border-gh-border bg-gh-bar-bg group shadow-2xl transition-all hover:border-gh-accent/30">
                        <span class="absolute top-2 right-2 bg-black/70 text-gh-sponsored px-2 py-0.5 rounded text-[10px] font-black uppercase z-10 border border-gh-sponsored/30">Sponsored</span>
                        @if ($ad->banner_path)
                            <a href="{{ $ad->url }}" class="block w-full h-full">
                                <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover transition-transform duration-700 ">
                            </a>
                        @else
                            <a href="{{ $ad->url }}" class="flex w-full h-full items-center justify-center bg-gradient-to-br from-[#1a2332] to-gh-bg no-underline font-bold text-white group-hover:text-gh-accent transition-all px-10">
                                <div class="text-center font-black uppercase tracking-widest text-sm italic">{{ $ad->title }}</div>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ═══ Search Navigation (Now below banners) ═══ --}}
        <div class="flex flex-col md:flex-row items-center gap-6 mt-12 mb-10 border-t border-gh-border pb-8">
            <form action="{{ route('search.index') }}" method="GET" class="w-full max-w-[650px]">
                <div class="relative flex items-center bg-gh-bar-bg border border-gh-border rounded-full px-5 py-2.5 focus-within:border-gh-accent focus-within:bg-gh-bg transition-all shadow-lg">
                    <span class="text-gh-dim mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $query }}" placeholder="Adjusting frequency..." class="flex-grow bg-transparent border-none text-white text-sm outline-none font-medium">
                    @if($query)
                        <button type="submit" class="text-gh-accent font-black text-[10px] uppercase tracking-tighter bg-gh-accent/10 hover:bg-gh-accent hover:text-gh-bg px-3 py-1 rounded-full transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round"/></svg>
                        </button>
                    @endif
                </div>
            </form>
        </div>

        @if($query)
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-12">
                {{-- Main Content --}}
                <div class="space-y-10">
                    <div class="flex items-center justify-between text-[0.65rem] font-bold text-gh-dim uppercase tracking-widest border-b border-gh-border pb-3">
                        <span>Revealing <span class="text-white">{{ number_format($links->total()) }} signatures</span></span>
                        <span class="italic">{{ $searchTime ?? '?' }}ms response</span>
                    </div>

                    @if ($links && $links->total() > 0)
                        <div class="space-y-10">
                            @foreach ($links as $link)
                                <article class="group relative flex flex-col gap-2">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex flex-col min-w-0">
                                            <h3 class="text-lg font-bold leading-tight">
                                                <a href="{{ route('link.show', $link->slug) }}" class="text-gh-accent hover:text-blue-300 no-underline transition-colors">{{ $link->title }}</a>
                                            </h3>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[10px] font-mono text-gh-dim truncate opacity-60">{{ $link->url }}</span>
                                                <div class="w-1.5 h-1.5 rounded-full {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500 ' : 'bg-red-500' }}"></div>
                                            </div>
                                        </div>
                                        <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter border {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'border-green-500/30 text-green-500 bg-green-500/5' : 'border-red-500/30 text-red-500 bg-red-500/5' }}">
                                            {{ $link->uptime_status->label() }}
                                        </span>
                                    </div>

                                    @if ($link->description)
                                        <p class="text-gh-text/70 text-sm leading-relaxed max-w-[700px]">
                                            {{ Str::limit($link->description, 280) }}
                                        </p>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-4 text-[10px] font-black text-gh-dim uppercase tracking-widest mt-1">
                                        <span class="text-gh-accent/80">{{ $link->category->label() }}</span>
                                        <span>•</span>
                                        <span>Detached {{ $link->created_at->diffForHumans() }}</span>
                                        @if($link->last_check)
                                            <span class="text-green-500/50">Verified {{ $link->last_check->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-20 pt-10 border-t border-gh-border">
                            {{ $links->links('pagination.simple') }}
                        </div>
                    @else
                        {{-- No Results --}}
                        <div class="py-20 flex flex-col items-center text-center">
                            <div class="w-20 h-20 bg-gh-bar-bg rounded-3xl flex items-center justify-center border border-gh-border mb-6">
                                <svg class="w-8 h-8 text-gh-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round"/></svg>
                            </div>
                            <h2 class="text-2xl font-black text-white mb-2 uppercase tracking-tighter">Signature Lost</h2>
                            <p class="text-gh-dim max-w-sm mb-10">We couldn't triangulate any verified nodes for "<strong>{{ $query }}</strong>". Try adjusting your frequency or broadening parameters.</p>
                            <a href="{{ route('home') }}" class="bg-gh-bar-bg border border-gh-border text-gh-text px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:text-white transition-all shadow-xl">Return to Core</a>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-12">
                    {{-- Local Categories --}}
                    @if(count($categoryBreakdown) > 0)
                        <div>
                            <h3 class="text-[0.65rem] font-black text-gh-dim uppercase tracking-[0.2em] mb-6 border-l-2 border-gh-accent pl-3">Data Clusters</h3>
                            <div class="space-y-1">
                                @foreach($categoryBreakdown as $catVal => $count)
                                    @php $cat = \App\Enum\Category::tryFrom($catVal); @endphp
                                    @if($cat)
                                        <a href="{{ route('search.index', ['q' => $query, 'category' => $catVal]) }}" class="flex justify-between items-center group py-2 px-3 rounded-lg hover:bg-gh-bar-bg transition-all no-underline">
                                            <span class="text-xs font-medium text-gh-dim group-hover:text-gh-accent transition-colors">{{ $cat->label() }}</span>
                                            <span class="text-[10px] font-black text-gh-dim bg-gh-bg border border-gh-border px-1.5 rounded opacity-40 group-hover:opacity-100 transition-all">{{ $count }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Sidebar Specific Ads (Shortened) --}}
                    @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                        <div>
                            <h3 class="text-[0.65rem] font-black text-gh-dim uppercase tracking-[0.2em] mb-6 border-l-2 border-gh-sponsored pl-3">Priority Nodes</h3>
                            <div class="space-y-4">
                                @foreach ($sidebarAds as $ad)
                                    <a href="{{ $ad->url }}" class="group block no-underline">
                                        @if($ad->banner_path)
                                            <div class="w-full h-24 mb-2 rounded-lg overflow-hidden border border-gh-border">
                                                <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover transition-transform duration-500">
                                            </div>
                                        @endif
                                        <span class="text-xs font-bold text-white group-hover:text-gh-sponsored transition-colors leading-tight">{{ $ad->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        @else
            <script>window.location.href = "{{ route('home') }}";</script>
        @endif
    </div>
</x-app.layouts>