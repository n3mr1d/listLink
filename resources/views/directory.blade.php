<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    {{-- Header Banner Ads --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div class="relative w-full max-w-[728px] h-[90px] mx-auto mb-6 rounded-md overflow-hidden border border-gh-border bg-gh-bg">
                {{-- Sponsored Label --}}
                <span class="absolute top-1.5 right-1.5 bg-black/70 text-gh-dim px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase z-10">
                    Sponsored
                </span>

                @if ($headerAd->banner_path)
                    <a href="{{ $headerAd->url }}" class="block w-full h-full">
                        <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}"
                            class="w-full h-full object-cover">
                    </a>
                @else
                    <a href="{{ $headerAd->url }}"
                        class="flex w-full h-full items-center justify-center bg-gradient-to-br from-[#1a2332] to-gh-bg no-underline">
                        <span class="text-xl font-bold text-white">{{ $headerAd->title }}</span>
                    </a>
                @endif

                {{-- Title/Premium Overlay --}}
                <div class="absolute bottom-0 left-0 w-full p-2 bg-gradient-to-t from-black/90 to-transparent flex justify-between items-end">
                    <div class="flex flex-col">
                        <a href="{{ $headerAd->url }}"
                            class="text-base font-bold text-white drop-shadow-md no-underline">{{ $headerAd->title }}</a>
                    </div>
                    <span class="bg-yellow-500/15 text-yellow-500 border border-yellow-500/30 px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase backdrop-blur-sm">
                        Premium
                    </span>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Contact Announcement --}}
    <div class="bg-blue-500/10 border border-blue-500/30 text-gh-text p-4 rounded-md mb-8 border-l-[4px] border-l-gh-accent">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="text-2xl"><i class="fa fa-envelope text-gh-accent"></i></span>
                <span class="text-sm">
                    Want to advertise or have suggestions? Contact:
                    <a href="mailto:treixnox@protonmail.com"
                        class="text-gh-accent font-bold underline">treixnox@protonmail.com</a>
                </span>
            </div>
            <a href="{{ route('advertise.create') }}" class="bg-gh-btn-bg text-white border border-gh-border px-3 py-1.5 rounded-md text-xs font-bold hover:bg-gh-btn-hover no-underline">
                Ad Pricing & Details
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-10">
        <div>
            <div class="mb-10 pb-4 border-b border-gh-border">
                <h1 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Tor Directory</h1>
                <p class="text-gh-dim">Privacy-focused directory of verified .onion websites. We don't have rules here.</p>
            </div>

            {{-- Links grouped by category --}}
            @php
                $grouped = $links->groupBy(fn($link) => $link->category->value);
            @endphp

            @foreach ($categories as $category)
                @if (isset($grouped[$category->value]) && $grouped[$category->value]->count() > 0)
                    <div class="mb-12">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h2 class="text-xl font-bold text-white tracking-tight">{{ $category->label() }}</h2>
                            <a href="{{ route('category.show', $category->value) }}" class="text-xs text-gh-accent hover:underline no-underline">View All &rarr;</a>
                        </div>

                        <div class="overflow-x-auto bg-gh-bar-bg border border-gh-border rounded-xl">
                            <table class="w-full border-collapse">
                                <thead class="bg-white/5 border-b border-gh-border">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Name</th>
                                        <th class="px-5 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider hidden md:table-cell">Last Check</th>
                                        <th class="px-5 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider w-[120px]">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    {{-- Sponsored Links at the top of first category --}}
                                    @if ($loop->first && isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                                        @foreach ($sponsoredLinks as $sponsoredLink)
                                            <tr class="bg-yellow-500/5">
                                                <td class="px-5 py-4">
                                                    <div class="flex flex-col gap-1">
                                                        <div class="flex items-center gap-2">
                                                            <span class="bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase">Ad</span>
                                                            <a href="{{ $sponsoredLink->url }}" class="text-gh-text font-bold hover:text-white no-underline">{{ $sponsoredLink->title }}</a>
                                                        </div>
                                                        <span class="text-[0.7rem] text-gh-dim font-mono truncate max-w-[250px]">{{ $sponsoredLink->url }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 hidden md:table-cell">
                                                    <span class="flex items-center gap-1.5 text-xs text-yellow-500/80 font-bold">
                                                        <i class="fa fa-crown"></i> Premium
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4">
                                                    <span class="bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 px-2 py-0.5 rounded-full text-[0.65rem] font-bold">Sponsored</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @foreach ($grouped[$category->value]->take(10) as $link)
                                        <tr class="hover:bg-white/[0.02] transition-colors">
                                            <td class="px-5 py-4 text-sm">
                                                <div class="flex flex-col gap-1">
                                                    <a href="{{ route('link.show', $link->slug) }}" class="text-gh-accent font-bold hover:underline no-underline">
                                                        {{ $link->title }}
                                                    </a>
                                                    <div class="flex items-center gap-3 text-[0.7rem]">
                                                        <span class="text-gh-dim font-mono truncate max-w-[200px]">{{ $link->url }}</span>
                                                        <span class="text-gh-dim/60 flex items-center gap-1"><i class="fas fa-globe"></i> Global</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 hidden md:table-cell">
                                                <span class="text-gh-dim text-xs flex items-center gap-1.5">
                                                    <i class="far fa-clock"></i> {{ $link->last_check ? $link->last_check->diffForHumans() : 'Never' }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500/10 text-green-500 border border-green-500/20' : ($link->uptime_status === \App\Enum\UptimeStatus::OFFLINE ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20') }}">
                                                    {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Recently Added Links --}}
            @if (isset($recentlyAddedLinks) && $recentlyAddedLinks->count() > 0)
                <div class="mb-12">
                    <div class="flex items-center gap-2 mb-4 px-1">
                        <i class="fa fa-clock text-gh-accent"></i>
                        <h2 class="text-xl font-bold text-white tracking-tight">Recently Added</h2>
                    </div>
                    <div class="overflow-x-auto bg-gh-bar-bg border border-gh-border rounded-xl">
                        <table class="w-full border-collapse">
                            <thead class="bg-white/5 border-b border-gh-border">
                                <tr>
                                    <th class="px-5 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Name</th>
                                    <th class="px-5 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider hidden md:table-cell">Added By</th>
                                    <th class="px-5 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider hidden md:table-cell">Added</th>
                                    <th class="px-5 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider w-[120px]">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach ($recentlyAddedLinks as $link)
                                    <tr class="hover:bg-white/[0.02] transition-colors">
                                        <td class="px-5 py-4 text-sm">
                                            <div class="flex flex-col gap-1">
                                                <a href="{{ route('link.show', $link->slug) }}" class="text-gh-accent font-bold hover:underline no-underline">
                                                    {{ $link->title }}
                                                </a>
                                                <div class="flex items-center gap-3 text-[0.7rem]">
                                                    <span class="text-gh-dim font-mono truncate max-w-[200px]">{{ $link->url }}</span>
                                                    <span class="text-gh-dim/60">{{ $link->category->label() }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell">
                                            <span class="text-gh-dim text-xs flex items-center gap-1.5"><i class="fa fa-user"></i> {{ $link->user->username ?? 'Anonymous' }}</span>
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell">
                                            <span class="text-gh-dim text-xs flex items-center gap-1.5"><i class="far fa-calendar"></i> {{ $link->created_at->diffForHumans() }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500/10 text-green-500 border border-green-500/20' : ($link->uptime_status === \App\Enum\UptimeStatus::OFFLINE ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20') }}">
                                                {!! $link->uptime_status->icon() !!} {{ $link->uptime_status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Recently Registered User --}}
            @if (isset($recentlyRegisteredUser))
                <div class="mb-12 bg-gh-bar-bg border border-gh-border rounded-xl p-6 shadow-sm">
                    <div class="text-white font-bold mb-4 flex items-center gap-2">🛒 New User</div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gh-bg border border-gh-border text-xs text-gh-text px-2.5 py-1 rounded-md flex items-center gap-2">
                            {{ $recentlyRegisteredUser->username }} <span class="text-[0.65rem] text-gh-dim">• Joined {{ $recentlyRegisteredUser->created_at->diffForHumans() }}</span>
                        </span>
                    </div>
                </div>
            @endif

            {{-- Pagination --}}
            <div class="mt-8 flex justify-center">
                {{ $links->links('pagination.simple') }}
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            {{-- Stats Card --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-xl mb-8 shadow-sm overflow-hidden">
                <div class="bg-white/5 px-5 py-3 border-b border-gh-border text-xs font-bold text-gh-dim uppercase tracking-wider">Directory Stats</div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gh-dim uppercase font-bold">Total Links</span>
                            <span class="text-xl font-bold text-white leading-none">{{ number_format($stats['total_links']) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gh-dim uppercase font-bold">Online Now</span>
                            <span class="text-xl font-bold text-green-400 leading-none">{{ number_format($stats['online_links']) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gh-dim uppercase font-bold">Indexed Pages</span>
                            <span class="text-xl font-bold text-blue-400 leading-none">{{ number_format($stats['indexed_count']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Categories Card --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-xl mb-8 shadow-sm overflow-hidden">
                <div class="bg-white/5 px-5 py-3 border-b border-gh-border text-xs font-bold text-gh-dim uppercase tracking-wider">Categories</div>
                <div class="p-0">
                    <ul class="list-none m-0 p-0 divide-y divide-white/5">
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('category.show', $category->value) }}" class="flex justify-between items-center px-5 py-3 text-sm text-gh-dim hover:bg-white/5 hover:text-gh-accent no-underline transition-colors">
                                    {{ $category->label() }}
                                    <i class="fas fa-chevron-right text-[0.6rem] opacity-30 group-hover:opacity-100 transition-opacity"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Sidebar Ads --}}
            @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                @foreach ($sidebarAds as $sideAd)
                    <div class="relative w-[300px] h-[250px] mx-auto mb-8 rounded-xl overflow-hidden border border-gh-border bg-gh-bg">
                        {{-- Sponsored Label --}}
                        <span class="absolute top-2 right-2 bg-black/70 text-gh-dim px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase z-10">
                            Sponsored
                        </span>

                        @if ($sideAd->banner_path)
                            <a href="{{ $sideAd->url }}" class="block w-full h-full">
                                <img src="{{ asset('storage/' . $sideAd->banner_path) }}" alt="{{ $sideAd->title }}"
                                    class="w-full h-full object-cover">
                            </a>
                        @else
                            <a href="{{ $sideAd->url }}"
                                class="flex flex-col w-full h-full items-center justify-center bg-gradient-to-b from-gh-bar-bg to-gh-bg no-underline p-6 text-center">
                                <div class="w-12 h-12 bg-white/5 rounded-lg flex items-center justify-center text-xl font-black text-white mb-4">HL</div>
                                <div class="text-sm font-bold text-white">{{ $sideAd->title }}</div>
                            </a>
                        @endif

                        {{-- Title/Premium Overlay --}}
                        <div class="absolute bottom-0 left-0 w-full p-3 bg-gradient-to-t from-black/95 to-transparent flex justify-between items-end">
                            <div class="flex flex-col max-w-[70%]">
                                <a href="{{ $sideAd->url }}"
                                    class="text-sm font-bold text-white truncate no-underline">{{ $sideAd->title }}</a>
                            </div>
                            <span class="bg-yellow-500/15 text-yellow-500 border border-yellow-500/30 px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase backdrop-blur-sm">
                                Premium
                            </span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</x-app.layouts>
