<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    <div class="flex flex-col min-h-[calc(100vh-140px)] items-center relative box-border">
        <!-- Main Search Area -->
        <main class="flex flex-col items-center w-full max-w-[800px] flex-grow px-5 py-10 box-border">
      

          {{-- ═══ Search Hero (Only shown when no query) ═══ --}}
    <div class="py-16 flex flex-col items-center">
        <div class="w-full max-w-[600px] flex flex-col items-center text-center">
            <x-app.logo class="w-40 h-40 " />
            <h1 class="text-3xl font-extrabold text-white mb-2">Hidden Line</h1>
            <p class="text-gh-dim mb-10">We don't have any rules, search anything here with {{ number_format($stats['indexed_count']) }} indexed pages</p>

            <form action="{{ route('search.index') }}" method="GET" class="w-full">
                <div class="relative flex items-center bg-gh-bar-bg border border-gh-border rounded-full px-6 py-4 shadow-xl focus-within:ring-2 focus-within:ring-gh-accent focus-within:bg-gh-bg transition-all">
                    <i class="fas fa-search text-gh-dim mr-4 text-xl"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search the onion network..." class="flex-grow bg-transparent border-none text-white text-lg outline-none">
                    <button type="submit" class="bg-gh-accent text-gh-bg px-6 py-2 rounded-full font-bold ml-4 hover:bg-blue-400 transition-colors">Search</button>
                </div>
            </form>

            <div class="mt-6 flex gap-4">
                <a href="{{ route('directory') }}" class="bg-gh-bar-bg border border-gh-border text-gh-text px-8 py-2.5 rounded-full font-semibold hover:bg-gh-border transition-colors text-sm">
                    <i class="fas fa-list-ul mr-2"></i> List Link
                </a>
            </div>
        </div>
    </div>
    
   <!-- Ads Area (Below Search/Stats) -->
            <div class="w-full mt-5">
                @if (isset($headerAds) && $headerAds->count() > 0)
                    <div class="flex flex-col gap-4 items-center">
                        <span class="text-[0.6rem] uppercase text-gh-dim tracking-widest font-bold">Sponsored Content</span>
                        @foreach ($headerAds as $headerAd)
                            <a href="{{ $headerAd->url }}" class="w-full max-w-[728px] h-[90px] bg-gh-bar-bg border border-gh-border rounded-lg overflow-hidden block transition-colors hover:border-gh-dim" target="_blank">
                                @if ($headerAd->banner_path)
                                    <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}" class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gh-dim text-sm font-semibold uppercase tracking-tighter">{{ $headerAd->title }}</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sponsored / Featured Links (Minimal Text Row) --}}
            @if (isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                <div class="w-full flex flex-wrap gap-4 justify-center mt-12 pt-8 border-t border-gh-border">
                    <span class="text-gh-dim text-xs uppercase tracking-tighter">Featured:</span>
                    @foreach ($sponsoredLinks->take(6) as $sponsoredLink)
                        <a href="{{ $sponsoredLink->url }}" target="_blank"
                            class="text-gh-sponsored no-underline text-xs font-semibold hover:underline">{{ $sponsoredLink->title }}</a>
                    @endforeach
                </div>
            @endif
            <!-- Stats (Detailed) -->
            <div class="w-full mt-10 flex flex-col gap-8">
                <div class="flex justify-center gap-10 border-b border-gh-border pb-5">
                    <div class="flex flex-col items-center">
                        <span class="text-2xl font-bold text-white">{{ number_format($stats['total_links']) }}</span>
                        <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider mt-1">Verified Links</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl font-bold text-green-400">{{ number_format($stats['online_links']) }}</span>
                        <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider mt-1">Online Now</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl font-bold text-blue-400">{{ number_format($stats['indexed_count']) }}</span>
                        <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider mt-1">Indexed</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-5 shadow-sm">
                        <h3 class="text-sm font-semibold m-0 mb-4 text-gh-accent flex items-center gap-2">
                            <i class="fas fa-clock"></i> Recently Added
                        </h3>
                        <ul class="list-none p-0 m-0">
                            @foreach ($recentlyAddedLinks as $link)
                                <li class="flex justify-between py-2 border-b border-white/5 last:border-0 text-sm">
                                    <a href="{{ route('link.show', $link->slug) }}" class="text-gh-text no-underline truncate max-w-[150px] hover:text-gh-accent hover:underline">{{ $link->title }}</a>
                                    <span class="text-gh-dim text-xs">{{ $link->created_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-5 shadow-sm">
                        <h3 class="text-sm font-semibold m-0 mb-4 text-gh-accent flex items-center gap-2">
                            <i class="fas fa-users"></i> Recent Users
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($recentlyRegisteredUsers as $user)
                                <span class="bg-gh-accent/10 text-gh-accent px-2.5 py-1 rounded-full text-xs border border-gh-accent/20">{{ $user->username }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

         
        </main>
    </div>

    <style>
        @keyframes gh-fade {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-app.layouts>