<x-app.layouts title="Admin Dashboard">

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Admin Control Panel</h1>
            <p class="text-gh-dim text-sm">Real-time oversight of the Hidden Line network.</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('admin.crawler.crawl-all') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gh-accent text-gh-bg px-4 py-2 rounded-lg font-black text-xs uppercase tracking-widest hover:bg-blue-400 no-underline transition-all">
                    <i class="fas fa-spider mr-2"></i> Crawl All
                </button>
            </form>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
            ['Directory (' . $stats['registered_links'] . ')', route('admin.links'), request()->routeIs('admin.links')],
            ['Ad Queue (' . $stats['pending_ads'] . ')', route('admin.ads'), request()->routeIs('admin.ads')],
            ['Uptime History', route('admin.uptime-logs'), request()->routeIs('admin.uptime-logs')],
            ['Access Control', route('admin.blacklist'), request()->routeIs('admin.blacklist')],
            ['Crawler Engine', route('admin.crawler.index'), request()->routeIs('admin.crawler.*')],
            ['Email Harvesting', route('admin.email-crawler.index'), request()->routeIs('admin.email-crawler.*')]
        ] as $item)
            <a href="{{ $item[1] }}" class="px-4 py-2.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(88,166,255,0.3)]' : 'text-gh-dim bg-white/5 border border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-12">
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center shadow-sm hover:border-gh-accent/40 transition-all cursor-default group">
            <div class="text-xs font-black text-gh-dim uppercase tracking-widest mb-3 group-hover:text-gh-accent">All Links</div>
            <div class="text-4xl font-black text-white">{{ number_format($stats['total_links']) }}</div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center shadow-sm hover:border-gh-accent/40 transition-all cursor-default group">
            <div class="text-xs font-black text-gh-dim uppercase tracking-widest mb-3 group-hover:text-gh-accent">Active Sync</div>
            <div class="text-4xl font-black text-white">{{ number_format($stats['active_links']) }}</div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center shadow-sm hover:border-green-500/40 transition-all cursor-default group">
            <div class="text-xs font-black text-gh-dim uppercase tracking-widest mb-3 group-hover:text-green-500">Directory</div>
            <div class="text-4xl font-black text-green-500">{{ number_format($stats['registered_links']) }}</div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center shadow-sm hover:border-blue-500/40 transition-all cursor-default group">
            <div class="text-xs font-black text-gh-dim uppercase tracking-widest mb-3 group-hover:text-blue-500">Discovery</div>
            <div class="text-4xl font-black text-blue-500">{{ number_format($stats['anonymous_links']) }}</div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center shadow-sm hover:border-purple-500/40 transition-all cursor-default group">
            <div class="text-xs font-black text-gh-dim uppercase tracking-widest mb-3 group-hover:text-purple-500">Ads Alert</div>
            <div class="text-4xl font-black text-purple-500">{{ number_format($stats['pending_ads']) }}</div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center shadow-sm hover:border-cyan-500/40 transition-all cursor-default group">
            <div class="text-xs font-black text-gh-dim uppercase tracking-widest mb-3 group-hover:text-cyan-400">Activity/24h</div>
            <div class="text-4xl font-black text-cyan-400">{{ number_format($stats['recent_checks']) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        {{-- Recent Links --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-lg">
            <div class="px-6 py-4 border-b border-gh-border bg-white/5 flex items-center justify-between">
                <h3 class="text-xs font-black text-white uppercase tracking-widest">Incoming Stream</h3>
                <i class="fas fa-rss text-gh-dim text-xs animate-pulse"></i>
            </div>
            <div class="divide-y divide-white/5 max-h-[500px] overflow-y-auto no-scrollbar">
                @forelse($recentLinks as $link)
                    <div class="px-6 py-5 hover:bg-white/[0.03] transition-colors group">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <span class="font-bold text-white group-hover:text-gh-accent transition-colors truncate">{{ $link->title }}</span>
                            @if ($link->user_id)
                                <span class="shrink-0 bg-green-500/10 text-green-500 border border-green-500/20 px-2 py-0.5 rounded text-[0.6rem] font-bold uppercase tracking-tighter">Verified</span>
                            @else
                                <span class="shrink-0 bg-blue-500/10 text-blue-500 border border-blue-500/20 px-2 py-0.5 rounded text-[0.6rem] font-bold uppercase tracking-tighter">Scraped</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-[0.7rem] text-gh-dim font-mono truncate max-w-[80%]">
                                {{ $link->url }}
                            </div>
                            <span class="text-[0.65rem] text-gh-dim/60 italic">{{ $link->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-inbox text-4xl text-white/5 mb-4"></i>
                        <p class="text-gh-dim text-sm italic">No data detected in the current session.</p>
                    </div>
                @endforelse
            </div>
            @if ($recentLinks->count() > 0)
                <div class="p-4 bg-white/[0.01] border-t border-gh-border text-center">
                    <a href="{{ route('admin.links') }}" class="text-[0.65rem] font-black uppercase text-gh-dim hover:text-white no-underline transition-colors tracking-widest">
                        Full Inventory &rarr;
                    </a>
                </div>
            @endif
        </div>

        {{-- System Health / Info --}}
        <div class="flex flex-col gap-8">
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-8 shadow-sm">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6">Service Health</h3>
                <div class="flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                            <span class="text-sm font-bold text-gh-dim">Primary Crawler</span>
                        </div>
                        <span class="text-xs font-black text-white uppercase bg-green-500/10 px-2 py-0.5 rounded">Operational</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                            <span class="text-sm font-bold text-gh-dim">Tor Proxy (Privoxy)</span>
                        </div>
                        <span class="text-xs font-black text-white uppercase bg-green-500/10 px-2 py-0.5 rounded">Operational</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></div>
                            <span class="text-sm font-bold text-gh-dim">Database Cluster</span>
                        </div>
                        <span class="text-xs font-black text-white uppercase bg-blue-500/10 px-2 py-0.5 rounded">Optimal</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gh-accent/10 to-transparent border border-gh-accent/20 rounded-2xl p-8">
                <h4 class="text-xs font-black text-gh-accent uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.ads.create') }}" class="bg-white/5 border border-white/5 p-4 rounded-xl text-center no-underline hover:border-gh-accent/50 transition-all group">
                        <div class="text-lg mb-2 text-gh-dim group-hover:text-white"><i class="fas fa-plus"></i></div>
                        <div class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest group-hover:text-white">New Ad</div>
                    </a>
                    <a href="{{ route('admin.blacklist') }}" class="bg-white/5 border border-white/5 p-4 rounded-xl text-center no-underline hover:border-red-500/50 transition-all group">
                        <div class="text-lg mb-2 text-gh-dim group-hover:text-white"><i class="fas fa-ban"></i></div>
                        <div class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest group-hover:text-white">Ban Domain</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app.layouts>