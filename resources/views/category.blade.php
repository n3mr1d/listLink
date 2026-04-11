<x-app.layouts title="{{ $category->label() }} .Onion Links"
    description="Browse all verified Tor hidden services in the {{ $category->label() }} category. Updated daily with uptime status.">

    <div class="max-w-[1200px] mx-auto px-4 py-12">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-3 mb-10 text-[10px] font-black uppercase tracking-[0.2em] text-gh-dim">
            <a href="{{ route('home') }}" class="hover:text-gh-accent no-underline transition-colors">Core</a>
            <svg class="w-2.5 h-2.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round"/></svg>
            <span class="text-white">Segment Report: {{ $category->label() }}</span>
        </nav>

        {{-- Top Banner Ad --}}
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div class="relative w-full h-[90px] mb-12 rounded-2xl overflow-hidden border border-gh-border bg-gh-bar-bg group shadow-2xl">
                <span class="absolute top-2 right-2 bg-black/70 text-gh-sponsored px-2 py-0.5 rounded text-[10px] font-black uppercase z-10 border border-gh-sponsored/30">Sponsored</span>
                @php $topAd = $headerAds->first(); @endphp
                @if ($topAd->banner_path)
                    <a href="{{ $topAd->url }}" class="block w-full h-full">
                        <img src="{{ asset('storage/' . $topAd->banner_path) }}" alt="{{ $topAd->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </a>
                @else
                    <a href="{{ $topAd->url }}" class="flex w-full h-full items-center justify-center bg-gradient-to-br from-[#1a2332] to-gh-bg no-underline font-bold text-white group-hover:text-gh-accent transition-all px-10">
                        <div class="text-center font-black uppercase tracking-widest text-sm italic">{{ $topAd->title }}</div>
                    </a>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-12">
            <div>
                <div class="mb-12 pb-6 border-b border-gh-border">
                    <h1 class="text-3xl font-black text-white tracking-tighter uppercase italic">{{ $category->label() }}</h1>
                    <p class="text-gh-dim text-sm font-medium">Filtered access for the current sector.</p>
                </div>

                @if($links->count() > 0)
                    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-2xl">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gh-bg/50 border-b border-gh-border">
                                    <th class="px-6 py-4 text-[10px] font-black text-gh-dim uppercase tracking-widest leading-none">Identity / URL</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gh-dim uppercase tracking-widest leading-none hidden md:table-cell">Details</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gh-dim uppercase tracking-widest leading-none w-[120px]">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gh-border/50">
                                @foreach($links as $link)
                                    <tr class="hover:bg-gh-bg transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-1">
                                                <a href="{{ route('link.show', $link->slug) }}" class="text-sm font-bold text-gh-accent hover:text-white no-underline transition-colors leading-tight">{{ $link->title }}</a>
                                                <span class="text-[10px] font-mono text-gh-dim opacity-40 truncate max-w-[200px]">{{ $link->url }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 hidden md:table-cell">
                                            <span class="text-[10px] font-medium text-gh-dim/60 italic">{{ Str::limit($link->description, 60) }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="inline-flex items-center gap-2 px-2 py-0.5 rounded-full border {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'border-green-500/30 bg-green-500/5 text-green-500' : 'border-red-500/30 bg-red-500/5 text-red-500' }}">
                                                <div class="w-1 h-1 rounded-full {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                                                <span class="text-[9px] font-black uppercase tracking-tighter">{{ $link->uptime_status->label() }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-12 flex justify-center">
                        {{ $links->links('pagination.simple') }}
                    </div>
                @else
                    <div class="bg-gh-bar-bg/30 border border-dashed border-gh-border rounded-3xl p-20 text-center">
                        <p class="text-gh-dim text-xs font-black uppercase tracking-widest opacity-40 italic">Sector is currently deserted.</p>
                        <a href="{{ route('submit.create') }}" class="inline-block mt-6 text-gh-accent font-black text-[10px] uppercase tracking-widest border-b border-gh-accent pb-1">Submit Signal Access</a>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-12">
                <div>
                    <h3 class="text-[10px] font-black text-gh-dim uppercase tracking-[0.2em] mb-6 border-l-2 border-gh-accent pl-3">Archives</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($categories as $cat)
                            <a href="{{ route('category.show', $cat->value) }}"
                                class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $cat->value === $category->value ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(56,139,253,0.3)]' : 'bg-gh-bar-bg text-gh-dim border border-gh-border hover:border-gh-accent hover:text-white' }}">
                                {{ $cat->label() }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Sidebar Ads --}}
                @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                    <div class="space-y-6">
                        @foreach ($sidebarAds as $sideAd)
                            <div class="relative w-full h-[250px] rounded-3xl overflow-hidden border border-gh-border bg-gh-bar-bg group shadow-2xl">
                                <span class="absolute top-3 right-3 bg-black/70 text-gh-sponsored px-2 py-0.5 rounded text-[9px] font-black uppercase z-10 border border-gh-sponsored/30">Sponsored</span>
                                @if ($sideAd->banner_path)
                                    <a href="{{ $sideAd->url }}" class="block w-full h-full">
                                        <img src="{{ asset('storage/' . $sideAd->banner_path) }}" alt="{{ $sideAd->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    </a>
                                @else
                                    <a href="{{ $sideAd->url }}" class="flex flex-col w-full h-full items-center justify-center bg-gradient-to-b from-gh-bar-bg to-gh-bg no-underline p-8 text-center group-hover:bg-gh-bg transition-all">
                                        <div class="text-xs font-black text-white uppercase tracking-[0.2em] leading-relaxed italic">{{ $sideAd->title }}</div>
                                        <div class="text-[9px] text-gh-dim mt-4 font-mono opacity-40 truncate w-full">{{ $sideAd->url }}</div>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-app.layouts>