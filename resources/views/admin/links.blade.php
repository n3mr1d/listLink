<x-app.layouts title="Admin - Link Inventory">

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Link Inventory</h1>
            <p class="text-gh-dim text-sm">Review, filter, and moderate all submitted .onion services.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('submit.create') }}" class="bg-gh-btn-bg text-white border border-gh-border px-4 py-2 rounded-lg font-bold text-sm tracking-tight hover:bg-gh-btn-hover no-underline transition-all flex items-center gap-2">
                <i class="fas fa-plus text-xs"></i> New Link
            </a>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), false],
            ['Directory Inventory', route('admin.links'), true],
            ['Ad Queue', route('admin.ads'), false],
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
        @foreach([
            ['all', 'Global Stream'],
            ['registered', 'Verified Contributors'],
            ['anonymous', 'Anonymous Data']
        ] as $tab)
            <a href="{{ route('admin.links', ['filter' => $tab[0]]) }}"
                class="px-5 py-2 rounded-xl text-[0.65rem] font-black uppercase tracking-widest transition-all border {{ $filter === $tab[0] ? 'bg-white text-gh-bg border-white' : 'bg-gh-bg text-gh-dim border-gh-border hover:text-white hover:border-gh-dim' }}">
                {{ $tab[1] }}
            </a>
        @endforeach
    </div>

    @if ($links->count() > 0)
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-[0.65rem] font-black text-gh-dim uppercase tracking-widest border-b border-gh-border">
                            <th class="px-6 py-4">Node Profile</th>
                            <th class="px-6 py-4 hidden md:table-cell">Onion Address</th>
                            <th class="px-6 py-4 hidden md:table-cell">Category</th>
                            <th class="px-6 py-4">Routing</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Timestamp</th>
                            <th class="px-6 py-4 text-right">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($links as $link)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('link.show', $link->slug) }}" class="font-bold text-white group-hover:text-gh-accent transition-colors no-underline">{{ $link->title }}</a>
                                        <div class="flex items-center gap-2">
                                            @if ($link->user)
                                                <span class="text-[0.6rem] text-gh-dim">by <span class="text-gh-text">{{ $link->user->username }}</span></span>
                                            @elseif ($link->user_id)
                                                <span class="text-[0.6rem] text-gh-dim">by <span class="text-gh-text">user #{{ $link->user_id }}</span></span>
                                            @else
                                                <span class="text-[0.6rem] text-gh-dim italic opacity-50">anonymous source</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="font-mono text-[0.7rem] text-gh-dim truncate max-w-[250px]" title="{{ $link->url }}">
                                        {{ $link->url }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span class="text-[0.65rem] font-bold text-gh-dim border border-gh-border px-2 py-0.5 rounded-lg bg-gh-bg">{{ $link->category->label() }}</span>
                                </td>
                                <td class="px-6 py-4 text-[0.7rem]">
                                    @if ($link->user_id)
                                        <div class="flex items-center gap-1.5 text-green-500 font-bold uppercase tracking-tighter">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></div>
                                            Directory
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1.5 text-blue-500 font-bold uppercase tracking-tighter">
                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_5px_rgba(59,130,246,0.5)]"></div>
                                            Search
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell text-[0.7rem] text-gh-dim font-medium">
                                    {{ $link->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.links.delete', $link->id) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Silahkan konfirmasi untuk hapus permanen!')">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500/10 text-red-500 border border-red-500/10 hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Purge Record">
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

            @if($links->hasPages())
                <div class="px-6 py-4 border-t border-white/5 bg-white/[0.01]">
                    {{ $links->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="py-24 text-center bg-gh-bar-bg border border-gh-border border-dashed rounded-2xl flex flex-col items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gh-bg border border-gh-border flex items-center justify-center text-gh-dim/20 mb-2">
                <i class="fa-solid fa-link-slash text-2xl"></i>
            </div>
            <p class="text-gh-dim text-sm italic">
                Strategic inventory empty for filter: <span class="text-white font-bold">{{ ucfirst($filter) }}</span>
            </p>
            <a href="{{ route('admin.links') }}" class="text-gh-accent text-xs font-bold hover:underline">Reset Filters &rarr;</a>
        </div>
    @endif

</x-app.layouts>