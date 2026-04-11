<x-app.layouts title="User Dashboard">

    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-gh-accent/10 flex items-center justify-center text-gh-accent">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <span class="text-[10px] font-black text-gh-accent uppercase tracking-[0.2em]">Verified Agent Dashboard</span>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tighter italic uppercase leading-none">Command Center</h1>
            <p class="text-gh-dim mt-3 text-sm font-medium">Monitoring and managing your broadcasted nodes on the network.</p>
        </div>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('dashboard.ads') }}" class="group relative flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-gh-bar-bg to-gh-bg border border-gh-border rounded-2xl hover:border-gh-accent/50 transition-all no-underline shadow-xl">
                <div class="text-gh-accent group-hover:scale-110 transition-transform"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-white uppercase tracking-widest">Advertiser Hub</span>
                    <span class="text-[9px] font-bold text-gh-dim uppercase opacity-60">View Analytics</span>
                </div>
            </a>
            <a href="{{ route('submit.create') }}" class="flex items-center gap-2 px-8 py-3 bg-gh-accent text-gh-bg rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-300 hover:-translate-y-0.5 transition-all shadow-2xl">
                <i class="fa-solid fa-plus-circle"></i> Deploy Node
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-12">
        {{-- High Performance Stats Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 flex items-center gap-5 shadow-lg">
                <div class="w-12 h-12 rounded-xl bg-gh-bg flex items-center justify-center text-gh-accent text-xl border border-gh-border">
                    <i class="fa-solid fa-link"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest block mb-1">Total Submissions</span>
                    <span class="text-2xl font-black text-white">{{ $links->total() }}</span>
                </div>
            </div>
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 flex items-center gap-5 shadow-lg">
                <div class="w-12 h-12 rounded-xl bg-gh-bg flex items-center justify-center text-green-500 text-xl border border-gh-border">
                    <i class="fa-solid fa-signal"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest block mb-1">Online Sync</span>
                    <span class="text-2xl font-black text-white">{{ $links->where('uptime_status', \App\Enum\UptimeStatus::ONLINE)->count() }}</span>
                </div>
            </div>
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 flex items-center gap-5 shadow-lg">
                <div class="w-12 h-12 rounded-xl bg-gh-bg flex items-center justify-center text-gh-sponsored text-xl border border-gh-border">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest block mb-1">Active Ad Units</span>
                    <span class="text-2xl font-black text-white">{{ $ads->where('status', 'active')->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Main Node Management --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-3xl overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-gh-border flex items-center justify-between bg-gh-bg/30">
                <h2 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-server text-gh-accent"></i> Node Registry
                </h2>
                <div class="flex items-center gap-2 px-3 py-1 rounded bg-gh-bg border border-gh-border">
                    <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                    <span class="text-[9px] font-black text-gh-dim uppercase tracking-tighter">Real-time Feed</span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gh-bg/50 border-b border-gh-border">
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gh-dim uppercase tracking-widest">Protocol Identity</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gh-dim uppercase tracking-widest">Network Segment</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gh-dim uppercase tracking-widest">Broadcast State</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gh-dim uppercase tracking-widest">Last Sync</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gh-border/30">
                        @forelse($links as $link)
                            <tr class="group hover:bg-gh-bg/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('link.show', $link->slug) }}" class="text-sm font-bold text-white hover:text-gh-accent no-underline transition-colors flex items-center gap-2">
                                            {{ $link->title }}
                                            <i class="fa-solid fa-external-link text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                        </a>
                                        <span class="text-[10px] text-gh-dim font-mono tracking-tighter opacity-50">{{ Str::limit($link->url, 50) }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[10px] text-gh-accent font-black bg-gh-accent/5 px-2.5 py-1 rounded-lg border border-gh-accent/10 uppercase tracking-tighter">{{ $link->category->label() }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter border {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'border-green-500/30 bg-green-500/5 text-green-500' : 'border-red-500/30 bg-red-500/5 text-red-500' }}">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                                        {{ $link->uptime_status->label() }}
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest">{{ $link->last_check ? $link->last_check->diffForHumans() : 'Standby' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center gap-6 opacity-30">
                                        <i class="fa-solid fa-satellite-dish text-6xl"></i>
                                        <div class="flex flex-col gap-1">
                                            <p class="text-white font-black uppercase tracking-widest m-0">No active signals detected</p>
                                            <p class="text-gh-dim text-[10px] font-medium uppercase tracking-[0.2em]">Deploy your first protocol to begin monitoring</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($links->hasPages())
                <div class="px-8 py-6 border-t border-gh-border bg-gh-bg/20">
                    {{ $links->links('pagination.simple') }}
                </div>
            @endif
        </div>
    </div>
</x-app.layouts>