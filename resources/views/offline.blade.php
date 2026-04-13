<x-app.layouts title="Offline .Onion Services" description="Browse all currently unreachable .onion hidden services on the Tor network. Check back later as services may come back online.">

    <div style="max-width:900px;margin:0 auto;padding:0 1rem 3rem;">

        {{-- Header --}}
        <div style="text-align:center;padding:2rem 0 1.5rem;border-bottom:1px solid var(--color-gh-border);margin-bottom:2rem;">
            <div style="width:3rem;height:3rem;border:1px solid rgba(248,113,113,.3);border-radius:.8rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:#f87171;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
            </div>
            <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0 0 .4rem;">Offline Services</h1>
            <p style="color:var(--color-gh-dim);font-size:.82rem;max-width:500px;margin:0 auto;">These .onion services are currently unreachable. They may return online at any time — Tor services can be intermittent by nature.</p>
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:.75rem;margin-bottom:2rem;">
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.75rem;text-align:center;">
                <span style="font-size:1.3rem;font-weight:900;color:#f87171;display:block;">{{ number_format($totalOffline) }}</span>
                <span style="font-size:.55rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;">Offline</span>
            </div>
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.75rem;text-align:center;">
                <span style="font-size:1.3rem;font-weight:900;color:#4ade80;display:block;">{{ number_format($totalOnline) }}</span>
                <span style="font-size:.55rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;">Online</span>
            </div>
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.75rem;text-align:center;">
                <span style="font-size:1.3rem;font-weight:900;color:var(--color-gh-accent);display:block;">{{ $offlinePercent }}%</span>
                <span style="font-size:.55rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;">Downtime Rate</span>
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('offline') }}" style="margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="2.5" style="margin-left:.75rem;flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search offline services..."
                    style="flex:1;background:transparent;border:none;color:#fff;padding:.7rem;font-size:.85rem;outline:none;">
                @if($search)
                    <a href="{{ route('offline') }}" style="color:var(--color-gh-dim);padding:.7rem;font-size:.72rem;text-decoration:none;">Clear</a>
                @endif
            </div>
        </form>

        {{-- Category Filter --}}
        <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:1.5rem;overflow-x:auto;">
            <a href="{{ route('offline') }}" 
                style="padding:.35rem .65rem;border-radius:.35rem;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;text-decoration:none;border:1px solid {{ !$categoryFilter ? '#fff' : 'var(--color-gh-border)' }};color:{{ !$categoryFilter ? '#0d1117' : 'var(--color-gh-dim)' }};background:{{ !$categoryFilter ? '#fff' : 'transparent' }};white-space:nowrap;">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('offline', ['cat' => $cat->value]) }}" 
                    style="padding:.35rem .65rem;border-radius:.35rem;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;text-decoration:none;border:1px solid {{ $categoryFilter === $cat->value ? '#fff' : 'var(--color-gh-border)' }};color:{{ $categoryFilter === $cat->value ? '#0d1117' : 'var(--color-gh-dim)' }};background:{{ $categoryFilter === $cat->value ? '#fff' : 'transparent' }};white-space:nowrap;">{{ $cat->label() }}</a>
            @endforeach
        </div>

        {{-- Links List --}}
        @if($links->count() > 0)
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                @foreach($links as $link)
                    <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:1rem;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;">
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;">
                                    <div style="width:6px;height:6px;border-radius:50%;background:#f87171;flex-shrink:0;"></div>
                                    <a href="{{ route('link.show', $link->slug) }}" style="font-size:.85rem;font-weight:700;color:#fff;text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;">{{ $link->title }}</a>
                                </div>
                                <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0 0 .4rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ Str::limit($link->description, 120) }}</p>
                                <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
                                    <span style="font-family:monospace;font-size:.58rem;color:var(--color-gh-dim);opacity:.6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;">{{ $link->url }}</span>
                                    <span style="font-size:.55rem;font-weight:700;color:var(--color-gh-dim);border:1px solid var(--color-gh-border);padding:.1rem .35rem;border-radius:.25rem;">{{ $link->category->label() }}</span>
                                    @if($link->last_crawled_at)
                                        <span style="font-size:.55rem;color:var(--color-gh-dim);">Last seen {{ $link->last_crawled_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="status-badge sb-offline" style="flex-shrink:0;">Offline</span>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($links->hasPages())
                <div style="margin-top:1.5rem;">
                    {{ $links->links('pagination.simple') }}
                </div>
            @endif
        @else
            <div style="text-align:center;padding:3rem 1rem;color:var(--color-gh-dim);opacity:.5;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:block;margin:0 auto .75rem;"><circle cx="12" cy="12" r="10"/><path d="M8 15h8M9 9h.01M15 9h.01"/></svg>
                <p style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;margin:0;">{{ $search ? 'No results found' : 'No offline services in this category' }}</p>
            </div>
        @endif

    </div>

</x-app.layouts>
