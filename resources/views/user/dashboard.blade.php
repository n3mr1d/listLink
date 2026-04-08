<x-app.layouts title="User Dashboard">

    <div class="mb-10 pb-4 border-b border-gh-border flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Welcome, {{ auth()->user()->username }}</h1>
            <p class="text-gh-dim">Manage your submitted links and track your advertisement performance.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('submit.create') }}" class="bg-gh-accent text-gh-bg px-4 py-2 rounded-lg font-bold text-sm tracking-tight hover:bg-blue-400 no-underline transition-all">Submit Link</a>
            <a href="{{ route('advertise.create') }}" class="bg-gh-btn-bg text-white border border-gh-border px-4 py-2 rounded-lg font-bold text-sm tracking-tight hover:bg-gh-btn-hover no-underline transition-all">New Ad</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-10">
        {{-- Links Section --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
            <div class="bg-white/5 border-b border-gh-border px-6 py-4 flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-link text-gh-dim"></i> My Submissions
                </h2>
                <span class="bg-gh-bg border border-gh-border px-2 py-0.5 rounded text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">{{ $links->total() }} Total</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-white/5 border-b border-gh-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Title & Identity</th>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Connectivity</th>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Last Sync</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($links as $link)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ route('link.show', $link->slug) }}" class="text-gh-accent font-bold hover:underline no-underline">
                                            {{ $link->title }}
                                        </a>
                                        <span class="text-[0.7rem] text-gh-dim font-mono truncate max-w-[300px]">{{ $link->url }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-white font-medium bg-white/5 border border-white/10 px-2 py-1 rounded">{{ $link->category->label() }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500/10 text-green-500 border border-green-500/20' : ($link->uptime_status === \App\Enum\UptimeStatus::OFFLINE ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20') }}">
                                        {{ $link->uptime_status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gh-dim">
                                    {{ $link->last_check ? $link->last_check->diffForHumans() : 'Never' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-folder-open text-3xl text-white/10"></i>
                                        <p class="text-gh-dim text-sm italic">You haven't submitted any links yet.</p>
                                        <a href="{{ route('submit.create') }}" class="text-gh-accent text-xs font-bold hover:underline">Submit your first link &rarr;</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($links->hasPages())
                <div class="px-6 py-4 border-t border-gh-border bg-white/[0.01]">
                    {{ $links->links('pagination.simple') }}
                </div>
            @endif
        </div>

        {{-- Ads Section --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-xl overflow-hidden shadow-sm">
            <div class="bg-white/5 border-b border-gh-border px-6 py-4 flex items-center justify-between">
                <h2 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-ad text-gh-dim"></i> Advertising Slots
                </h2>
                <span class="bg-gh-bg border border-gh-border px-2 py-0.5 rounded text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">{{ $ads->total() }} Active</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-white/5 border-b border-gh-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Promotion Focus</th>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Placement</th>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">Current State</th>
                            <th class="px-6 py-3 text-left text-[0.65rem] font-bold text-gh-dim uppercase tracking-wider">End of Campaign</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($ads as $ad)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-white font-bold">{{ $ad->title }}</span>
                                        <span class="text-[0.7rem] text-gh-dim font-mono truncate max-w-[300px]">{{ $ad->url }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[0.65rem] font-bold text-white bg-white/5 px-2 py-1 rounded border border-white/10 uppercase tracking-widest">{{ $ad->ad_type->label() }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = [
                                            'active' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                            'pending' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
                                            'rejected' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                            'expired' => 'bg-gh-dim/10 text-gh-dim border-gh-border'
                                        ][$ad->status] ?? 'bg-white/5 text-white border-white/10';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase border {{ $statusClass }}">
                                        {{ ucfirst($ad->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gh-dim">
                                    {{ $ad->expires_at ? $ad->expires_at->format('M d, Y') : 'Life-time' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-bullhorn text-3xl text-white/10"></i>
                                        <p class="text-gh-dim text-sm italic">You don't have any active ad campaigns.</p>
                                        <a href="{{ route('advertise.create') }}" class="text-gh-accent text-xs font-bold hover:underline">Grow your traffic &rarr;</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($ads->hasPages())
                <div class="px-6 py-4 border-t border-gh-border bg-white/[0.01]">
                    {{ $ads->links('pagination.simple') }}
                </div>
            @endif
        </div>
    </div>
</x-app.layouts>