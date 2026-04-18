<x-app.layouts title="Verified Tor .Onion Directory"
    description="The most reliable Tor hidden services directory. Daily uptime monitoring, verified .onion links, and community driven indexing.">

    <style>
        .stats-grid {
            margin-top: 3rem;
            width: 100%;
            max-width: 850px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            border-top: 1px solid rgba(48, 54, 61, .4);
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

            .stats-grid>div:last-child {
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

            .stats-grid>div:last-child {
                grid-column: auto;
            }
        }
    </style>

    <div
        style="display:flex;flex-direction:column;min-height:65vh;align-items:center;justify-content:center;padding:1rem;">

        {{-- Hero --}}
        <div
            style="width:100%;max-width:600px;display:flex;flex-direction:column;align-items:center;margin-bottom:2rem;text-align:center;">
            <x-app.logo style="height:7rem;margin-bottom:.5rem;opacity:.9;" />
            <h1 style="font-size:2rem;font-weight:900;color:#fff;letter-spacing:-.02em;margin:0 0 .3rem;">
                {{ config('app.name') }}
            </h1>
            <p
                style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.2em;color:var(--color-gh-dim);margin:0;">
                Decentralized Search Protocol without javascript</p>

            {{-- ── Dual Gateway Badge ── --}}
            @php
                $torUrl = config('site.tor_url');
                $clearnetUrl = config('site.clearnet_url');
                $currentHost = request()->getHost();
                $torHost = parse_url($torUrl, PHP_URL_HOST);
                $isOnTor = $torHost && str_ends_with($currentHost, $torHost);
            @endphp
            @if($clearnetUrl)
                <div
                    style="display:flex;align-items:center;gap:.5rem;margin-top:.85rem;flex-wrap:wrap;justify-content:center;">
                    {{-- Tor Gate --}}
                    <a href="{{ $torUrl }}" title="Access via Tor Network"
                        style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:2rem;border:1px solid {{ $isOnTor ? 'rgba(74,222,128,.55)' : 'rgba(48,54,61,.7)' }};background:{{ $isOnTor ? 'rgba(74,222,128,.08)' : 'rgba(13,17,23,.6)' }};text-decoration:none;">
                        {{-- Tor onion SVG --}}
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                            stroke="{{ $isOnTor ? '#4ade80' : 'var(--color-gh-dim)' }}" stroke-width="2.5"
                            stroke-linecap="round">
                            <circle cx="12" cy="12" r="10" />
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                            <path d="M2 12h20" />
                        </svg>
                        <span
                            style="font-size:.58rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:{{ $isOnTor ? '#4ade80' : 'var(--color-gh-dim)' }};">
                            Tor Gate
                        </span>
                        @if($isOnTor)
                            <span
                                style="width:5px;height:5px;border-radius:50%;background:#4ade80;box-shadow:0 0 5px #4ade80;"></span>
                        @endif
                    </a>

                    <span style="color:var(--color-gh-border);font-size:.65rem;">↔</span>

                    {{-- Clearnet Gate --}}
                    <a href="{{ $clearnetUrl }}" title="Access via Clearnet (HTTPS)"
                        style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:2rem;border:1px solid {{ !$isOnTor ? 'rgba(88,166,255,.55)' : 'rgba(48,54,61,.7)' }};background:{{ !$isOnTor ? 'rgba(88,166,255,.08)' : 'rgba(13,17,23,.6)' }};text-decoration:none;">
                        {{-- HTTPS lock SVG --}}
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                            stroke="{{ !$isOnTor ? 'var(--color-gh-accent)' : 'var(--color-gh-dim)' }}" stroke-width="2.5"
                            stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <span
                            style="font-size:.58rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:{{ !$isOnTor ? 'var(--color-gh-accent)' : 'var(--color-gh-dim)' }};">
                            Clearnet Gate
                        </span>
                        @if(!$isOnTor)
                            <span
                                style="width:5px;height:5px;border-radius:50%;background:var(--color-gh-accent);box-shadow:0 0 5px var(--color-gh-accent);"></span>
                        @endif
                    </a>
                </div>
            @endif
        </div>


        {{-- Search --}}
        <form action="{{ route('search.index') }}" method="GET" style="width:100%;max-width:540px;">
            <div
                style="display:flex;align-items:center;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
                <span
                    style="padding:.6rem .75rem;color:var(--color-gh-dim);display:flex;align-items:center;flex-shrink:0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search anything on the darknet..."
                    style="flex:1;background:transparent;border:none;color:#fff;font-size:.95rem;padding:.6rem .25rem;outline:none;">
                <button type="submit"
                    style="background:var(--color-gh-accent);color:#0d1117;padding:.6rem 1.1rem;border:none;font-weight:800;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;cursor:pointer;white-space:nowrap;">Search</button>
            </div>

            <div style="display:flex;justify-content:center;gap:1.5rem;margin-top:1rem;">
                <a href="{{ route('directory') }}"
                    style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.12em;text-decoration:none;">Browse
                    Directory</a>
                <span style="color:var(--color-gh-border);">|</span>
                <a href="{{ route('advertise.create') }}"
                    style="font-size:.65rem;font-weight:800;color:var(--color-gh-sponsored);text-transform:uppercase;letter-spacing:.12em;text-decoration:none;">
                    Advertise</a>
            </div>
        </form>

        {{-- Stats --}}
        <div class="stats-grid">
            <div>
                <span
                    style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['online_links']) }}</span>
                <span
                    style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Online
                    Link</span>
            </div>
            <div>
                <span
                    style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['indexed_count']) }}</span>
                <span
                    style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Pages
                    Indexed</span>
            </div>
            <div>
                <span
                    style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['total_users']) }}</span>
                <span
                    style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Users</span>
            </div>
            <div>
                <div style="display:flex;align-items:center;justify-content:center;gap:.3rem;">
                    <span
                        style="width:6px;height:6px;border-radius:50%;background:#4ade80;box-shadow:0 0 5px #4ade80;"></span>
                    <span
                        style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['live_viewers']) }}</span>
                </div>
                <span
                    style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Live
                    Viewers</span>
            </div>
            <div>
                <span
                    style="font-size:1.3rem;font-weight:900;color:#fff;display:block;">{{ number_format($stats['total_views']) }}</span>
                <span
                    style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;letter-spacing:.12em;">Total
                    Views</span>
            </div>
        </div>


    </div>

    {{-- Live Activity --}}
    <div class="activity-grid">
        <div>
            <h3
                style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.18em;margin:0 0 .75rem;padding-left:.5rem;border-left:2px solid var(--color-gh-accent);">
                Recent Discoveries</h3>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                @foreach ($recentlyAddedLinks->take(4) as $link)
                    <a href="{{ route('link.show', $link->slug) }}"
                        style="display:flex;justify-content:space-between;align-items:center;text-decoration:none;font-size:.78rem;color:var(--color-gh-text);">
                        <span
                            style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;">{{ $link->title }}</span>
                        <span
                            style="font-size:.65rem;color:var(--color-gh-dim);flex-shrink:0;margin-left:.5rem;">{{ $link->created_at->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <div>
            <h3
                style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.18em;margin:0 0 .75rem;padding-left:.5rem;border-left:2px solid #4ade80;">
                Recent Registrations</h3>
            @if($recentlyRegisteredUser)
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div
                        style="width:2rem;height:2rem;border-radius:.35rem;background:rgba(88,166,255,.1);display:flex;align-items:center;justify-content:center;color:var(--color-gh-accent);font-size:.65rem;font-weight:800;flex-shrink:0;">
                        {{ substr($recentlyRegisteredUser->username, 0, 1) }}
                    </div>
                    <div>
                        <span
                            style="font-size:.78rem;font-weight:700;color:#fff;display:block;">{{ $recentlyRegisteredUser->username }}</span>
                        <span style="font-size:.65rem;color:var(--color-gh-dim);">Registered
                            {{ $recentlyRegisteredUser->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-app.layouts>