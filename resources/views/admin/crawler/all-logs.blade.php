<x-app.layouts title="Admin - Global Crawl Intel Stream">

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Global Intelligence Stream</h1>
            <p class="text-gh-dim text-sm italic">Consolidated audit trail of all reconnaissance operations across the network.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.crawler.index') }}" class="text-gh-dim text-[0.65rem] font-black uppercase tracking-widest hover:text-white flex items-center gap-2 no-underline">
                <i class="fas fa-arrow-left text-[0.6rem]"></i> Return to Controller
            </a>
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

    {{-- Performance Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        @foreach([
            [number_format($logStats['total']), 'Total Stream', 'gh-accent'],
            [number_format($logStats['success']), 'Synthesis Success', 'green-400'],
            [number_format($logStats['failed']), 'Synthesis Failure', 'red-500'],
            [number_format($logStats['skipped']), 'Operation Skip', 'orange-400'],
        ] as $s)
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center shadow-sm">
                <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-2 group-hover:text-{{ $s[2] }}">{{ $s[1] }}</div>
                <div class="text-2xl font-black text-white">{{ $s[0] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Filter Stream --}}
    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-3 mb-10 flex items-center gap-3 overflow-x-auto no-scrollbar shadow-sm">
        <span class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest px-4 border-r border-white/10">Filter Epoch</span>
        @foreach(['all', 'success', 'failed', 'skipped', 'timeout'] as $s)
            <a href="{{ route('admin.crawler.logs', ['status' => $s]) }}"
               class="px-5 py-2 rounded-xl text-[0.6rem] font-black uppercase tracking-widest border transition-all no-underline {{ $statusFilter === $s ? 'bg-white text-gh-bg border-white shadow-lg' : 'bg-white/5 text-gh-dim border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $s }}
            </a>
        @endforeach
    </div>

    {{-- Main Stream --}}
    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm mb-24">
        <div class="px-8 py-5 border-b border-gh-border bg-white/5 flex items-center justify-between">
            <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-stream text-gh-accent"></i> Operational Audit Feed
            </h3>
            <span class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest opacity-60">{{ $logs->total() }} Sequential Events</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/5">
                    <tr class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest">
                        <th class="px-8 py-4">Node Profile</th>
                        <th class="px-8 py-4 text-center">Outcome</th>
                        <th class="px-8 py-4 text-center">HTTP</th>
                        <th class="px-8 py-4 text-center">Latency</th>
                        <th class="px-8 py-4 text-center">Discov.</th>
                        <th class="px-8 py-4">Anomaly Details</th>
                        <th class="px-8 py-4 text-right">Epoch Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                @if($log->link)
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('admin.crawler.link-logs', $log->link_id) }}" class="text-[0.7rem] font-bold text-gh-accent no-underline hover:underline truncate max-w-[220px]" title="{{ $log->link->url }}">
                                            {{ $log->link->url }}
                                        </a>
                                        <span class="text-[0.6rem] text-gh-dim font-black uppercase tracking-widest opacity-40">Verified Identifier</span>
                                    </div>
                                @else
                                    <span class="text-[0.6rem] text-gh-dim font-black uppercase tracking-widest bg-red-500/5 px-2 py-1 rounded border border-red-500/10">Purged Record #{{ $log->link_id }}</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                @php
                                    $st = match($log->status) {
                                        'success' => 'bg-green-500/10 text-green-500 border-green-500/20 shadow-[0_0_8px_rgba(34,197,94,0.15)]',
                                        'failed' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                        'skipped' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
                                        'timeout' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                        default => 'bg-white/5 text-gh-dim border-white/5',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[0.6rem] font-black uppercase tracking-widest border {{ $st }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center px-8 py-5 text-center font-mono text-[0.7rem] text-gh-dim">
                                <span class="{{ $log->http_status == 200 ? 'text-green-500 font-bold' : ($log->http_status >= 400 ? 'text-red-500' : 'text-gh-dim') }}">{{ $log->http_status ?? 'VOID' }}</span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-[0.7rem] font-mono text-gh-accent font-bold">{{ $log->response_time_ms ? $log->response_time_ms . 'ms' : '—' }}</span>
                            </td>
                            <td class="px-8 py-5 text-center font-black text-cyan-400 text-[0.8rem]">
                                {{ $log->discovered_count ?: '0' }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-[0.7rem] text-red-400/80 font-medium truncate max-w-[180px]" title="{{ $log->error_message }}">
                                    {{ $log->error_message ?: '—' }}
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="text-[0.7rem] font-black text-white whitespace-nowrap tracking-tighter">{{ $log->created_at->diffForHumans() }}</div>
                                <div class="text-[0.55rem] font-mono text-gh-dim opacity-40 mt-1">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <p class="text-gh-dim text-sm italic">Intrusion log currently vacant. No events recorded in epoch.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-8 py-4 border-t border-white/5 bg-white/[0.01]">
                {{ $logs->links('pagination.simple') }}
            </div>
        @endif
    </div>

</x-app.layouts>
