<x-app.layouts title="Admin - Ad Management">

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Advertisement Control</h1>
            <p class="text-gh-dim text-sm">Moderate campaign requests and manage active sponsored slots.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ads.create') }}" class="bg-gh-accent text-gh-bg px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-400 no-underline transition-all shadow-lg shadow-blue-500/10">
                <i class="fas fa-plus mr-2"></i> Launch New Ad
            </a>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), false],
            ['Directory Inventory', route('admin.links'), false],
            ['Ad Queue', route('admin.ads'), true],
            ['Uptime History', route('admin.uptime-logs'), false],
            ['Access Control', route('admin.blacklist'), false],
            ['Crawler Engine', route('admin.crawler.index'), false],
            ['Email Harvesting', route('admin.email-crawler.index'), false]
        ] as $item)
            <a href="{{ $item[1] }}" class="px-4 py-2.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(88,166,255,0.3)]' : 'text-gh-dim bg-white/5 border border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    {{-- Filter Bars --}}
    <div class="flex items-center gap-3 mb-8 overflow-x-auto no-scrollbar">
        @foreach(['pending', 'active', 'expired', 'rejected', 'all'] as $f)
            <a href="{{ route('admin.ads', ['filter' => $f]) }}"
                class="px-5 py-2 rounded-xl text-[0.65rem] font-black uppercase tracking-widest transition-all border {{ $filter === $f ? 'bg-white text-gh-bg border-white shadow-lg' : 'bg-gh-bg text-gh-dim border-gh-border hover:text-white hover:border-gh-dim' }}">
                {{ $f }}
            </a>
        @endforeach
    </div>

    @if ($ads->count() > 0)
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-[0.65rem] font-black text-gh-dim uppercase tracking-widest border-b border-gh-border">
                            <th class="px-6 py-4">Campaign Focus</th>
                            <th class="px-6 py-4 hidden md:table-cell">Tier & Placement</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 hidden md:table-cell text-center">Contact Identity</th>
                            <th class="px-6 py-4 text-right">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($ads as $ad)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="font-bold text-white group-hover:text-gh-accent transition-colors">{{ $ad->title }}</div>
                                        <div class="text-[0.7rem] text-gh-dim font-mono truncate max-w-[250px]" title="{{ $ad->url }}">{{ $ad->url }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[0.65rem] font-black text-white uppercase tracking-tighter">{{ $ad->ad_type->label() }}</span>
                                        <span class="text-[0.6rem] text-gh-dim uppercase tracking-widest opacity-60">{{ $ad->placement->label() }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = match($ad->status) {
                                            'active' => 'bg-green-500/10 text-green-500 border-green-500/20 shadow-[0_0_10px_rgba(34,197,94,0.1)]',
                                            'pending' => 'bg-orange-500/10 text-orange-500 border-orange-500/20 shadow-[0_0_10px_rgba(249,115,22,0.1)]',
                                            'rejected' => 'bg-red-500/10 text-red-500 border-red-500/20 shadow-[0_0_10px_rgba(239,68,68,0.1)]',
                                            'expired' => 'bg-white/5 text-gh-dim border-white/5',
                                            default => 'bg-gh-bg text-white border-gh-border',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[0.65rem] font-black uppercase tracking-wider border {{ $statusClass }}">
                                        {{ $ad->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell text-center">
                                    <span class="bg-black/20 px-2 py-1 rounded text-[0.7rem] font-mono text-gh-accent border border-white/5">{{ $ad->contact_info }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.ads.edit', $ad->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gh-dim hover:text-white transition-all border border-white/5 shadow-sm" title="Modify">
                                            <i class="fa-solid fa-pen-to-square text-[0.7rem]"></i>
                                        </a>
                                        
                                        @if ($ad->status === 'pending')
                                            <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-500/10 text-green-500 hover:bg-green-500 hover:text-white transition-all border border-green-500/10 shadow-sm" title="Authorize">
                                                    <i class="fa-solid fa-check text-[0.7rem]"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all border border-red-500/10 shadow-sm" title="Decline">
                                                    <i class="fa-solid fa-xmark text-[0.7rem]"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.ads.delete', $ad->id) }}" method="POST" onsubmit="return confirm('Silahkan konfirmasi untuk hapus iklan!')">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all border border-red-500/10 shadow-sm" title="Purge">
                                                <i class="fa-solid fa-trash text-[0.7rem]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($ads->hasPages())
                <div class="px-6 py-4 border-t border-white/5 bg-white/[0.01]">
                    {{ $ads->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="py-24 text-center bg-gh-bar-bg border border-gh-border border-dashed rounded-2xl flex flex-col items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gh-bg border border-gh-border flex items-center justify-center text-gh-dim/20">
                <i class="fa-solid fa-rectangle-ad text-2xl"></i>
            </div>
            <p class="text-gh-dim text-sm italic">
                No campaign data found for filter: <span class="text-white font-bold">{{ ucfirst($filter) }}</span>
            </p>
            <a href="{{ route('admin.ads') }}" class="text-gh-accent text-xs font-bold hover:underline">Reset Filters &rarr;</a>
        </div>
    @endif

</x-app.layouts>