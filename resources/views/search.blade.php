<x-app.layouts title="{{ $query ? 'Results for ' . $query : 'Verified Tor .Onion Directory' }} - {{ config('app.name') }}"
    description="Search across thousands of verified Tor hidden services. Privacy-focused search engine for the darknet.">

    <style>
        .search-layout { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        @media (min-width: 768px) { .search-layout { grid-template-columns: 1fr 260px; } }

        .stats-grid {
            margin-top: 3rem;
            width: 100%;
            max-width: 850px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            border-top: 1px solid rgba(48,54,61,.4);
            padding-top: 1.5rem;
            text-align: center;
        }
        .activity-grid {
            max-width: 900px;
            margin: 2rem auto 0;
            padding: 0 1rem 3rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            opacity: .75;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
            .stats-grid > div:last-child {
                grid-column: span 2;
            }
            .activity-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .stats-grid > div:last-child {
                grid-column: auto;
            }
        }

        /* ── Keyword highlight ── */
        mark.kw-hl {
            background: rgba(88,166,255,.18);
            color: var(--color-gh-accent);
            border-radius: .2rem;
            padding: 0 .15rem;
            font-weight: 700;
            font-style: inherit;
        }

        /* ── Correction / interpretation banner ── */
        .srp-banner {
            font-size: .78rem;
            line-height: 1.6;
            padding: .55rem .85rem;
            border-radius: .4rem;
            margin-bottom: 1rem;
            border-left: 3px solid var(--color-gh-accent);
            background: rgba(88,166,255,.06);
            color: var(--color-gh-text);
        }
        .srp-banner a {
            color: var(--color-gh-dim);
            text-decoration: underline;
            text-underline-offset: 2px;
        }
        .srp-banner a:hover { color: #fff; }

        /* ── Intent chip ── */
        .intent-chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .55rem;
            border-radius: 2rem;
            border: 1px solid rgba(88,166,255,.25);
            background: rgba(88,166,255,.06);
            font-size: .58rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--color-gh-accent);
            margin-bottom: .9rem;
        }
        .intent-chip.navigational {
            border-color: rgba(74,222,128,.35);
            background: rgba(74,222,128,.06);
            color: #4ade80;
        }
        .intent-chip.transactional {
            border-color: rgba(251,191,36,.35);
            background: rgba(251,191,36,.06);
            color: #fbbf24;
        }

        /* ── Relevance score badge ── */
        .relevance-badge {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .45rem;
            border-radius: .25rem;
            font-size: .55rem;
            font-weight: 900;
            font-family: monospace;
            letter-spacing: .05em;
            flex-shrink: 0;
        }
        .relevance-badge.high {
            background: rgba(74,222,128,.12);
            color: #4ade80;
            border: 1px solid rgba(74,222,128,.3);
        }
        .relevance-badge.medium {
            background: rgba(88,166,255,.12);
            color: var(--color-gh-accent);
            border: 1px solid rgba(88,166,255,.3);
        }
        .relevance-badge.low {
            background: rgba(248,113,113,.08);
            color: #f87171;
            border: 1px solid rgba(248,113,113,.25);
        }

        /* ── Ranking reasons ── */
        .ranking-reasons {
            display: flex;
            flex-wrap: wrap;
            gap: .25rem;
            margin-top: .4rem;
        }
        .ranking-reason {
            font-size: .52rem;
            font-weight: 700;
            color: var(--color-gh-dim);
            background: rgba(88,166,255,.05);
            padding: .1rem .35rem;
            border-radius: .2rem;
            border: 1px solid rgba(48,54,61,.6);
        }

        /* ── Result index number ── */
        .result-index {
            font-size: .65rem;
            font-weight: 900;
            color: rgba(88,166,255,.35);
            font-family: monospace;
            min-width: 1.5rem;
            text-align: right;
            flex-shrink: 0;
            padding-top: .15rem;
        }

        /* ── Synonym chips ── */
        .synonym-chip {
            display: inline-block;
            font-size: .55rem;
            padding: .1rem .35rem;
            border-radius: .2rem;
            background: rgba(139,92,246,.08);
            border: 1px solid rgba(139,92,246,.25);
            color: #a78bfa;
            font-weight: 700;
        }

        /* ── Related suggestions ── */
        .related-searches {
            margin: 1.25rem 0 .5rem;
            padding: .85rem 1rem;
            border: 1px solid var(--color-gh-border);
            border-radius: .45rem;
            background: rgba(13,17,23,.5);
        }
        .related-searches h4 {
            font-size: .6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: var(--color-gh-dim);
            margin: 0 0 .6rem;
        }
        .related-searches ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
        }
        .related-searches ul li a {
            display: inline-block;
            padding: .25rem .65rem;
            border: 1px solid var(--color-gh-border);
            border-radius: 2rem;
            font-size: .72rem;
            color: var(--color-gh-dim);
            text-decoration: none;
            transition: border-color .15s, color .15s;
        }
        .related-searches ul li a:hover {
            border-color: rgba(88,166,255,.5);
            color: var(--color-gh-accent);
        }

        /* ── Snippets & Triggers ── */
        .trigger-tags {
            margin-top: .6rem;
            display: flex;
            flex-wrap: wrap;
            gap: .3rem;
            align-items: center;
        }
        .trigger-label {
            font-size: .55rem;
            font-weight: 800;
            color: var(--color-gh-dim);
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .trigger-tag {
            font-size: .62rem;
            font-weight: 700;
            color: var(--color-gh-accent);
            background: rgba(88,166,255,.1);
            padding: .1rem .4rem;
            border-radius: .25rem;
            border: 1px solid rgba(88,166,255,.15);
        }
        /* ── Ranking Insights Panel ── */
        .ranking-insights {
            display: none;
            margin-top: .75rem;
            padding: .75rem;
            background: rgba(13,17,23,.8);
            border: 1px solid var(--color-gh-border);
            border-radius: .4rem;
            font-size: .68rem;
        }
        .ranking-insights:target {
            display: block;
            animation: slideDown .2s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .insight-row {
            display: flex;
            justify-content: space-between;
            padding: .2rem 0;
            border-bottom: 1px solid rgba(48,54,61,.4);
        }
        .insight-row:last-child { border-bottom: none; }
        .insight-label { color: var(--color-gh-dim); }
        .insight-value { color: #fff; font-weight: 700; }

        /* ── Trust Indicators ── */
        .trust-tag {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .15rem .4rem;
            border-radius: 2rem;
            font-size: .52rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .trust-high { background: rgba(74,222,128,.1); color: #4ade80; border: 1px solid rgba(74,222,128,.2); }
        .trust-med { background: rgba(88,166,255,.1); color: var(--color-gh-accent); border: 1px solid rgba(88,166,255,.2); }
        .trust-low { background: rgba(248,113,113,.1); color: #f87171; border: 1px solid rgba(248,113,113,.2); }

        .snippet-box {
            margin-top: .65rem;
            font-size: .75rem;
            line-height: 1.5;
            color: rgba(230,237,243,.7);
        }

        /* ── Sponsored inline ── */
        .ad-badge {
            display: inline-block;
            font-size: .46rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: var(--color-gh-sponsored);
            border: 1px solid rgba(210,153,34,.4);
            border-radius: .2rem;
            padding: .1rem .3rem;
            vertical-align: middle;
            flex-shrink: 0;
        }
        .search-ads-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin: 2rem 0;
            justify-items: center;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .search-ads-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .search-ad-box {
            position: relative;
            width: 468px;
            height: 60px;
            max-width: 100%;
            border-radius: .4rem;
            overflow: hidden;
            border: 1px solid var(--color-gh-border);
            flex-shrink: 0;
        }
    </style>

    {{-- ══════════════════════════════════════════ --}}
    {{-- NO QUERY: homepage-style centered hero     --}}
    {{-- ══════════════════════════════════════════ --}}
    @if(!$query)

        <div style="display:flex;flex-direction:column;min-height:65vh;align-items:center;justify-content:center;padding:1rem;">

            {{-- Hero --}}
            <div style="width:100%;max-width:600px;display:flex;flex-direction:column;align-items:center;margin-bottom:2rem;text-align:center;">
                <x-app.logo style="height:7rem;margin-bottom:.5rem;opacity:.9;" />
                <h1 style="font-size:2rem;font-weight:900;color:#fff;letter-spacing:-.02em;margin:0 0 .3rem;">{{ config('app.name') }}</h1>
                <p style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.2em;color:var(--color-gh-dim);margin:0;">Decentralized Search Protocol without javascript</p>

                {{-- ── Dual Gateway Badge ── --}}
                @php
                    $torUrl      = config('site.tor_url');
                    $clearnetUrl = config('site.clearnet_url');
                    $currentHost = request()->getHost();
                    $torHost     = parse_url($torUrl, PHP_URL_HOST);
                    $isOnTor     = $torHost && str_ends_with($currentHost, $torHost);
                @endphp
                @if($clearnetUrl)
                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.85rem;flex-wrap:wrap;justify-content:center;">
                    {{-- Tor Gate --}}
                    <a href="{{ $torUrl }}"
                       title="Access via Tor Network"
                       style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:2rem;border:1px solid {{ $isOnTor ? 'rgba(74,222,128,.55)' : 'rgba(48,54,61,.7)' }};background:{{ $isOnTor ? 'rgba(74,222,128,.08)' : 'rgba(13,17,23,.6)' }};text-decoration:none;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ $isOnTor ? '#4ade80' : 'var(--color-gh-dim)' }}" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
                        <span style="font-size:.58rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:{{ $isOnTor ? '#4ade80' : 'var(--color-gh-dim)' }};">Tor Gate</span>
                        @if($isOnTor)<span style="width:5px;height:5px;border-radius:50%;background:#4ade80;box-shadow:0 0 5px #4ade80;"></span>@endif
                    </a>

                    <span style="color:var(--color-gh-border);font-size:.65rem;">↔</span>

                    {{-- Clearnet Gate --}}
                    <a href="{{ $clearnetUrl }}"
                       title="Access via Clearnet (HTTPS)"
                       style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:2rem;border:1px solid {{ !$isOnTor ? 'rgba(88,166,255,.55)' : 'rgba(48,54,61,.7)' }};background:{{ !$isOnTor ? 'rgba(88,166,255,.08)' : 'rgba(13,17,23,.6)' }};text-decoration:none;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="{{ !$isOnTor ? 'var(--color-gh-accent)' : 'var(--color-gh-dim)' }}" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span style="font-size:.58rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:{{ !$isOnTor ? 'var(--color-gh-accent)' : 'var(--color-gh-dim)' }};">Clearnet Gate</span>
                        @if(!$isOnTor)<span style="width:5px;height:5px;border-radius:50%;background:var(--color-gh-accent);box-shadow:0 0 5px var(--color-gh-accent);"></span>@endif
                    </a>
                </div>
                @endif
            </div>


            {{-- Search bar --}}
            <form action="{{ route('search.index') }}" method="GET" style="width:100%;max-width:540px;">
                <div style="display:flex;align-items:center;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
                    <span style="padding:.6rem .75rem;color:var(--color-gh-dim);display:flex;align-items:center;flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search anything on the darknet..."
                        style="flex:1;background:transparent;border:none;color:#fff;font-size:.95rem;padding:.6rem .25rem;outline:none;">
                    <button type="submit"
                        style="background:var(--color-gh-accent);color:#0d1117;padding:.6rem 1.1rem;border:none;font-weight:800;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;cursor:pointer;white-space:nowrap;">Search</button>
                </div>

                <div style="display:flex;justify-content:center;gap:1.5rem;margin-top:1rem;flex-wrap:wrap;">
                    <a href="{{ route('directory') }}" style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.12em;text-decoration:none;">Browse Directory</a>
                    <span style="color:var(--color-gh-border);">|</span>
                    <a href="{{ route('leaderboard') }}" style="font-size:.65rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.12em;text-decoration:none;">Network Elite</a>
                    <span style="color:var(--color-gh-border);">|</span>
                    <a href="{{ route('advertise.create') }}" style="font-size:.65rem;font-weight:800;color:var(--color-gh-sponsored);text-transform:uppercase;letter-spacing:.12em;text-decoration:none;">Promote Node</a>
                </div>
            </form>

            <div class="stats-grid">
                <div>
                    <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['online_links']) }}</span>
                    <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Online Nodes</span>
                </div>
                <div>
                    <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['indexed_count']) }}</span>
                    <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Pages Indexed</span>
                </div>
                <div>
                    <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['total_links']) }}</span>
                    <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Total Links</span>
                </div>
                <div>
                    <div style="display:flex;align-items:center;justify-content:center;gap:.3rem;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;box-shadow:0 0 5px #4ade80;"></span>
                        <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['live_viewers']) }}</span>
                    </div>
                    <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Live Viewers</span>
                </div>
                <div>
                    <span style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['total_views']) }}</span>
                    <span style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Total Views</span>
                </div>
            </div>

        {{-- Header Ad Grid (No Query) --}}
        @if (isset($headerAds) && $headerAds->count() > 0)
            <div class="search-ads-grid" style="max-width: 970px;">
                @foreach ($headerAds as $ad)
                    <div class="search-ad-box">
                        <span style="position:absolute;top:.2rem;right:.4rem;background:rgba(0,0,0,.7);color:var(--color-gh-sponsored);padding:.1rem .35rem;border-radius:.2rem;font-size:8px;font-weight:800;text-transform:uppercase;z-index:1;border:1px solid rgba(210,153,34,.2);">Sponsored</span>
                        @if ($ad->banner_path)
                            <a href="{{ route('ad.track', $ad->id) }}" style="display:block;width:100%;height:100%;">
                                <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @else
                            <a href="{{ route('ad.track', $ad->id) }}" style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-btn-bg);">
                                <div style="text-align:center;">
                                    <div style="font-size:.7rem;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.05em;">{{ $ad->title }}</div>
                                    <div style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.6;">{{ $ad->url }}</div>
                                </div>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Live Activity --}}
    <div class="activity-grid">
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

    {{-- ══════════════════════════════════════════ --}}
    {{-- HAS QUERY: search results layout           --}}
    {{-- ══════════════════════════════════════════ --}}
    @else

        <div style="max-width:1100px;margin:0 auto;padding:0 0 3rem;">



            {{-- Header Ad Grid (Has Query) --}}
            @if (isset($headerAds) && $headerAds->count() > 0)
                <div class="search-ads-grid" style="margin-bottom: 1.5rem;">
                    @foreach ($headerAds as $ad)
                        <div class="search-ad-box">
                            <span style="position:absolute;top:.2rem;right:.4rem;background:rgba(0,0,0,.7);color:var(--color-gh-sponsored);padding:.1rem .35rem;border-radius:.2rem;font-size:8px;font-weight:800;text-transform:uppercase;z-index:1;border:1px solid rgba(210,153,34,.2);">Sponsored</span>
                            @if ($ad->banner_path)
                                <a href="{{ route('ad.track', $ad->id) }}" class="internal-ad-link" style="display:block;width:100%;height:100%;">
                                    <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" style="width:100%;height:100%;object-fit:cover;">
                                </a>
                            @else
                                <a href="{{ route('ad.track', $ad->id) }}" class="internal-ad-link" style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-btn-bg);">
                                    <div style="text-align:center;">
                                        <div style="font-size:.7rem;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.05em;">{{ $ad->title }}</div>
                                        <div style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.6;">{{ $ad->url }}</div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Compact search bar with Logo --}}
            <div style="margin-bottom:2rem;padding-top:1rem;border-top:1px solid var(--color-gh-border);display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
                <a href="{{ route('search.index') }}" style="display:flex;align-items:center;text-decoration:none;gap:.5rem;flex-shrink:0;">
                    <x-app.logo style="height:2.2rem;opacity:.9;" />
                    <span style="font-size:1.1rem;font-weight:900;color:#fff;letter-spacing:-.02em;">{{ config('app.name') }}</span>
                </a>

                <form action="{{ route('search.index') }}" method="GET" style="flex:1;max-width:600px;">
                    <div style="display:flex;align-items:center;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:2rem;overflow:hidden;padding:0 .5rem 0 1rem;">
                        <span style="color:var(--color-gh-dim);display:flex;align-items:center;flex-shrink:0;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        </span>
                        <input type="text" name="q" value="{{ $query }}" placeholder="Search nodes..."
                            style="flex:1;background:transparent;border:none;color:#fff;font-size:.85rem;padding:.5rem .75rem;outline:none;">
                        <button type="submit" style="background:none;border:none;color:var(--color-gh-accent);cursor:pointer;padding:.35rem;display:flex;align-items:center;gap:.35rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span style="font-size:.82rem;font-weight:800;color:#fff;letter-spacing:.05em;">{{ number_format($links->total()) }}</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Intent chip + Correction banner --}}
            @if($interpretation)
                {{-- Intent classification chip --}}
                @php
                    $intentData = $interpretation['intent'] ?? ['type' => 'informational', 'confidence' => 50, 'reason' => ''];
                    $intentType = $intentData['type'] ?? 'informational';
                    $intentConf = $intentData['confidence'] ?? 50;
                    $intentIcon = match($intentType) {
                        'navigational' => '⊕',
                        'transactional' => '⚡',
                        default => '◉',
                    };
                @endphp
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.5rem;margin-bottom:.6rem;">
                    <span class="intent-chip {{ $intentType }}">
                        {{ $intentIcon }} {{ ucfirst($intentType) }} · {{ $intentConf }}%
                    </span>
                    @if(!empty($interpretation['synonyms']))
                        @foreach(array_slice($interpretation['synonyms'], 0, 4) as $syn)
                            <span class="synonym-chip">≈ {{ $syn }}</span>
                        @endforeach
                    @endif
                </div>

                {{-- Correction banner --}}
                @if($correctedQuery)
                    <div class="srp-banner" role="status">
                        <span style="color:#fff;font-weight:700;">Showing results for:</span>
                        <em style="color:var(--color-gh-accent);font-style:normal;font-weight:700;"> {{ $correctedQuery }}</em><br>
                        <span style="font-size:.7rem;color:var(--color-gh-dim);">
                            Search instead for:
                            <a href="{{ route('search.index', array_merge(request()->except('q'), ['q' => $query])) }}">{{ $query }}</a>
                        </span>
                    </div>
                @else
                    <div style="font-size:.75rem;color:var(--color-gh-dim);margin-bottom:.9rem;">
                        Showing results for: <strong style="color:#fff;">{{ $query }}</strong>
                    </div>
                @endif
            @endif

            {{-- Results + Sidebar --}}
            <div class="search-layout">

                {{-- Results --}}
                <div class="results-container">
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:.65rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;border-bottom:1px solid var(--color-gh-border);padding-bottom:.6rem;margin-bottom:1.5rem;">
                        <span>Revealing <span style="color:#fff;">{{ number_format($links->total()) }} Links</span></span>
                        <span style="font-style:italic;">{{ $searchTime ?? '?' }}ms</span>
                    </div>

                    @if ($links && $links->total() > 0)

                        {{-- ── Sponsored Inline Listing ── --}}
                        @if(isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                            @foreach($sponsoredLinks as $ad)
                                <article class="search-result-item" style="padding:.75rem 0;">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
                                        <div style="min-width:0;flex:1;">
                                            <h3 style="margin:0 0 .2rem;font-size:.95rem;font-weight:700;line-height:1.35;">
                                                <a href="{{ route('ad.track', $ad->id) }}" class="internal-ad-link"
                                                   style="color:var(--color-gh-accent);text-decoration:none;">{{ $ad->title }}</a>
                                            </h3>
                                            <div style="display:flex;align-items:center;gap:.4rem;margin-top:.1rem;">
                                                <span style="width:5px;height:5px;border-radius:50%;flex-shrink:0;background:#4ade80;"></span>
                                                <span style="font-size:.6rem;font-family:monospace;color:var(--color-gh-dim);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:340px;opacity:.55;">{{ $ad->url }}</span>
                                            </div>
                                        </div>
                                        <span class="ad-badge">Ad</span>
                                    </div>
                                    @if($ad->description)
                                        <p style="color:rgba(230,237,243,.55);font-size:.8rem;line-height:1.55;margin:.5rem 0 0;max-width:680px;">{{ Str::limit($ad->description, 200) }}</p>
                                    @endif
                                </article>
                            @endforeach
                            {{-- Thin separator before organic results --}}
                            <div style="display:flex;align-items:center;gap:.6rem;margin:.5rem 0 .25rem;">
                                <span style="flex:1;height:1px;background:var(--color-gh-border);"></span>
                                <span style="font-size:.48rem;font-weight:800;text-transform:uppercase;letter-spacing:.18em;color:var(--color-gh-dim);white-space:nowrap;">Results</span>
                                <span style="flex:1;height:1px;background:var(--color-gh-border);"></span>
                            </div>
                        @endif
                        {{-- ── End Sponsored ── --}}



                        <div style="display:flex;flex-direction:column;gap:.5rem;">
                            @foreach ($links as $index => $link)

                                @php
                                    $isOnline = $link->uptime_status === \App\Enum\UptimeStatus::ONLINE;
                                    $rScore = $link->relevance_score ?? 0;
                                    $rClass = $rScore >= 60 ? 'high' : ($rScore >= 30 ? 'medium' : 'low');
                                    $rReasons = $link->ranking_reasons ?? [];
                                    $resultNum = ($links->currentPage() - 1) * $links->perPage() + $index + 1;
                                @endphp
                                <article class="search-result-item" style="padding:.75rem 0;border-bottom:1px solid var(--color-gh-border);">

                                    {{-- Top row: index + title + relevance + status --}}
                                    <div style="display:flex;align-items:flex-start;gap:.6rem;">
                                        {{-- Result index --}}
                                        <span class="result-index">#{{ $resultNum }}</span>

                                        <div style="min-width:0;flex:1;">
                                            {{-- Title row --}}
                                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;">
                                                <h3 style="margin:0 0 .2rem;font-size:.95rem;font-weight:700;line-height:1.35;">
                                                    <a href="{{ route('search.redirect', ['url' => $link->url, 'q' => $query, 'link_id' => $link->id]) }}" 
                                                       class="result-link"
                                                       style="color:var(--color-gh-accent);text-decoration:none;">{{ $link->title }}</a>
                                                </h3>
                                                <div style="display:flex;align-items:center;gap:.35rem;flex-shrink:0;">
                                                    {{-- Relevance Score Badge --}}
                                                    @if($rScore > 0)
                                                        <span class="relevance-badge {{ $rClass }}" title="Relevance Score">
                                                            <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                            {{ $rScore }}
                                                        </span>
                                                    @endif
                                                    @if($link->latestCrawlLog?->http_status)
                                                        <span style="font-family:monospace;font-size:.58rem;color:var(--color-gh-dim);">{{ $link->latestCrawlLog->http_status }}</span>
                                                    @endif
                                                    @if($link->latestCrawlLog && str_contains($link->latestCrawlLog->error_message ?? '', 'Bot-challenge / WAF blocked'))
                                                        <span style="font-size:.58rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--color-gh-sponsored);border:1px solid rgba(210,153,34,.5);padding:.1rem .3rem;border-radius:.2rem;background:rgba(210,153,34,.1);">Bot Blocked</span>
                                                    @endif
                                                    <span style="font-size:.58rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:{{ $isOnline ? '#4ade80' : '#f87171' }};">
                                                        {{ $link->uptime_status->label() }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- URL row --}}
                                            <div style="display:flex;align-items:center;gap:.4rem;margin-top:.1rem;">
                                                <span style="width:5px;height:5px;border-radius:50%;flex-shrink:0;background:{{ $isOnline ? '#4ade80' : '#f87171' }};"></span>
                                                <span style="font-size:.6rem;font-family:monospace;color:var(--color-gh-dim);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:340px;opacity:.55;">{{ $link->url }}</span>
                                            </div>

                                            {{-- Description (with keyword highlighting) --}}
                                            @if ($link->highlighted_description)
                                                <p style="color:rgba(230,237,243,.55);font-size:.8rem;line-height:1.55;margin:.5rem 0 0;max-width:680px;">
                                                    {!! $link->highlighted_description !!}
                                                </p>
                                            @endif

                                            {{-- Content Snippets --}}
                                            @if($link->snippet_content)
                                                <div class="snippet-box">
                                                    {!! $link->snippet_content !!}
                                                </div>
                                            @endif

                                            {{-- Meta row + Insights Toggle --}}
                                            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.35rem;margin-top:.55rem;">
                                                <div style="display:flex;align-items:center;gap:.35rem;font-size:.6rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;">
                                                    <span style="color:rgba(88,166,255,.8);">{{ $link->category->label() }}</span>
                                                    <span style="opacity:.35;">·</span>
                                                    <span>{{ $link->created_at->diffForHumans() }}</span>
                                                    @if($link->last_check)
                                                        <span style="opacity:.35;">·</span>
                                                        <span style="color:rgba(74,222,128,.6);">Last Check {{ $link->last_check->diffForHumans() }}</span>
                                                    @endif
                                                </div>
                                                
                                                <a href="#insight-{{ $link->id }}" style="font-size:.58rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;letter-spacing:.05em;border:1px solid var(--color-gh-border);padding:.1rem .4rem;border-radius:.25rem;">
                                                    Why this result?
                                                </a>
                                            </div>

                                            {{-- Ranking Insights Panel --}}
                                            <div id="insight-{{ $link->id }}" class="ranking-insights">
                                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                                                    <span style="font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-accent);">Ranking Breakdown</span>
                                                    <a href="#" style="color:var(--color-gh-dim);text-decoration:none;font-size:1rem;">&times;</a>
                                                </div>
                                                <div class="insight-row">
                                                    <span class="insight-label">Relevance Score</span>
                                                    <span class="insight-value">{{ $rScore }}/100</span>
                                                </div>
                                                <div class="insight-row">
                                                    <span class="insight-label">Semantic Match</span>
                                                    <span class="insight-value">{{ str_contains(implode(' ', $rReasons), 'Semantic') ? 'High' : 'Medium' }}</span>
                                                </div>
                                                <div class="insight-row">
                                                    <span class="insight-label">Source Trust</span>
                                                    <span class="insight-value">{{ $link->is_featured ? 'Verified' : ($link->user_id ? 'Registered' : 'Crowdsourced') }}</span>
                                                </div>
                                                <div style="margin-top:.5rem;padding-top:.4rem;border-top:1px solid rgba(48,54,61,.6);">
                                                    <span style="display:block;font-size:.55rem;color:var(--color-gh-dim);margin-bottom:.25rem;text-transform:uppercase;">Primary Ranking Factors:</span>
                                                    <div class="ranking-reasons">
                                                        @foreach($rReasons as $reason)
                                                            <span class="ranking-reason">{{ $reason }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Related searches --}}
                        @if(count($relatedSuggestions) > 0)
                            <div class="related-searches">
                                <h4>Related searches</h4>
                                <ul>
                                    @foreach($relatedSuggestions as $suggestion)
                                        <li>
                                            <a href="{{ route('search.index', ['q' => $suggestion]) }}">{{ $suggestion }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                     

                        <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--color-gh-border);">
                            {{ $links->links('pagination.simple') }}
                        </div>
                    @else
                        <div style="padding:4rem 1rem;text-align:center;">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="1.5" style="display:block;margin:0 auto 1rem;opacity:.3;">
                                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
                            </svg>
                            <h2 style="font-size:1rem;font-weight:900;color:#fff;text-transform:uppercase;letter-spacing:.1em;margin:0 0 .5rem;">No Nodes Found</h2>
                            <p style="color:var(--color-gh-dim);font-size:.78rem;max-width:300px;margin:0 auto 1.75rem;line-height:1.6;">
                                No results for <span style="color:#fff;font-weight:700;">"{{ $query }}"</span>.<br>Try a different keyword or browse by category.
                            </p>
                            <a href="{{ route('search.index') }}"
                               style="display:inline-block;padding:.45rem 1.1rem;border:1px solid var(--color-gh-border);border-radius:2rem;color:var(--color-gh-dim);text-decoration:none;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;">
                                ← Clear Search
                            </a>
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

                    {{-- Sidebar Ads (Internal) --}}
                    @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                        <div>
                            <h3 style="font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.18em;margin:0 0 .75rem;padding-left:.5rem;border-left:2px solid var(--color-gh-sponsored);">Priority Nodes</h3>
                            


                            <div style="display:flex;flex-direction:column;gap:.75rem;">
                                @foreach ($sidebarAds as $ad)
                                    <div style="position:relative; width:100%; border: 1px solid var(--color-gh-border); border-radius: .4rem; padding: .5rem; background: var(--color-gh-btn-bg);">
                                        <a href="{{ route('ad.track', $ad->id) }}" class="internal-ad-link" style="text-decoration:none;display:block;">
                                            @if($ad->banner_path)
                                                <div style="width:100%;height:80px;border-radius:.3rem;overflow:hidden;margin-bottom:.35rem;">
                                                    <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="{{ $ad->title }}" style="width:100%;height:100%;object-fit:cover;">
                                                </div>
                                            @endif
                                            <span style="font-size:.78rem;font-weight:700;color:#fff;display:block;line-height:1.2;">{{ $ad->title }}</span>
                                            <span style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.5;word-break:break-all;">{{ Str::limit($ad->url, 40) }}</span>
                                        </a>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    @endif
                </aside>

            </div>
        </div>


    @endif


</x-app.layouts>
