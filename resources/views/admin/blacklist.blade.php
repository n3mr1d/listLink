<x-app.layouts title="Admin - Blacklist Control">

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Access Blacklist</h1>
            <p class="text-gh-dim text-sm">Define pattern-based restrictions to neutralize malicious submissions.</p>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), false],
            ['Directory Inventory', route('admin.links'), false],
            ['Ad Queue', route('admin.ads'), false],
            ['Uptime History', route('admin.uptime-logs'), false],
            ['Access Control', route('admin.blacklist'), true],
            ['Crawler Engine', route('admin.crawler.index'), false],
            ['Email Harvesting', route('admin.email-crawler.index'), false]
        ] as $item)
            <a href="{{ $item[1] }}" class="px-4 py-2.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(88,166,255,0.3)]' : 'text-gh-dim bg-white/5 border border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    {{-- Add to Blacklist --}}
    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm mb-12">
        <div class="px-6 py-4 border-b border-gh-border bg-white/5">
            <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-plus-circle text-red-500"></i> Register New Ban Pattern
            </h3>
        </div>
        <div class="p-8">
            <form action="{{ route('admin.blacklist.add') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                @csrf
                <div class="md:col-span-5 flex flex-col gap-2">
                    <label class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest ml-1">Pattern (e.g., domain.onion)</label>
                    <input type="text" name="url_pattern" placeholder="Enter target string..." required
                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-sm text-white focus:ring-1 focus:ring-red-500 outline-none transition-all placeholder:font-mono placeholder:text-gh-dim/30 font-mono">
                </div>
                <div class="md:col-span-5 flex flex-col gap-2">
                    <label class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest ml-1">Classification / Reason</label>
                    <input type="text" name="reason" placeholder="Brief rationale..."
                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-sm text-white focus:ring-1 focus:ring-red-500 outline-none transition-all placeholder:text-gh-dim/30">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-red-500 text-white font-black text-xs uppercase tracking-widest py-3.5 rounded-xl hover:bg-red-600 transition-all shadow-lg shadow-red-500/10">
                        Sanction
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Current Blacklist --}}
    @if ($entries->count() > 0)
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-[0.65rem] font-black text-gh-dim uppercase tracking-widest border-b border-gh-border">
                            <th class="px-6 py-4">Prohibited Node Pattern</th>
                            <th class="px-6 py-4">Rationale</th>
                            <th class="px-6 py-4">Inception Date</th>
                            <th class="px-6 py-4 text-right">Intervention</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($entries as $entry)
                            <tr class="hover:bg-red-500/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm text-red-500 bg-red-500/5 px-2 py-0.5 rounded border border-red-500/10">{{ $entry->url_pattern }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-gh-text leading-relaxed">{{ $entry->reason ?: 'Manifest policy violation' }}</span>
                                </td>
                                <td class="px-6 py-4 text-[0.7rem] text-gh-dim font-medium">
                                    {{ $entry->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end">
                                        <form action="{{ route('admin.blacklist.remove', $entry->id) }}" method="POST" onsubmit="return confirm('Hapus pattern dari blacklist?')">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gh-dim hover:text-white border border-white/5 transition-all shadow-sm" title="Rehabilitate">
                                                <i class="fa-solid fa-undo text-[0.7rem]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($entries->hasPages())
                <div class="px-6 py-4 border-t border-white/5 bg-white/[0.01]">
                    {{ $entries->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="py-24 text-center bg-gh-bar-bg border border-gh-border border-dashed rounded-2xl flex flex-col items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gh-bg border border-gh-border flex items-center justify-center text-gh-dim/20">
                <i class="fa-solid fa-shield-halved text-2xl"></i>
            </div>
            <p class="text-gh-dim text-sm italic">
                Strategic integrity high. No blacklisted patterns detected.
            </p>
        </div>
    @endif

</x-app.layouts>
