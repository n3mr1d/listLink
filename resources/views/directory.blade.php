<x-app.layouts title="Verified Tor .Onion Directory"
    description="The most reliable Tor hidden services directory. Explore thousands of verified .onion links with daily uptime monitoring.">

    <style>
        .dir-layout { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        @media (min-width: 1024px) { .dir-layout { grid-template-columns: 1fr 260px; } }

        .dir-table { width: 100%; border-collapse: collapse; }
        .dir-table th { font-size: .6rem; font-weight: 800; color: var(--color-gh-dim); text-transform: uppercase; letter-spacing: .12em; padding: .5rem .85rem; text-align: left; border-bottom: 1px solid var(--color-gh-border); }
        .dir-table td { padding: .65rem .85rem; border-bottom: 1px solid var(--color-gh-border); vertical-align: middle; }
        .dir-table tr:last-child td { border-bottom: none; }

        .status-pill { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 2rem; font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }

        @media (max-width: 640px) { .dir-table .col-sync { display: none; } }
    </style>

    {{-- Header Ad --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div style="position:relative;width:100%;max-width:728px;height:80px;border-radius:.5rem;overflow:hidden;border:1px solid var(--color-gh-border);margin-bottom:1.5rem;">
                <span style="position:absolute;top:.3rem;right:.5rem;background:rgba(0,0,0,.75);color:var(--color-gh-sponsored);padding:.12rem .4rem;border-radius:.2rem;font-size:.58rem;font-weight:800;text-transform:uppercase;z-index:1;">Sponsored</span>
                @if ($headerAd->banner_path)
                    <a href="{{ route('ad.track', $headerAd->id) }}" style="display:block;width:100%;height:100%;">
                        <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}" style="width:100%;height:100%;object-fit:cover;">
                    </a>
                @else
                    <a href="{{ route('ad.track', $headerAd->id) }}" style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-btn-bg);">
                        <span style="font-size:.85rem;font-weight:800;color:#fff;">{{ $headerAd->title }}</span>
                    </a>
                @endif
            </div>
        @endforeach
    @endif

    <div class="dir-layout">

        {{-- ══ Main Column ══ --}}
        <div>
            {{-- Page heading --}}
            <div style="margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:baseline;justify-content:space-between;gap:1rem;">
                <div>
                    <h1 style="font-size:1.35rem;font-weight:900;color:#fff;margin:0 0 .2rem;letter-spacing:-.02em;">The Registry</h1>
                    <p style="color:var(--color-gh-dim);font-size:.8rem;margin:0;">Verified .onion services currently broadcasting on the network.</p>
                </div>
                <span style="font-size:.65rem;font-weight:700;color:var(--color-gh-dim);white-space:nowrap;">{{ number_format($stats['online_links']) }} nodes</span>
            </div>


            @foreach ($categories as $category)
                @if (isset($grouped[$category->value]) && $grouped[$category->value]->count() > 0)
                    <section style="margin-bottom:2rem;">

                        {{-- Category header --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem;padding-left:.5rem;">
                            <h2 style="font-size:.8rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.12em;border-left:2px solid var(--color-gh-accent);padding-left:.5rem;margin:0;">
                                {{ $category->label() }}
                                <span style="font-weight:600;color:var(--color-gh-dim);margin-left:.4rem;">({{ $grouped[$category->value]->count() }})</span>
                            </h2>
                            <a href="{{ route('category.show', $category->value) }}"
                               style="font-size:.65rem;font-weight:700;color:var(--color-gh-accent);text-decoration:none;">
                                View all →
                            </a>
                        </div>

                        {{-- Table --}}
                        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                            <table class="dir-table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th class="col-sync">Last Check</th>
                                        <th style="width:90px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Sponsored rows --}}
                                    @if ($loop->first && isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                                        @foreach ($sponsoredLinks as $sponsored)
                                            <tr style="background:rgba(210,153,34,.03);">
                                                <td>
                                                    <div style="display:flex;flex-direction:column;gap:.2rem;">
                                                        <div style="display:flex;align-items:center;gap:.4rem;">
                                                            <span style="font-size:.58rem;font-weight:800;background:var(--color-gh-sponsored);color:#0d1117;padding:.1rem .35rem;border-radius:.2rem;">AD</span>
                                                            <a href="{{ route('ad.track', $sponsored->id) }}"
                                                               style="font-size:.82rem;font-weight:700;color:var(--color-gh-sponsored);text-decoration:none;">{{ $sponsored->title }}</a>
                                                        </div>
                                                        <span style="font-size:.62rem;font-family:monospace;color:var(--color-gh-dim);opacity:.45;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:240px;">{{ $sponsored->url }}</span>
                                                    </div>
                                                </td>
                                                <td class="col-sync" style="font-size:.65rem;color:var(--color-gh-sponsored);opacity:.6;">Priority</td>
                                                <td>
                                                    <span style="font-size:.6rem;font-weight:800;border:1px solid rgba(210,153,34,.3);color:var(--color-gh-sponsored);padding:.2rem .5rem;border-radius:2rem;text-transform:uppercase;">Promoted</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    {{-- Regular rows --}}
                                    @foreach ($grouped[$category->value]->take(4) as $link)
                                        @php $isOnline = $link->uptime_status === \App\Enum\UptimeStatus::ONLINE; @endphp
                                        <tr>
                                            <td>
                                                <div style="display:flex;flex-direction:column;gap:.2rem;">
                                                    <a href="{{ route('link.show', $link->slug) }}"
                                                       style="font-size:.82rem;font-weight:700;color:var(--color-gh-accent);text-decoration:none;line-height:1.2;">{{ $link->title }}</a>
                                                    <span style="font-size:.62rem;font-family:monospace;color:var(--color-gh-dim);opacity:.4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:240px;">{{ $link->url }}</span>
                                                </div>
                                            </td>
                                            <td class="col-sync" style="font-size:.72rem;color:var(--color-gh-dim);">
                                                {{ $link->last_check ? $link->last_check->diffForHumans() : '—' }}
                                            </td>
                                            <td>
                                                <span class="status-pill" style="border:1px solid {{ $isOnline ? 'rgba(74,222,128,.3)' : 'rgba(248,113,113,.3)' }};color:{{ $isOnline ? '#4ade80' : '#f87171' }};">
                                                    <span style="width:4px;height:4px;border-radius:50%;background:{{ $isOnline ? '#4ade80' : '#f87171' }};flex-shrink:0;"></span>
                                                    {{ $link->uptime_status->label() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($grouped[$category->value]->count() > 4)
                                        <tr>
                                            <td colspan="3" style="padding: 0;">
                                                <a href="{{ route('category.show', $category->value) }}" 
                                                   style="display: block; width: 100%; padding: .75rem; text-align: center; text-decoration: none; color: var(--color-gh-accent); font-size: .75rem; font-weight: 700; background: rgba(56, 139, 253, 0.05); border-top: 1px solid var(--color-gh-border); transition: background 0.2s;">
                                                    View all {{ $grouped[$category->value]->count() }} links in {{ $category->label() }} →
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            @endforeach

            {{-- Recent additions --}}
            <section style="margin-top:1.5rem;">
                <h2 style="font-size:.72rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;margin:0 0 .75rem;display:flex;align-items:center;gap:.5rem;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Recent Additions
                </h2>
                <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                    <table class="dir-table">
                        <tbody>
                            @foreach ($recentlyAddedLinks->take(4) as $link)
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:.65rem;">
                                            <div style="width:1.75rem;height:1.75rem;border-radius:.3rem;background:rgba(88,166,255,.08);border:1px solid rgba(88,166,255,.15);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:800;color:var(--color-gh-accent);flex-shrink:0;">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div>
                                                <a href="{{ route('link.show', $link->slug) }}"
                                                   style="font-size:.8rem;font-weight:600;color:var(--color-gh-text);text-decoration:none;display:block;line-height:1.2;">{{ $link->title }}</a>
                                                <span style="font-size:.62rem;color:var(--color-gh-dim);opacity:.55;">{{ $link->category->label() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align:right;font-size:.65rem;font-weight:600;color:var(--color-gh-dim);">
                                        {{ $link->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Pagination --}}
            <div style="margin-top:1.5rem;">
                {{ $categories->links('pagination.simple') }}
            </div>
        </div>

        {{-- ══ Sidebar ══ --}}
        <aside style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Stats --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.85rem 1rem;">
                <h3 style="font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;margin:0 0 .85rem;display:flex;align-items:center;gap:.4rem;">
                    <span style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></span> System Vitals
                </h3>
                <div style="display:flex;flex-direction:column;gap:.65rem;">
                    @foreach([
                        ['label' => 'Active Nodes',      'val' => number_format($stats['total_links']),   'color' => '#fff'],
                        ['label' => 'Broadcasting Now',  'val' => number_format($stats['online_links']),  'color' => '#4ade80'],
                        ['label' => 'Indexed Segments',  'val' => number_format($stats['indexed_count']), 'color' => 'var(--color-gh-accent)'],
                    ] as $stat)
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:.68rem;color:var(--color-gh-dim);">{{ $stat['label'] }}</span>
                            <span style="font-size:1.1rem;font-weight:900;color:{{ $stat['color'] }};line-height:1;">{{ $stat['val'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:.85rem;padding-top:.75rem;border-top:1px solid var(--color-gh-border);">
                    <a href="{{ route('advertise.create') }}"
                       style="display:block;width:100%;padding:.55rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;text-align:center;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;text-decoration:none;box-sizing:border-box;">
                        Advertise Here
                    </a>
                </div>
            </div>

            {{-- Categories --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                <div style="padding:.6rem .9rem;border-bottom:1px solid var(--color-gh-border);font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;">
                    Categories
                </div>
                <div style="padding:.25rem 0;">
                    @foreach ($categories as $category)
                        <a href="{{ route('category.show', $category->value) }}"
                           style="display:flex;justify-content:space-between;align-items:center;padding:.45rem .9rem;text-decoration:none;color:var(--color-gh-dim);font-size:.78rem;">
                            {{ $category->label() }}
                            <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" opacity=".25"><path d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Donate --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.85rem 1rem;">
                <h3 style="font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.12em;margin:0 0 .4rem;">Support</h3>
                <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0 0 .6rem;line-height:1.5;opacity:.6;">Help maintain decentralized infrastructure.</p>
                <div style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.45rem .65rem;">
                    <div style="font-size:.6rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.2rem;">BTC</div>
                    <div style="font-family:monospace;font-size:.65rem;color:var(--color-gh-accent);word-break:break-all;user-select:all;">{{ config('Donate.btc') }}</div>
                </div>
            </div>

            {{-- Sidebar Ads --}}
            @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                @foreach ($sidebarAds as $sideAd)
                    <div style="position:relative;width:100%;height:180px;border-radius:.5rem;overflow:hidden;border:1px solid var(--color-gh-border);">
                        <span style="position:absolute;top:.3rem;right:.45rem;background:rgba(0,0,0,.75);color:var(--color-gh-sponsored);padding:.1rem .35rem;border-radius:.2rem;font-size:.55rem;font-weight:800;text-transform:uppercase;z-index:1;">Ad</span>
                        @if ($sideAd->banner_path)
                            <a href="{{ route('ad.track', $sideAd->id) }}" style="display:block;width:100%;height:100%;">
                                <img src="{{ asset('storage/' . $sideAd->banner_path) }}" alt="{{ $sideAd->title }}" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @else
                            <a href="{{ route('ad.track', $sideAd->id) }}" style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-btn-bg);">
                                <span style="font-size:.8rem;font-weight:700;color:#fff;text-align:center;padding:.75rem;">{{ $sideAd->title }}</span>
                            </a>
                        @endif
                    </div>
                @endforeach
            @endif

        </aside>
    </div>

</x-app.layouts>