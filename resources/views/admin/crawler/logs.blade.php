<x-app.layouts title="Admin - Crawl Intelligence Analysis">

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Node Intelligence Analysis</h1>
            <p class="font-mono text-[0.8rem] text-gh-dim truncate opacity-60" title="{{ $link->url }}">{{ $link->url }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.crawler.index') }}" class="text-gh-dim text-[0.65rem] font-black uppercase tracking-widest hover:text-white flex items-center gap-2 no-underline">
                <i class="fas fa-arrow-left text-[0.6rem]"></i> Return to Inventory
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

    {{-- Summary Data --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center">
            <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-2">Total Iterations</div>
            <div class="text-3xl font-black text-white">{{ $link->crawl_count }}</div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center">
            <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-2">Current State</div>
            @php
                $statusColor = match($link->crawl_status) {
                    'success' => 'text-green-500',
                    'failed'  => 'text-red-500',
                    default   => 'text-gh-dim',
                };
            @endphp
            <div class="text-3xl font-black {{ $statusColor }} uppercase">{{ $link->crawl_status }}</div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center">
            <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-2">Temporal Epoch</div>
            <div class="text-sm font-black text-white mt-2">
                {{ $link->last_crawled_at ? $link->last_crawled_at->diffForHumans() : 'Untracked' }}
            </div>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 text-center">
            <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-2">Recursive Discovery</div>
            <div class="text-3xl font-black text-cyan-400">{{ $link->discoveredLinks()->count() }}</div>
        </div>
    </div>

    {{-- Intelligence Preview --}}
    @if($content)
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm mb-12">
            <div class="px-8 py-5 border-b border-gh-border bg-white/5 flex items-center justify-between">
                <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-microchip text-gh-accent"></i> Indexed Tactical Content
                </h3>
                @if($content->language)
                    <span class="text-[0.6rem] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg border border-gh-accent/20 bg-gh-accent/5 text-gh-accent">
                        {{ $content->language }}
                    </span>
                @endif
            </div>
            <div class="p-8 lg:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-10">
                    <div class="space-y-6">
                        <div class="flex flex-col gap-2">
                            <span class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widests tracking-widest ml-1">Node Domain</span>
                            <span class="font-mono text-xs text-white bg-gh-bg border border-gh-border px-4 py-2.5 rounded-xl">{{ $content->domain ?: '—' }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest ml-1">Primary Header (H1)</span>
                            <span class="text-sm font-bold text-white tracking-tight leading-snug">{{ $content->h1 ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex flex-col gap-2">
                            <span class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest ml-1">Envelope Analysis</span>
                            <div class="flex items-center gap-2">
                                <span class="bg-gh-accent/5 border border-gh-accent/20 px-2 py-1 rounded-lg font-mono text-[0.65rem] text-gh-accent uppercase font-black tracking-widest">{{ $content->content_type ?: 'null-type' }}</span>
                                <span class="text-[0.6rem] font-bold text-gh-dim uppercase tracking-widest opacity-60">{{ number_format($content->content_length) }} octets</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest ml-1">Semantic Metadata</span>
                            <span class="text-xs text-gh-dim italic leading-relaxed">{{ $content->meta_description ?: 'No semantic description found in source.' }}</span>
                        </div>
                    </div>
                </div>

                @if($content->body_text)
                    <div class="pt-10 border-t border-white/5">
                        <details class="group">
                            <summary class="cursor-pointer text-[0.65rem] font-black text-gh-accent uppercase tracking-widest hover:text-white flex items-center gap-2 list-none outline-none transition-colors">
                                <i class="fas fa-chevron-right text-[0.5rem] transition-transform group-open:rotate-90"></i>
                                Deep-Inspection: Raw Semantic Stream ({{ number_format(strlen($content->body_text)) }} chars)
                            </summary>
                            <div class="mt-8 bg-black/40 border border-gh-border rounded-2xl p-8 max-h-[400px] overflow-y-auto no-scrollbar font-mono text-[0.7rem] text-gh-dim/80 leading-loose whitespace-pre-wrap select-all">
                                {{ Str::limit($content->body_text, 8000) }}
                            </div>
                        </details>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Intelligence History --}}
    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm mb-24">
        <div class="px-8 py-5 border-b border-gh-border bg-white/5 flex items-center justify-between">
            <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-history text-gh-accent"></i> Temporal Scan History
            </h3>
            <span class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest opacity-60">{{ $logs->total() }} Sequential Records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/5">
                    <tr class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest">
                        <th class="px-8 py-4">Execution Epoch</th>
                        <th class="px-8 py-4 text-center">Synthesis Outcome</th>
                        <th class="px-8 py-4 text-center">HTTP Response</th>
                        <th class="px-8 py-4 text-center">Latency</th>
                        <th class="px-8 py-4 text-center">Discoveries</th>
                        <th class="px-8 py-4">Anomaly Log</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                <div class="text-[0.75rem] font-black text-white mb-1 group-hover:text-gh-accent transition-colors">{{ $log->created_at->diffForHumans() }}</div>
                                <div class="text-[0.6rem] font-mono text-gh-dim opacity-50">{{ $log->created_at->format('M d, Y @ H:i:s') }}</div>
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
                            <td class="px-8 py-5 text-center font-mono text-[0.7rem] text-gh-dim">
                                <span class="{{ $log->http_status == 200 ? 'text-green-500 font-bold' : 'text-orange-400' }}">{{ $log->http_status ?: 'VOID' }}</span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-[0.7rem] font-mono text-gh-accent font-bold">{{ $log->response_time_ms ? $log->response_time_ms . 'ms' : '—' }}</span>
                            </td>
                            <td class="px-8 py-5 text-center font-black text-cyan-400 text-sm">
                                {{ $log->discovered_count ?: '0' }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-[0.65rem] text-red-400 font-medium truncate max-w-[200px]" title="{{ $log->error_message }}">
                                    {{ $log->error_message ?: '—' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <p class="text-gh-dim text-sm italic">Historical archive is currently empty for this node.</p>
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
