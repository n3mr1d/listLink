<x-app.layouts title="Admin - Discovered Node Fragments">

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Discovered Node Fragments</h1>
            <p class="font-mono text-[0.8rem] text-gh-dim truncate opacity-60" title="{{ $link->url }}">Parent Probe: {{ $link->url }}</p>
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

    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm mb-24">
        <div class="px-8 py-5 border-b border-gh-border bg-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-project-diagram text-gh-accent"></i> Fragment Inventory
                <span class="text-[0.6rem] font-black text-gh-dim ml-2 opacity-50">{{ number_format($discovered->total()) }} URLs Detected</span>
            </h3>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.crawler.discovered.clear', $link->id) }}" onsubmit="return confirm('Silahkan konfirmasi untuk menghapus semua data temuan URL dari halaman ini!')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-red-500/20 bg-red-500/5 text-[0.65rem] text-red-500 font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-trash-alt text-[0.7rem]"></i> Purge Fragments
                    </button>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/5">
                    <tr class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest">
                        <th class="px-8 py-4 w-24">Serial #</th>
                        <th class="px-8 py-4">Captured Target URL</th>
                        <th class="px-8 py-4 text-right">Detection Context</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($discovered as $item)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5 font-mono text-[0.65rem] text-gh-dim select-none">
                                0x{{ dechex($item->id) }}
                            </td>
                            <td class="px-8 py-5">
                                <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="font-mono text-[0.75rem] text-cyan-400 no-underline hover:text-gh-accent break-all leading-relaxed block max-w-[900px]" title="{{ $item->url }}">
                                    {{ $item->url }}
                                </a>
                            </td>
                            <td class="px-8 py-5 text-right whitespace-nowrap">
                                <div class="text-[0.7rem] font-black text-white tracking-widest">{{ $item->created_at->diffForHumans() }}</div>
                                <div class="text-[0.55rem] font-mono text-gh-dim opacity-40 mt-1 uppercase">Captured Logic</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <i class="fas fa-ghost text-4xl text-white/5"></i>
                                    <p class="text-gh-dim text-sm italic">No fragments captured. Node remains isolated.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($discovered->hasPages())
            <div class="px-8 py-4 border-t border-white/5 bg-white/[0.01]">
                {{ $discovered->links('pagination.simple') }}
            </div>
        @endif
    </div>

</x-app.layouts>
