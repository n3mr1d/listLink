<x-app.layouts title="Verified Tor .Onion Directory"
    description="The most reliable Tor hidden services directory. Explore thousands of verified .onion links with daily uptime monitoring.">

    {{-- ═══ Priority Banner Relay ═══ --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div class="relative w-full max-w-[728px] h-[90px] mx-auto mb-8 rounded-xl overflow-hidden border border-gh-border bg-gh-bg group">
                <span class="absolute top-2 right-2 bg-black/70 text-gh-sponsored px-2 py-0.5 rounded text-[10px] font-black uppercase z-10 border border-gh-sponsored/30">Sponsored</span>

                @if ($headerAd->banner_path)
                    <a href="{{ $headerAd->url }}" class="block w-full h-full">
                        <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}" class="w-full h-full object-cover">
                    </a>
                @else
                    <a href="{{ $headerAd->url }}" class="flex w-full h-full items-center justify-center bg-gradient-to-br from-[#1a2332] to-gh-bg no-underline font-bold text-white group-hover:text-gh-accent transition-colors">
                        {{ $headerAd->title }}
                    </a>
                @endif
            </div>
        @endforeach
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-12">
        {{-- ── Main Directory Feed ── --}}
        <div>
            <div class="mb-12 pb-6 border-b border-gh-border">
                <h1 class="text-3xl font-black text-white tracking-tighter uppercase italic">The Registry</h1>
                <p class="text-gh-dim text-sm font-medium">Verified .onion services currently broadcasting on the network.</p>
            </div>

            @php
                $grouped = $links->groupBy(fn($link) => $link->category->value);
            @endphp

            @foreach ($categories as $category)
                @if (isset($grouped[$category->value]) && $grouped[$category->value]->count() > 0)
                    <section class="mb-16">
                        <div class="flex items-center justify-between mb-5 px-1">
                            <h2 class="text-lg font-black text-white tracking-widest uppercase border-l-4 border-gh-accent pl-4">{{ $category->label() }}</h2>
                            <a href="{{ route('category.show', $category->value) }}" class="text-[10px] font-black text-gh-accent hover:text-white uppercase tracking-tighter">Segment Full Report &rarr;</a>
                        </div>

                        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-2xl">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gh-bg/50 border-b border-gh-border">
                                        <th class="px-6 py-4 text-[10px] font-black text-gh-dim uppercase tracking-widest leading-none">Identity / URL</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gh-dim uppercase tracking-widest leading-none hidden md:table-cell">Last Sync</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gh-dim uppercase tracking-widest leading-none w-[120px]">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gh-border/50">
                                    {{-- Sponsored insertion --}}
                                    @if ($loop->first && isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                                        @foreach ($sponsoredLinks as $sponsoredLink)
                                            <tr class="bg-gh-sponsored/5 hover:bg-gh-sponsored/10 transition-colors">
                                                <td class="px-6 py-5">
                                                    <div class="flex flex-col gap-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-[9px] font-black bg-gh-sponsored text-gh-bg px-1.5 rounded leading-tight">AD</span>
                                                            <a href="{{ $sponsoredLink->url }}" class="text-sm font-bold text-gh-sponsored hover:underline no-underline">{{ $sponsoredLink->title }}</a>
                                                        </div>
                                                        <span class="text-[10px] font-mono text-gh-dim opacity-50">{{ $sponsoredLink->url }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 hidden md:table-cell uppercase text-[10px] font-black text-gh-sponsored/60">Priority Routing</td>
                                                <td class="px-6 py-5">
                                                    <span class="text-[9px] font-black border border-gh-sponsored/30 text-gh-sponsored px-2 py-0.5 rounded-full uppercase">Promoted</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @foreach ($grouped[$category->value]->take(10) as $link)
                                        <tr class="hover:bg-gh-bg transition-colors">
                                            <td class="px-6 py-5">
                                                <div class="flex flex-col gap-1">
                                                    <a href="{{ route('link.show', $link->slug) }}" class="text-sm font-bold text-gh-accent hover:text-white no-underline transition-colors leading-tight">{{ $link->title }}</a>
                                                    <span class="text-[10px] font-mono text-gh-dim opacity-40 truncate max-w-[200px]">{{ $link->url }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 hidden md:table-cell text-[10px] font-medium text-gh-dim">
                                                {{ $link->last_check ? $link->last_check->diffForHumans() : 'Standby' }}
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full border {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'border-green-500/30 bg-green-500/5 text-green-500' : 'border-red-500/30 bg-red-500/5 text-red-500' }}">
                                                    <div class="w-1 h-1 rounded-full {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                                                    <span class="text-[9px] font-black uppercase tracking-tighter">{{ $link->uptime_status->label() }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            @endforeach

            {{-- ── Network Expansion Log ── --}}
            <section class="mt-20">
                <div class="flex items-center gap-3 mb-8">
                    <svg class="w-5 h-5 text-gh-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
                    <h2 class="text-lg font-black text-white uppercase tracking-widest">Expansion Log</h2>
                </div>
                
                <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-xl">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-gh-border/50">
                            @foreach ($recentlyAddedLinks->take(5) as $link)
                                <tr class="hover:bg-gh-bg group transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-lg bg-gh-accent/10 flex items-center justify-center text-gh-accent font-black text-[10px] group-hover:bg-gh-accent group-hover:text-gh-bg transition-all">#{{ $loop->iteration }}</div>
                                            <div class="flex flex-col">
                                                <a href="{{ route('link.show', $link->slug) }}" class="text-xs font-bold text-gh-text group-hover:text-gh-accent no-underline">{{ $link->title }}</a>
                                                <span class="text-[10px] text-gh-dim opacity-50">{{ $link->category->label() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest">{{ $link->created_at->diffForHumans() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Pagination Relay --}}
            <div class="mt-16 flex justify-center">
                {{ $links->links('pagination.simple') }}
            </div>
        </div>

        {{-- ── System Analytics (Sidebar) ── --}}
        <aside class="space-y-12">
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-2xl">
                <h3 class="text-[10px] font-black text-gh-dim uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> System Vitals
                </h3>
                <div class="space-y-8">
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gh-dim uppercase">Active Nodes</span>
                        <span class="text-2xl font-black text-white leading-none">{{ number_format($stats['total_links']) }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gh-dim uppercase">Broadcasting Now</span>
                        <span class="text-2xl font-black text-green-400 leading-none">{{ number_format($stats['online_links']) }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-black text-gh-dim uppercase">Indexed Segments</span>
                        <span class="text-2xl font-black text-blue-400 leading-none">{{ number_format($stats['indexed_count']) }}</span>
                    </div>
                </div>
                <div class="mt-10 pt-6 border-t border-gh-border">
                    <a href="{{ route('advertise.create') }}" class="block w-full py-3 bg-gh-accent text-gh-bg hover:bg-blue-300 rounded-xl text-center text-[10px] font-black uppercase tracking-widest no-underline transition-all">Submit Protocol Ad</a>
                </div>
            </div>

            <div>
                <h3 class="text-[10px] font-black text-gh-dim uppercase tracking-[0.2em] mb-6 border-l-2 border-gh-accent pl-3">Data Categories</h3>
                <div class="space-y-1">
                    @foreach ($categories as $category)
                        <a href="{{ route('category.show', $category->value) }}" class="flex justify-between items-center px-4 py-2.5 rounded-xl text-xs font-semibold text-gh-dim hover:text-gh-accent hover:bg-gh-bg transition-all no-underline">
                            {{ $category->label() }}
                            <svg class="w-3 h-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Support Relay --}}
            <div class="p-6 bg-gh-bar-bg border border-gh-border rounded-2xl">
                <h3 class="text-[10px] font-black text-gh-dim uppercase tracking-[0.2em] mb-3">Protocol Health</h3>
                <p class="text-[10px] leading-relaxed text-gh-dim mb-6 opacity-60">Help maintain decentralized infrastructure by supporting the relay network.</p>
                <div class="flex flex-col gap-3">
                    <div class="p-3 bg-gh-bg border border-gh-border rounded-xl flex items-center justify-between">
                        <span class="text-[10px] font-black text-white">BTC Address</span>
                        <span class="text-[10px] font-mono text-gh-accent">See Config</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</x-app.layouts>