<x-app.layouts title="Verified Tor .Onion Directory"
    description="The most reliable Tor hidden services directory. Daily uptime monitoring, verified .onion links, and community driven indexing.">

    <div class="flex flex-col min-h-[70vh] items-center justify-center px-4">
        <!-- Minimal Hero -->
        <div class="w-full max-w-[650px] flex flex-col items-center mb-12">
            <x-app.logo class="h-40 mb-2 opacity-90" />
            <h1 class="text-3xl font-black text-white tracking-widest uppercase">Hidden Line</h1>
            <p class="text-gh-dim text-xs font-bold uppercase tracking-[0.3em] mt-1">Decentralized Search Protocol</p>
        </div>

        <!-- High-Performance Search Console -->
        <form action="{{ route('search.index') }}" method="GET" class="w-full max-w-[580px]">
            <div class="relative group">
                <div class="flex items-center bg-gh-bar-bg border border-gh-border rounded-xl px-4 py-3 shadow-2xl focus-within:border-gh-accent focus-within:bg-gh-bg transition-all">
                    <span class="text-gh-dim mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Intercepting onion signatures..."
                        class="flex-grow bg-transparent border-none text-white text-base outline-none placeholder:text-gh-dim/40 font-medium">
                    <button type="submit" class="bg-gh-accent text-gh-bg px-6 py-1.5 rounded-lg font-black text-xs uppercase tracking-tighter hover:bg-blue-400 transition-colors ml-2">Search</button>
                </div>
            </div>
            
            <div class="flex justify-center gap-6 mt-6">
                <a href="{{ route('directory') }}" class="text-[0.65rem] font-black text-gh-dim hover:text-white uppercase tracking-widest border-b border-transparent hover:border-gh-accent transition-all">Browse Directory</a>
                <span class="text-gh-border mx-1">|</span>
                <a href="{{ route('advertise.create') }}" class="text-[0.65rem] font-black text-gh-sponsored hover:text-yellow-400 uppercase tracking-widest transition-all">Promote Node</a>
            </div>
        </form>

        <!-- Network Vitals -->
        <div class="mt-24 w-full max-w-[800px] grid grid-cols-3 gap-4 border-t border-gh-border/30 pt-10">
            <div class="flex flex-col items-center">
                <span class="text-xl font-black text-white">{{ number_format($stats['online_links']) }}</span>
                <span class="text-[0.55rem] text-gh-dim uppercase font-black tracking-widest mt-1">Online Nodes</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-xl font-black text-white">{{ number_format($stats['indexed_count']) }}</span>
                <span class="text-[0.55rem] text-gh-dim uppercase font-black tracking-widest mt-1">Pages Indexed</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-xl font-black text-white">{{ number_format($stats['total_users']) }}</span>
                <span class="text-[0.55rem] text-gh-dim uppercase font-black tracking-widest mt-1">Verified Agents</span>
            </div>
        </div>

        <!-- Featured/Ads (Minimal Row) -->
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div class="mt-16 w-full max-w-[700px] flex flex-col gap-2">
                @foreach ($headerAds->take(2) as $ad)
                    <a href="{{ $ad->url }}" class="flex items-center justify-between p-3 bg-gh-bar-bg border border-gh-border rounded-lg hover:border-gh-accent/50 transition-all border-l-2 border-l-gh-sponsored">
                        <div class="flex items-center gap-3">
                            <span class="text-[0.6rem] font-black text-gh-sponsored border border-gh-sponsored/30 px-1.5 py-0.5 rounded">AD</span>
                            <span class="text-sm font-bold text-gh-text group-hover:text-white">{{ $ad->title }}</span>
                        </div>
                        <span class="text-[0.6rem] text-gh-dim font-mono truncate max-w-[200px] hidden sm:block opacity-50">{{ $ad->url }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Live Activity (Subtle) -->
    <div class="max-w-[1000px] mx-auto mt-20 px-6 grid grid-cols-1 md:grid-cols-2 gap-10 opacity-70 hover:opacity-100 transition-opacity">
        <div>
            <h3 class="text-[0.65rem] font-black text-gh-dim uppercase tracking-[0.2em] mb-4 border-l-2 border-gh-accent pl-2">Recent Discoveries</h3>
            <div class="space-y-2">
                @foreach ($recentlyAddedLinks->take(4) as $link)
                    <a href="{{ route('link.show', $link->slug) }}" class="flex justify-between items-center text-xs group">
                        <span class="text-gh-text group-hover:text-gh-accent truncate max-w-[200px]">{{ $link->title }}</span>
                        <span class="text-gh-dim text-[10px]">{{ $link->created_at->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div>
            <h3 class="text-[0.65rem] font-black text-gh-dim uppercase tracking-[0.2em] mb-4 border-l-2 border-green-500 pl-2">Network Expansion</h3>
            @if($recentlyRegisteredUser)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-gh-accent/10 flex items-center justify-center text-gh-accent font-black text-[10px]">{{ substr($recentlyRegisteredUser->username, 0, 1) }}</div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-white">{{ $recentlyRegisteredUser->username }}</span>
                        <span class="text-[10px] text-gh-dim">New node registered {{ $recentlyRegisteredUser->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app.layouts>