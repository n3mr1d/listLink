<x-app.layouts title="{{ $query ? 'Results for ' . $query : 'Search .Onion Engine' }} - Hidden Line"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    <div style="max-width:1100px;margin:0 auto;padding:0 0 3rem;">

        {{-- Header Ads --}}
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div style="display:flex;flex-direction:column;gap:.5rem;margin-bottom:1.5rem;">
                @foreach ($headerAds as $ad)
                    <div style="position:relative;width:100%;max-width:970px;height:80px;border-radius:.5rem;overflow:hidden;border:1px solid var(--color-gh-border);">
                        <span style="position:absolute;top:.3rem;right:.5rem;background:rgba(0,0,0,.7);color:var(--color-gh-sponsored);padding:.15rem .4rem;border-radius:.2rem;font-size:.6rem;font-weight:800;text-transform:uppercase;z-index:1;">Sponsored</span>
                        @if ($ad->banner_path)
                            <a href="{{ route('ad.track', $ad->id) }}" style="display:block;width:100%;height:100%;">
                                <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @else
                            <a href="{{ route('ad.track', $ad->id) }}" style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-btn-bg);">
                                <span style="font-size:.85rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.08em;">{{ $ad->title }}</span>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Search bar --}}
        <div style="margin-bottom:1.5rem;padding-top:1rem;border-top:1px solid var(--color-gh-border);">
            <form action="{{ route('search.index') }}" method="GET" style="max-width:600px;">
                <div style="display:flex;align-items:center;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:2rem;overflow:hidden;padding:0 .5rem 0 1rem;">
                    <span style="color:var(--color-gh-dim);display:flex;align-items:center;flex-shrink:0;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $query }}" placeholder="Adjusting frequency..."
                        style="flex:1;background:transparent;border:none;color:#fff;font-size:.85rem;padding:.5rem .75rem;outline:none;">
                    @if($query)
                        <button type="submit" style="background:none;border:none;color:var(--color-gh-accent);cursor:pointer;padding:.35rem;display:flex;align-items:center;gap:0.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                          <span style="font-size:.85rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.08em;">{{ number_format($links->total()) }}</span> 
                        </button>
                    @endif
                </div>
            </form>
        </div>

        @if($query)
            {{-- Two-column layout: results + sidebar --}}
            <div style="display:grid;grid-template-columns:1fr;gap:2rem;">
                {{-- Responsive: sidebar collapses below on mobile --}}
                <style>
                    @media (min-width:768px) {
                        .search-layout { grid-template-columns: 1fr 260px !important; }
                    }
                </style>
                <div class="search-layout" style="display:grid;grid-template-columns:1fr;gap:2rem;">

                    {{-- Results --}}
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:.65rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;border-bottom:1px solid var(--color-gh-border);padding-bottom:.6rem;margin-bottom:1.5rem;">
                            <span>Revealing <span style="color:#fff;">{{ number_format($links->total()) }} signatures</span></span>
                            <span style="font-style:italic;">{{ $searchTime ?? '?' }}ms</span>
                        </div>

                        @if ($links && $links->total() > 0)
                            <div style="display:flex;flex-direction:column;gap:1.75rem;">
                                @foreach ($links as $link)
                                    <article>
                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                                            <div style="min-width:0;">
                                                <h3 style="margin:0 0 .25rem;font-size:1.05rem;font-weight:700;line-height:1.3;">
                                                    <a href="{{ route('link.show', $link->slug) }}" style="color:var(--color-gh-accent);text-decoration:none;">{{ $link->title }}</a>
                                                </h3>
                                                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.15rem;">
                                                    <span style="font-size:.62rem;font-family:monospace;color:var(--color-gh-dim);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px;opacity:.6;">{{ $link->url }}</span>
                                                    <span style="width:6px;height:6px;border-radius:50%;flex-shrink:0;background:{{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? '#4ade80' : '#f87171' }};"></span>
                                                </div>
                                            </div>
                                            <span style="flex-shrink:0;padding:.2rem .5rem;border-radius:.3rem;font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;border:1px solid {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'rgba(74,222,128,.3)' : 'rgba(248,113,113,.3)' }};color:{{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? '#4ade80' : '#f87171' }};">
                                                {{ $link->uptime_status->label() }}
                                            </span>
                                        </div>

                                        @if ($link->description)
                                            <p style="color:rgba(230,237,243,.6);font-size:.82rem;line-height:1.6;margin:.5rem 0 0;max-width:680px;">
                                                {{ Str::limit($link->description, 240) }}
                                            </p>
                                        @endif

                                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.5rem;font-size:.62rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-top:.5rem;">
                                            <span style="color:rgba(88,166,255,.8);">{{ $link->category->label() }}</span>
                                            <span>·</span>
                                            <span>{{ $link->created_at->diffForHumans() }}</span>
                                            @if($link->last_check)
                                                <span style="color:rgba(74,222,128,.5);">Verified {{ $link->last_check->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--color-gh-border);">
                                {{ $links->links('pagination.simple') }}
                            </div>
                        @else
                            <div style="padding:3rem 1rem;text-align:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="1.5" style="display:block;margin:0 auto .75rem;opacity:.35;"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round"/></svg>
                                <h2 style="font-size:1.2rem;font-weight:900;color:#fff;text-transform:uppercase;letter-spacing:-.02em;margin:0 0 .5rem;">Signature Lost</h2>
                                <p style="color:var(--color-gh-dim);font-size:.82rem;max-width:320px;margin:0 auto 1.5rem;">Couldn't triangulate nodes for "<strong>{{ $query }}</strong>". Try adjusting parameters.</p>
                                <a href="{{ route('home') }}" style="display:inline-block;padding:.5rem 1.25rem;border:1px solid var(--color-gh-border);border-radius:.4rem;color:var(--color-gh-text);text-decoration:none;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;">Return to Core</a>
                            </div>
                        @endif
                    </div>

                    {{-- Sidebar --}}
                    <aside>
                        @if(count($categoryBreakdown) > 0)
                            <div style="margin-bottom:1.5rem;">
                                <h3 style="font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.18em;margin:0 0 .75rem;padding-left:.5rem;border-left:2px solid var(--color-gh-accent);">Data Clusters</h3>
                                <div style="display:flex;flex-direction:column;gap:.1rem;">
                                    @foreach($categoryBreakdown as $catVal => $count)
                                        @php $cat = \App\Enum\Category::tryFrom($catVal); @endphp
                                        @if($cat)
                                            <a href="{{ route('search.index', ['q' => $query, 'category' => $catVal]) }}"
                                               style="display:flex;justify-content:space-between;align-items:center;padding:.4rem .5rem;border-radius:.35rem;text-decoration:none;color:var(--color-gh-dim);font-size:.78rem;">
                                                <span>{{ $cat->label() }}</span>
                                                <span style="font-size:.62rem;font-weight:800;color:var(--color-gh-dim);background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);padding:.1rem .4rem;border-radius:.25rem;">{{ $count }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Sidebar Ads --}}
                        @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                            <div>
                                <h3 style="font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.18em;margin:0 0 .75rem;padding-left:.5rem;border-left:2px solid var(--color-gh-sponsored);">Priority Nodes</h3>
                                <div style="display:flex;flex-direction:column;gap:.75rem;">
                                    @foreach ($sidebarAds as $ad)
                                        <a href="{{ route('ad.track', $ad->id) }}" style="text-decoration:none;display:block;">
                                            @if($ad->banner_path)
                                                <div style="width:100%;height:80px;border-radius:.4rem;overflow:hidden;border:1px solid var(--color-gh-border);margin-bottom:.3rem;">
                                                    <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" style="width:100%;height:100%;object-fit:cover;">
                                                </div>
                                            @endif
                                            <span style="font-size:.78rem;font-weight:700;color:#fff;">{{ $ad->title }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </aside>

                </div>
            </div>
            {{-- No query: controller should redirect to home --}}
        @endif
    </div>

</x-app.layouts>