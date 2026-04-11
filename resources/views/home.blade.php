<x-app.layouts title="Verified Tor .Onion Directory"
    description="The most reliable Tor hidden services directory. Daily uptime monitoring, verified .onion links, and community driven indexing.">

    <div style="display:flex;flex-direction:column;min-height:65vh;align-items:center;justify-content:center;padding:1rem;">

        {{-- Hero --}}
        <div style="width:100%;max-width:600px;display:flex;flex-direction:column;align-items:center;margin-bottom:2rem;text-align:center;">
            <x-app.logo style="height:7rem;margin-bottom:.5rem;opacity:.9;" />
            <h1 style="font-size:2rem;font-weight:900;color:#fff;letter-spacing:-.02em;margin:0 0 .3rem;">Hidden Line</h1>
            <p style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.2em;color:var(--color-gh-dim);margin:0;">Decentralized Search Protocol</p>
        </div>

        {{-- Search --}}
        <form action="{{ route('search.index') }}" method="GET" style="width:100%;max-width:540px;">
            <div style="display:flex;align-items:center;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
                <span style="padding:.6rem .75rem;color:var(--color-gh-dim);display:flex;align-items:center;flex-shrink:0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="Intercepting onion signatures..."
                    style="flex:1;background:transparent;border:none;color:#fff;font-size:.95rem;padding:.6rem .25rem;outline:none;">
                <button type="submit"
                    style="background:var(--color-gh-accent);color:#0d1117;padding:.6rem 1.1rem;border:none;font-weight:800;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;cursor:pointer;white-space:nowrap;">Search</button>
            </div>

            <div style="display:flex;justify-content:center;gap:1.5rem;margin-top:1rem;">
                <a href="{{ route('directory') }}" style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.12em;text-decoration:none;">Browse Directory</a>
                <span style="color:var(--color-gh-border);">|</span>
                <a href="{{ route('advertise.create') }}" style="font-size:.65rem;font-weight:800;color:var(--color-gh-sponsored);text-transform:uppercase;letter-spacing:.12em;text-decoration:none;">Promote Node</a>
            </div>
        </form>

        {{-- Stats --}}
        <div style="margin-top:3rem;width:100%;max-width:700px;display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;border-top:1px solid rgba(48,54,61,.4);padding-top:1.5rem;text-align:center;">
            <div>
                <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['online_links']) }}</span>
                <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Online Nodes</span>
            </div>
            <div>
                <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['indexed_count']) }}</span>
                <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Pages Indexed</span>
            </div>
            <div>
                <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['total_users']) }}</span>
                <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Users</span>
            </div>
        </div>

        {{-- Header Ads --}}
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div style="margin-top:2rem;width:100%;max-width:728px;display:flex;flex-direction:column;gap:.75rem;">
                @foreach ($headerAds->take(2) as $ad)
                    <div style="position:relative;width:100%;height:80px;border-radius:.5rem;overflow:hidden;border:1px solid var(--color-gh-border);">
                        <span style="position:absolute;top:.35rem;right:.5rem;background:rgba(0,0,0,.7);color:var(--color-gh-sponsored);padding:.15rem .5rem;border-radius:.25rem;font-size:.6rem;font-weight:800;text-transform:uppercase;z-index:1;border:1px solid rgba(210,153,34,.25);">Sponsored</span>
                        @if ($ad->banner_path)
                            <a href="{{ route('ad.track', $ad->id) }}" style="display:block;width:100%;height:100%;">
                                <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @else
                            <a href="{{ route('ad.track', $ad->id) }}" style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-btn-bg);">
                                <div style="text-align:center;">
                                    <div style="font-size:.85rem;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.08em;">{{ $ad->title }}</div>
                                    <div style="font-size:.65rem;font-family:monospace;color:var(--color-gh-dim);opacity:.6;margin-top:.2rem;">{{ $ad->url }}</div>
                                </div>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Live Activity --}}
    <div style="max-width:900px;margin:2rem auto 0;padding:0 1rem 3rem;display:grid;grid-template-columns:1fr 1fr;gap:2rem;opacity:.75;">
        <div>
            <h3 style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.18em;margin:0 0 .75rem;padding-left:.5rem;border-left:2px solid var(--color-gh-accent);">Recent Discoveries</h3>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                @foreach ($recentlyAddedLinks->take(4) as $link)
                    <a href="{{ route('link.show', $link->slug) }}" style="display:flex;justify-content:space-between;align-items:center;text-decoration:none;font-size:.78rem;color:var(--color-gh-text);">
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;">{{ $link->title }}</span>
                        <span style="font-size:.65rem;color:var(--color-gh-dim);flex-shrink:0;margin-left:.5rem;">{{ $link->created_at->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div>
            <h3 style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.18em;margin:0 0 .75rem;padding-left:.5rem;border-left:2px solid #4ade80;">Network Expansion</h3>
            @if($recentlyRegisteredUser)
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:2rem;height:2rem;border-radius:.35rem;background:rgba(88,166,255,.1);display:flex;align-items:center;justify-content:center;color:var(--color-gh-accent);font-size:.65rem;font-weight:800;flex-shrink:0;">
                        {{ substr($recentlyRegisteredUser->username, 0, 1) }}
                    </div>
                    <div>
                        <span style="font-size:.78rem;font-weight:700;color:#fff;display:block;">{{ $recentlyRegisteredUser->username }}</span>
                        <span style="font-size:.65rem;color:var(--color-gh-dim);">Registered {{ $recentlyRegisteredUser->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-app.layouts>