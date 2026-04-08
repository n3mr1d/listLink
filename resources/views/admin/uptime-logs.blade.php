<x-app.layouts title="Admin - Uptime Surveillance">

    <div class="mb-8">
        <h1 class="text-3xl font-black text-white tracking-tight mb-2">Network Surveillance</h1>
        <p class="text-gh-dim text-sm">Historical record of uptime verification requests across the Tor network.</p>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), false],
            ['Directory Inventory', route('admin.links'), false],
            ['Ad Queue', route('admin.ads'), false],
            ['Uptime History', route('admin.uptime-logs'), true],
            ['Access Control', route('admin.blacklist'), false],
            ['Crawler Engine', route('admin.crawler.index'), false],
            ['Email Harvesting', route('admin.email-crawler.index'), false]
        ] as $item)
            <a href="{{ $item[1] }}" class="px-4 py-2.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(88,166,255,0.3)]' : 'text-gh-dim bg-white/5 border border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    @if ($logs->count() > 0)
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-[0.65rem] font-black text-gh-dim uppercase tracking-widest border-b border-gh-border">
                            <th class="px-6 py-4">Target Node</th>
                            <th class="px-6 py-4">Verification State</th>
                            <th class="px-6 py-4 text-center">Latency</th>
                            <th class="px-6 py-4 hidden md:table-cell text-center">Source Identity</th>
                            <th class="px-6 py-4 text-right">Verification Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    @if ($log->link)
                                        <div class="flex flex-col gap-1">
                                            <a href="{{ route('link.show', $log->link->slug) }}" class="font-bold text-white hover:text-gh-accent transition-colors no-underline">{{ $log->link->title }}</a>
                                            <span class="text-[0.65rem] text-gh-dim font-mono truncate max-w-[200px]">{{ $log->link->url }}</span>
                                        </div>
                                    @else
                                        <span class="text-gh-dim italic opacity-30 uppercase text-[0.6rem] font-black tracking-widest">Record Extinguished</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = match($log->status) {
                                            'online' => 'bg-green-500/10 text-green-500 border-green-500/20 shadow-[0_0_8px_rgba(34,197,94,0.15)]',
                                            'offline' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                            'timeout' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                            default => 'bg-gh-bg text-gh-dim border-gh-border',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[0.6rem] font-black uppercase tracking-widest border {{ $statusClass }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($log->response_time_ms)
                                        <span class="text-[0.7rem] font-mono text-gh-accent font-bold">{{ $log->response_time_ms }}<span class="text-[0.6rem] ml-0.5 opacity-60">ms</span></span>
                                    @else
                                        <span class="text-gh-dim opacity-20">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-center">
                                    <span class="bg-black/20 px-2 py-1 rounded text-[0.6rem] font-mono text-gh-dim/60" title="{{ $log->checked_by_ip_hash }}">
                                        {{ substr($log->checked_by_ip_hash, 0, 8) }}...
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-[0.7rem] text-gh-dim font-medium uppercase tracking-tighter">
                                    {{ $log->checked_at ? $log->checked_at->diffForHumans() : 'Unknown Epoch' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-white/5 bg-white/[0.01]">
                    {{ $logs->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="py-24 text-center bg-gh-bar-bg border border-gh-border border-dashed rounded-2xl flex flex-col items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gh-bg border border-gh-border flex items-center justify-center text-gh-dim/20">
                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
            </div>
            <p class="text-gh-dim text-sm italic">
                Logs empty. All silence on the Tor front.
            </p>
        </div>
    @endif

</x-app.layouts>
