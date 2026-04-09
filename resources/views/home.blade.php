<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    <div class="flex flex-col min-h-[calc(100vh-140px)] items-center relative box-border">
        <!-- Main Search Area -->
        <main class="flex flex-col items-center w-full max-w-[800px] flex-grow px-5 py-10 box-border">


            {{-- ═══ Search Hero (Only shown when no query) ═══ --}}
            <div class="py-12 flex flex-col items-center">
                <div class="w-full max-w-[600px] flex flex-col items-center text-center">
                    <x-app.logo class="h-10" />
                    <h1 class="text-2xl font-extrabold text-white mb-2">Hidden Line</h1>
                    <p class="text-gh-dim mb-10 text-sm">Indexed Onion Pages:
                        {{ number_format($stats['indexed_count']) }}
                    </p>

                    <form action="{{ route('search.index') }}" method="GET" class="w-full">
                        <div
                            class="relative flex items-center bg-gh-bar-bg border border-gh-border rounded-lg px-5 py-3 shadow-md focus-within:ring-2 focus-within:ring-gh-accent focus-within:bg-gh-bg">
                            <span class="text-gh-dim mr-3 text-lg select-none">&#128269;</span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Search the onion network..."
                                class="flex-grow bg-transparent border-none text-white text-base outline-none">
                            <button type="submit"
                                class="bg-gh-accent text-gh-bg px-5 py-2 rounded font-bold ml-2 hover:bg-blue-400">Search</button>
                        </div>
                    </form>

                    <div class="mt-6 flex gap-4">
                        <a href="{{ route('directory') }}"
                            class="bg-gh-bar-bg border border-gh-border text-gh-text px-6 py-2 rounded font-semibold hover:bg-gh-border text-xs">
                            List Link
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ads Area (Below Search/Stats) -->
            <div class="w-full mt-5">
                @if (isset($headerAds) && $headerAds->count() > 0)
                    @foreach ($headerAds as $headerAd)
                        <div
                            class="relative w-full max-w-[728px] h-[90px] mx-auto mb-6 rounded-md overflow-hidden border border-gh-border bg-gh-bg">
                            <span
                                class="absolute top-1.5 right-1.5 bg-black/70 text-gh-dim px-1.5 py-0.5 rounded text-[0.6rem] font-bold uppercase z-10">
                                Sponsored
                            </span>

                            @if ($headerAd->banner_path)
                                <a href="{{ $headerAd->url }}" class="block w-full h-full">
                                    <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}"
                                        class="w-full h-full object-cover">
                                </a>
                            @else
                                <a href="{{ $headerAd->url }}"
                                    class="flex w-full h-full items-center justify-center bg-gradient-to-br from-[#1a2332] to-gh-bg no-underline font-bold text-white">
                                    {{ $headerAd->title }}
                                </a>
                            @endif
                        </div>
                    @endforeach
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
                        <span
                            class="text-2xl font-bold text-green-400">{{ number_format($stats['online_links']) }}</span>
                        <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider mt-1">Online Now</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span
                            class="text-2xl font-bold text-blue-400">{{ number_format($stats['indexed_count']) }}</span>
                        <span class="text-[0.65rem] text-gh-dim uppercase tracking-wider mt-1">Indexed</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-5 shadow-sm">
                        <h3 class="text-xs font-bold m-0 mb-4 text-gh-accent uppercase tracking-wider">
                            Recently Added
                        </h3>
                        <ul class="list-none p-0 m-0">
                            @foreach ($recentlyAddedLinks as $link)
                                <li class="flex justify-between py-2 border-b border-white/5 last:border-0 text-sm">
                                    <a href="{{ route('link.show', $link->slug) }}"
                                        class="text-gh-text no-underline truncate max-w-[150px] hover:text-gh-accent">{{ $link->title }}</a>
                                    <span class="text-gh-dim text-[0.65rem]">{{ $link->created_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="bg-gh-bar-bg border border-gh-border rounded-xl p-5 shadow-sm">
                        <h3 class="text-xs font-bold m-0 mb-4 text-gh-accent uppercase tracking-wider">
                            Recent Users ({{ $stats['total_users'] }})
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @if($recentlyRegisteredUser)
                                <span
                                    class="bg-gh-accent/10 text-gh-accent px-2.5 py-1 rounded border border-gh-accent/20 text-[0.65rem] font-bold uppercase">
                                    {{ $recentlyRegisteredUser->username }} <span class="opacity-50 mx-1">•</span> Joined
                                    {{ $recentlyRegisteredUser->created_at->diffForHumans() }}
                                </span>
                            @else
                                <span class="text-gh-dim text-xs">No users found.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


        </main>
    </div>
</x-app.layouts>