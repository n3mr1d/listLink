<x-app.layouts title="Admin - Crawler Engine">

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Crawler Engine</h1>
            <p class="text-gh-dim text-sm italic">Automated reconnaissance and indexing powered by Ahmia-inspired recursive discovery.</p>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.crawler.dispatch') }}">
                @csrf
                <button type="submit" class="bg-gh-accent text-gh-bg px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-400 no-underline transition-all shadow-lg shadow-blue-500/10" onclick="return confirm('Initiate smart discovery?')">
                    <i class="fas fa-satellite mr-2"></i> Smart Dispatch
                </button>
            </form>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), false],
            ['Directory Inventory', route('admin.links'), false],
            ['Ad Queue', route('admin.ads'), false],
            ['Uptime History', route('admin.uptime-logs'), false],
            ['Access Control', route('admin.blacklist'), false],
            ['Crawler Engine', route('admin.crawler.index'), true],
            ['Email Harvesting', route('admin.email-crawler.index'), false]
        ] as $item)
            <a href="{{ $item[1] }}" class="px-4 py-2.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(88,166,255,0.3)]' : 'text-gh-dim bg-white/5 border border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-4 mb-10">
        @foreach([
            [$stats['total'], 'Total Links', 'gh-accent'],
            [$stats['never_crawled'], 'Pending Sync', 'blue-500'],
            [$stats['success'], 'Sync Success', 'green-400'],
            [$stats['failed'], 'Sync Failed', 'red-500'],
            [$stats['pending'], 'Discovery', 'orange-400'],
            [$stats['force_queued'], 'Force Flag', 'purple-400'],
            [number_format($stats['discovered']), 'Discovered', 'cyan-400'],
            [number_format($stats['indexed']), 'Indexed', 'green-500'],
        ] as $s)
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-4 text-center group cursor-default">
                <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-2 group-hover:text-{{ $s[2] }}">{{ $s[1] }}</div>
                <div class="text-2xl font-black text-white">{{ $s[0] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-10">
            {{-- Performance Dashboard --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-cyan-500/10 to-transparent border border-cyan-500/20 rounded-2xl p-6">
                    <div class="text-3xl font-black text-white mb-1">{{ $stats['crawls_24h'] }}</div>
                    <div class="text-[0.65rem] font-black text-cyan-400 uppercase tracking-widest">Active Crawls (24h)</div>
                </div>
                <div class="bg-gradient-to-br from-green-500/10 to-transparent border border-green-500/20 rounded-2xl p-6">
                    <div class="text-3xl font-black text-white mb-1">{{ $stats['success_24h'] }}</div>
                    <div class="text-[0.65rem] font-black text-green-400 uppercase tracking-widest">Success Ratio (24h)</div>
                </div>
                <div class="bg-gradient-to-br from-purple-500/10 to-transparent border border-purple-500/20 rounded-2xl p-6">
                    <div class="text-3xl font-black text-white mb-1">{{ $stats['avg_response_ms'] ? $stats['avg_response_ms'] . 'ms' : '—' }}</div>
                    <div class="text-[0.65rem] font-black text-purple-400 uppercase tracking-widest">Avg Latency (24h)</div>
                </div>
            </div>

            {{-- Link Inventory --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gh-border bg-white/5 flex items-center justify-between">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-list-ul text-gh-accent"></i> Real-time Queue Status
                    </h3>
                    <a href="{{ route('admin.crawler.logs') }}" class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest hover:text-white no-underline transition-colors">Complete Logs &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white/5 border-b border-white/5">
                            <tr class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest">
                                <th class="px-6 py-4">Node Identity</th>
                                <th class="px-6 py-4 text-center">Sync State</th>
                                <th class="px-6 py-4 text-center">Last Epoch</th>
                                <th class="px-6 py-4 text-right">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($links as $link)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="font-mono text-xs text-white truncate max-w-[280px]" title="{{ $link->url }}">{{ Str::limit($link->url, 50) }}</div>
                                            <div class="text-[0.65rem] text-gh-dim truncate max-w-[240px] italic">{{ $link->title }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $st = match($link->crawl_status) {
                                                'success' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                                'failed' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                                default => 'bg-white/5 text-gh-dim border-white/10',
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[0.6rem] font-black uppercase border {{ $st }}">
                                            @if($link->force_recrawl)<i class="fas fa-bolt text-[0.5rem] animate-pulse mr-1"></i>@endif
                                            {{ $link->crawl_status }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[0.7rem] text-gh-dim font-medium">{{ $link->last_crawled_at ? $link->last_crawled_at->diffForHumans() : 'Untracked' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.crawler.crawl-single', $link->id) }}">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gh-dim hover:text-white border border-white/5 transition-all shadow-sm" title="Re-Sync Node">
                                                    <i class="fa-solid fa-sync text-[0.7rem]"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.crawler.link-logs', $link->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gh-dim hover:text-white border border-white/5 transition-all shadow-sm" title="Observatory">
                                                <i class="fa-solid fa-scroll text-[0.7rem]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <p class="text-gh-dim text-sm italic">Crawl inventory is currently vacant.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($links->hasPages())
                    <div class="px-6 py-4 border-t border-white/5 bg-white/[0.01]">
                        {{ $links->links('pagination.simple') }}
                    </div>
                @endif
            </div>
        </div>

        <aside class="space-y-8">
            {{-- Global Actions --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-sm">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6">Strategic Overrides</h3>
                <div class="flex flex-col gap-4">
                    <form method="POST" action="{{ route('admin.crawler.crawl-all') }}">
                        @csrf
                        <button type="submit" class="w-full bg-red-500/5 border border-red-500/20 text-red-500 py-3 rounded-xl font-black text-[0.65rem] uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all shadow-sm" onclick="return confirm('⚠️ Execute mandatory total crawl?')">
                            <i class="fas fa-biohazard mr-2"></i> Mandatory Total Sync
                        </button>
                    </form>
                    <p class="text-[0.65rem] text-gh-dim italic leading-relaxed px-1">Triggering this will bypass interval checks and queue all known records for exhaustive verification.</p>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gh-border bg-white/5 flex items-center justify-between">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">Audit Trail</h3>
                    <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)] animate-pulse"></div>
                </div>
                <div class="divide-y divide-white/5 max-h-[450px] overflow-y-auto no-scrollbar">
                    @foreach($recentLogs as $log)
                        <div class="px-6 py-4 hover:bg-white/[0.02] transition-colors flex items-start gap-3">
                            <div class="mt-0.5 scale-75">
                                {{ match($log->status) { 'success' => '✅', 'failed' => '❌', 'skipped' => '⏭', 'timeout' => '⏰', default => '⚪' } }}
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="text-[0.7rem] text-white font-mono truncate mb-1">{{ $log->link ? Str::limit($log->link->url, 40) : 'Extinguished ID' }}</div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[0.6rem] text-gh-dim uppercase font-bold">{{ $log->created_at->diffForHumans() }}</span>
                                    @if($log->response_time_ms)<span class="text-[0.6rem] text-gh-accent font-mono">{{ $log->response_time_ms }}ms</span>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gh-accent/5 border border-gh-accent/20 rounded-2xl p-6">
                <h4 class="text-xs font-black text-gh-accent uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i class="fas fa-network-wired"></i> Architecture Notes
                </h4>
                <p class="text-[0.75rem] text-gh-dim leading-relaxed mb-0 italic">Active {{ $crawlInterval }}-day rolling cycle. Scheduler dispatches every 6h to stale nodes. Proxies routed through tactical egress nodes.</p>
            </div>
        </aside>
    </div>

</x-app.layouts>
