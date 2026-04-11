<x-app.layouts title="{{ $link->title }} - Tor .Onion Directory"
    description="Details for {{ $link->title }}: {{ Str::limit($link->description, 150) }} - Verified .onion link on Hidden Line.">

    <div class="max-w-[1200px] mx-auto px-4 py-12">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-3 mb-10 text-[10px] font-black uppercase tracking-[0.2em] text-gh-dim">
            <a href="{{ route('home') }}" class="hover:text-gh-accent no-underline transition-colors">Core</a>
            <svg class="w-2.5 h-2.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round"/></svg>
            <a href="{{ route('category.show', $link->category->value) }}" class="hover:text-gh-accent no-underline transition-colors">{{ $link->category->label() }}</a>
            <svg class="w-2.5 h-2.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round"/></svg>
            <span class="text-white">{{ $link->title }}</span>
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

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-12">
            {{-- Main Content Column --}}
            <div class="space-y-12">
                {{-- Link detail card --}}
                <div class="bg-gh-bar-bg border border-gh-border rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gh-accent/5 blur-[100px] -mr-32 -mt-32"></div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-10">
                            <div>
                                <h1 class="text-4xl font-black text-white tracking-tighter mb-3 uppercase italic">{{ $link->title }}</h1>
                                <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-gh-dim">
                                    <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-gh-accent"></div> {{ $link->category->label() }}</span>
                                    <span>•</span>
                                    <span class="text-gh-dim/60 italic">Signal Registered via {{ $link->user->username ?? 'Anonymous Proxy' }}</span>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500/10 text-green-500 border border-green-500/20 shadow-[0_0_20px_rgba(34,197,94,0.15)]' : 'bg-red-500/10 text-red-500 border border-red-500/20' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                                    {{ $link->uptime_status->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="group relative bg-gh-bg/50 border border-gh-border rounded-2xl p-1 mb-10 focus-within:border-gh-accent transition-all">
                            <div class="flex items-center gap-4 px-5 py-4">
                                <div class="flex-grow font-mono text-sm text-gh-text/90 truncate select-all">{{ $link->url }}</div>
                                <button onclick="copyToClipboard('{{ $link->url }}')" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-gh-bar-bg border border-gh-border text-gh-dim hover:text-white hover:border-gh-accent hover:bg-gh-accent/10 transition-all active:scale-95" id="copyBtn" title="Copy Signal Address">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-width="2.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                        </div>

                        @if($link->description)
                            <div class="mb-12">
                                <h3 class="text-[10px] font-black text-gh-dim uppercase tracking-[0.2em] mb-4 opacity-50">Transmission Data</h3>
                                <p class="text-[1.05rem] text-gh-text/80 leading-relaxed font-medium">{{ $link->description }}</p>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-4 pt-10 border-t border-gh-border">
                            <form action="{{ route('link.check', $link->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-gh-bar-bg text-white border border-gh-border px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gh-bg hover:border-gh-accent transition-all flex items-center gap-3">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2.5" stroke-linecap="round"/></svg>
                                    Refresh Pulse
                                </button>
                            </form>
                            <a href="{{ $link->url }}" target="_blank" rel="noreferrer noopener" class="bg-gh-accent text-gh-bg px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-300 transition-all flex items-center gap-3 no-underline shadow-[0_0_30px_rgba(56,139,253,0.2)]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2.5" stroke-linecap="round"/></svg>
                                Relay Connection
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Comments Section --}}
                <div class="space-y-8">
                    <h2 class="text-xl font-black text-white uppercase tracking-tighter flex items-center gap-3">
                        <span class="w-2 h-8 bg-gh-accent"></span> 
                        Signal Feed <span class="text-gh-dim opacity-30">[{{ $link->comments->count() }}]</span>
                    </h2>

                    <div class="space-y-6">
                        @forelse($link->comments as $comment)
                            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-8 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gh-accent/10 flex items-center justify-center text-gh-accent text-xs font-black">
                                            {{ strtoupper(substr($comment->username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-white leading-none">{{ $comment->username }}</div>
                                            <div class="text-[9px] font-black text-gh-dim uppercase tracking-tighter mt-1 opacity-40">Frequency verified</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black text-gh-dim uppercase tracking-tighter">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-gh-text/70 leading-relaxed pl-14">{{ $comment->content }}</div>
                            </div>
                        @empty
                            <div class="bg-gh-bar-bg/30 border border-dashed border-gh-border rounded-3xl p-16 text-center">
                                <p class="text-gh-dim text-xs font-black uppercase tracking-widest opacity-40 italic">No community reports logged for this node.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Form --}}
                    <div class="bg-gh-bar-bg border border-gh-border rounded-3xl overflow-hidden shadow-2xl mt-12">
                        <div class="bg-white/5 px-8 py-5 border-b border-gh-border flex items-center justify-between">
                            <h3 class="text-[10px] font-black text-white uppercase tracking-widest">Post Report</h3>
                            <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest opacity-30">Secure Transmission</span>
                        </div>
                        <div class="p-8 md:p-10">
                            <form action="{{ route('link.comment', $link->id) }}" method="POST" class="space-y-8">
                                @csrf
                                <div class="hidden"><input type="text" name="website_url_hp" tabindex="-1"></div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-3">
                                        <label for="username" class="text-[10px] font-black text-gh-dim uppercase tracking-widest ml-1">Identity</label>
                                        @auth
                                            <div class="bg-gh-bg border border-gh-border rounded-2xl px-5 py-4 text-white text-xs font-black opacity-50">{{ auth()->user()->username }}</div>
                                            <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                                        @else
                                            <input type="text" name="username" id="username" placeholder="Anonymous Node" class="w-full bg-gh-bg border border-gh-border rounded-2xl px-5 py-4 text-white text-xs font-bold outline-none focus:border-gh-accent transition-all">
                                        @endauth
                                    </div>
                                    <div class="space-y-3">
                                        <label for="challenge" class="text-[10px] font-black text-gh-dim uppercase tracking-widest ml-1">Protocol: {{ $challenge }}</label>
                                        <input type="number" name="challenge" id="challenge" required placeholder="Result" class="w-full bg-gh-bg border border-gh-border rounded-2xl px-5 py-4 text-white text-xs font-bold outline-none focus:border-gh-accent transition-all">
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label for="content" class="text-[10px] font-black text-gh-dim uppercase tracking-widest ml-1">Report Data</label>
                                    <textarea name="content" id="content" placeholder="Broadcast your verdict..." required rows="5" class="w-full bg-gh-bg border border-gh-border rounded-2xl px-5 py-4 text-white text-xs font-bold outline-none focus:border-gh-accent transition-all resize-none"></textarea>
                                </div>

                                <button type="submit" class="bg-gh-accent text-gh-bg px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-blue-300 transition-all shadow-[0_0_30px_rgba(56,139,253,0.1)]">Transmit Signal</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <aside class="space-y-12">
                {{-- Metadata --}}
                <div class="bg-gh-bar-bg border border-gh-border rounded-3xl overflow-hidden shadow-xl">
                    <div class="bg-white/5 px-6 py-4 border-b border-gh-border">
                        <h3 class="text-[10px] font-black text-white uppercase tracking-widest leading-none">Node Vitals</h3>
                    </div>
                    <div class="p-8 space-y-8">
                        @php
                            $vitals = [
                                ['label' => 'Subscribed', 'val' => $link->created_at->format('M d, Y'), 'icon' => '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round"/>'],
                                ['label' => 'Last Pulse', 'val' => $link->last_check ? $link->last_check->diffForHumans() : 'Standby', 'icon' => '<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round"/>'],
                                ['label' => 'Total Syncs', 'val' => number_format($link->check_count) . ' logs', 'icon' => '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round"/>'],
                            ];
                        @endphp

                        @foreach($vitals as $vital)
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-gh-bg border border-gh-border flex items-center justify-center text-gh-accent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $vital['icon'] !!}</svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-gh-dim uppercase tracking-widest leading-none mb-1 opacity-50">{{ $vital['label'] }}</span>
                                    <span class="text-[11px] font-bold text-white">{{ $vital['val'] }}</span>
                                </div>
                            </div>
                        @endforeach

                        <div class="pt-4 space-y-3">
                            <span class="text-[9px] font-black text-gh-dim uppercase tracking-widest leading-none opacity-50">Signal Integrity</span>
                            @php $score = min(100, max(10, ($link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 60 : 20) + min(40, $link->check_count * 2))); @endphp
                            <div class="h-2 bg-gh-bg rounded-full overflow-hidden border border-gh-border shadow-inner">
                                <div class="h-full {{ $score > 70 ? 'bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.5)]' : ($score > 40 ? 'bg-yellow-500' : 'bg-red-500') }} transition-all duration-1000" style="width: {{ $score }}%"></div>
                            </div>
                            <div class="text-right text-[10px] font-black text-white italic">{{ $score }}% Strength</div>
                        </div>
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

                {{-- Warning --}}
                <div class="p-8 bg-red-900/10 border border-red-500/20 rounded-3xl">
                    <h4 class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="3" stroke-linecap="round"/></svg>
                        Security Protocol
                    </h4>
                    <p class="text-[11px] text-gh-dim/80 leading-relaxed font-medium">Never broadcast PII identities. Ensure signal routing via Tor Browser with restricted script execution. Avoid data persistence on remote nodes.</p>
                </div>
            </aside>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('copyBtn');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg>';
                btn.classList.add('border-green-500', 'bg-green-500/10');
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('border-green-500', 'bg-green-500/10');
                }, 2000);
            });
        }
    </script>
</x-app.layouts>