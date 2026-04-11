<x-app.layouts title="Admin Dashboard">

    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-gh-accent/10 flex items-center justify-center text-gh-accent">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <span class="text-[10px] font-black text-gh-accent uppercase tracking-[0.2em]">Master Control Protocol</span>
            </div>
            <h1 class="text-4xl font-black text-white italic tracking-tighter uppercase leading-none">Network Oversight</h1>
            <p class="text-gh-dim mt-3 text-sm font-medium">Global synchronization and moderation of the Hidden Line backbone.</p>
        </div>
        <div class="flex gap-4">
            <form action="{{ route('admin.crawler.crawl-all') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gh-accent text-gh-bg px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-300 hover:-translate-y-0.5 transition-all shadow-2xl flex items-center gap-3">
                    <i class="fa-solid fa-spider"></i> Execute Global Crawl
                </button>
            </form>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-3 overflow-x-auto pb-6 mb-12 border-b border-gh-border/50 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), request()->routeIs('admin.dashboard'), 'fa-chart-pie'],
            ['Registry (' . $stats['registered_links'] . ')', route('admin.links'), request()->routeIs('admin.links'), 'fa-server'],
            ['Ad Queue (' . $stats['pending_ads'] . ')', route('admin.ads'), request()->routeIs('admin.ads'), 'fa-bullhorn'],
            ['Uptime Logs', route('admin.uptime-logs'), request()->routeIs('admin.uptime-logs'), 'fa-heartbeat'],
            ['Security', route('admin.blacklist'), request()->routeIs('admin.blacklist'), 'fa-user-lock'],
            ['Crawler', route('admin.crawler.index'), request()->routeIs('admin.crawler.*'), 'fa-bug'],
            ['Extraction', route('admin.email-crawler.index'), request()->routeIs('admin.email-crawler.*'), 'fa-envelope-open-text']
        ] as $item)
            <a href="{{ $item[1] }}" class="group flex items-center gap-3 px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_4px_20px_rgba(88,166,255,0.4)]' : 'text-gh-dim bg-gh-bar-bg border border-gh-border hover:border-gh-accent/30 hover:text-white' }}">
                <i class="fa-solid {{ $item[3] }} {{ ($item[2] ?? false) ? '' : 'opacity-40 group-hover:opacity-100 group-hover:text-gh-accent' }}"></i>
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    {{-- Performance Matrix --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-12">
        @foreach([
            ['Total Nodes', number_format($stats['total_links']), 'fa-link', 'text-gh-accent'],
            ['Active Sync', number_format($stats['active_links']), 'fa-signal', 'text-green-500'],
            ['Directory', number_format($stats['registered_links']), 'fa-id-card', 'text-blue-400'],
            ['Scraped', number_format($stats['anonymous_links']), 'fa-search', 'text-gh-dim'],
            ['Pending Ads', number_format($stats['pending_ads']), 'fa-clock', 'text-purple-500'],
            ['checks/24h', number_format($stats['recent_checks']), 'fa-history', 'text-cyan-400']
        ] as $stat)
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-lg hover:border-gh-accent/20 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[9px] font-black text-gh-dim uppercase tracking-widest">{{ $stat[0] }}</span>
                    <i class="fa-solid {{ $stat[2] }} {{ $stat[3] }} opacity-30 text-xs"></i>
                </div>
                <div class="text-3xl font-black text-white leading-none">{{ $stat[1] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-12">
        {{-- Activity Stream --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-3xl overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-gh-border bg-gh-bg/30 flex items-center justify-between">
                <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-bolt-lightning text-gh-accent"></i> Real-time Stream
                </h3>
                <span class="text-[9px] font-bold text-gh-dim bg-gh-bg border border-gh-border px-2 py-0.5 rounded italic">Protocol 04.9.2</span>
            </div>
            <div class="divide-y divide-gh-border/30 max-h-[600px] overflow-y-auto no-scrollbar">
                @forelse($recentLinks as $link)
                    <div class="px-8 py-6 hover:bg-gh-bg/40 transition-colors group">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <span class="font-bold text-white group-hover:text-gh-accent transition-colors truncate">{{ $link->title }}</span>
                            <span class="shrink-0 text-[8px] font-black px-2 py-0.5 rounded border {{ $link->user_id ? 'border-green-500/30 text-green-500 bg-green-500/5' : 'border-blue-500/30 text-blue-400 bg-blue-500/5' }} uppercase tracking-widest">
                                {{ $link->user_id ? 'Authenticated' : 'Crawler' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-[10px] text-gh-dim font-mono truncate max-w-[80%] opacity-50">
                                {{ $link->url }}
                            </div>
                            <span class="text-[10px] font-black text-gh-dim/60 uppercase tracking-tighter">{{ $link->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center opacity-20">
                        <i class="fa-solid fa-inbox text-5xl mb-4"></i>
                        <p class="text-white font-black uppercase tracking-widest text-xs">Stream Static</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar Operations --}}
        <div class="space-y-8">
            <div class="bg-gh-bar-bg border border-gh-border rounded-3xl p-8 shadow-xl relative overflow-hidden">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-8 flex items-center gap-3">
                    <i class="fa-solid fa-microchip text-gh-accent"></i> Core Ops
                </h3>
                <div class="space-y-6">
                    @foreach([
                        ['Crawler Cluster', 'Nominal', 'bg-green-500'],
                        ['Tor Relay Node', 'Active', 'bg-green-500'],
                        ['MariaDB Mainframe', 'Optimal', 'bg-blue-400'],
                        ['Cache Layer', 'Synced', 'bg-purple-500']
                    ] as $svc)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full {{ $svc[2] }} shadow-[0_0_10px_rgba(255,255,255,0.2)]"></div>
                                <span class="text-sm font-bold text-gh-dim">{{ $svc[0] }}</span>
                            </div>
                            <span class="text-[9px] font-black text-white uppercase px-2 py-0.5 rounded bg-gh-bg border border-gh-border tracking-tighter">{{ $svc[1] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gradient-to-br from-gh-accent/10 to-transparent border border-gh-accent/20 rounded-3xl p-8">
                <h4 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-gauge-high italic text-gh-accent"></i> Quick Deployment
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.ads.create') }}" class="flex flex-col items-center gap-3 bg-gh-bg border border-gh-border p-5 rounded-2xl hover:border-gh-accent/50 transition-all no-underline group">
                        <i class="fa-solid fa-plus-square text-lg text-gh-dim group-hover:text-gh-accent transition-colors"></i>
                        <span class="text-[9px] font-black text-gh-dim uppercase tracking-widest group-hover:text-white">New Ad</span>
                    </a>
                    <a href="{{ route('admin.blacklist') }}" class="flex flex-col items-center gap-3 bg-gh-bg border border-gh-border p-5 rounded-2xl hover:border-red-500/50 transition-all no-underline group">
                        <i class="fa-solid fa-shield-slash text-lg text-gh-dim group-hover:text-red-500 transition-colors"></i>
                        <span class="text-[9px] font-black text-gh-dim uppercase tracking-widest group-hover:text-white">Ban Node</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app.layouts>