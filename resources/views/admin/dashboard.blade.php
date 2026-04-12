<x-app.layouts title="Admin Dashboard">

    @include('admin._nav')

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:1.5rem;">
        <div class="admin-header">
            <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.3rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.12em;">Admin Control</span>
            </div>
            <h1>Network Oversight</h1>
            <p>Global synchronization and moderation of the Hidden Line backbone.</p>
        </div>
        <form action="{{ route('admin.crawler.crawl-all') }}" method="POST">
            @csrf
            <button type="submit" style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;border:none;cursor:pointer;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                Execute Global Crawl
            </button>
        </form>
    </div>

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.6rem;margin-bottom:1.5rem;">
        @foreach([
            ['Total Nodes', number_format($stats['total_links'])],
            ['Active Sync', number_format($stats['active_links'])],
            ['Directory', number_format($stats['registered_links'])],
            ['Scraped', number_format($stats['anonymous_links'])],
            ['Pending Ads', number_format($stats['pending_ads'])],
            ['Checks/24h', number_format($stats['recent_checks'])]
        ] as $stat)
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.85rem 1rem;">
                <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;">{{ $stat[0] }}</span>
                <span style="font-size:1.5rem;font-weight:900;color:#fff;line-height:1;">{{ $stat[1] }}</span>
            </div>
        @endforeach
    </div>

    {{-- Two Column Layout --}}
    <div style="display:grid;grid-template-columns:1fr;gap:1rem;">
        {{-- Activity Stream --}}
        <div class="panel">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Real-time Stream
            </div>
            <div style="max-height:480px;overflow-y:auto;">
                @forelse($recentLinks as $link)
                    <div style="padding:.7rem 1rem;border-bottom:1px solid rgba(48,54,61,.35);">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-bottom:.2rem;">
                            <span style="font-size:.8rem;font-weight:700;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $link->title }}</span>
                            <span class="status-badge {{ $link->user_id ? 'sb-active' : 'sb-unknown' }}">
                                {{ $link->user_id ? 'Auth' : 'Crawler' }}
                            </span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                            <span style="font-size:.6rem;color:var(--color-gh-dim);font-family:monospace;opacity:.6;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:70%;">{{ $link->url }}</span>
                            <span style="font-size:.55rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;white-space:nowrap;">{{ $link->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        <p>Stream Static</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sidebar panels --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
        {{-- Core Operations --}}
        <div class="panel">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6M9 13h6M9 17h4"/></svg>
                Core Ops
            </div>
            <div style="padding:.75rem 1rem;">
                @foreach([
                    ['Crawler Cluster', 'Nominal', '#4ade80'],
                    ['Tor Relay Node', 'Active', '#4ade80'],
                    ['MariaDB Mainframe', 'Optimal', '#58a6ff'],
                    ['Cache Layer', 'Synced', '#a78bfa']
                ] as $svc)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:.45rem 0;{{ !$loop->last ? 'border-bottom:1px solid rgba(48,54,61,.3);' : '' }}">
                        <div style="display:flex;align-items:center;">
                            <div style="width:5px;height:5px;border-radius:50%;background:{{ $svc[2] }};margin-right:.5rem;"></div>
                            <span style="font-size:.75rem;font-weight:600;color:var(--color-gh-dim);">{{ $svc[0] }}</span>
                        </div>
                        <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;color:#fff;padding:.15rem .4rem;border-radius:.25rem;border:1px solid var(--color-gh-border);">{{ $svc[1] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="panel">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Quick Actions
            </div>
            <div style="padding:.75rem 1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
                    <a href="{{ route('admin.ads.create') }}" style="display:flex;flex-direction:column;align-items:center;gap:.35rem;padding:.75rem .5rem;border:1px solid var(--color-gh-border);border-radius:.5rem;text-decoration:none;color:var(--color-gh-dim);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M12 8v8M8 12h8"/></svg>
                        <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;">New Ad</span>
                    </a>
                    <a href="{{ route('admin.blacklist') }}" style="display:flex;flex-direction:column;align-items:center;gap:.35rem;padding:.75rem .5rem;border:1px solid var(--color-gh-border);border-radius:.5rem;text-decoration:none;color:var(--color-gh-dim);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9.5 9.5l5 5M14.5 9.5l-5 5"/></svg>
                        <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;">Ban Node</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-app.layouts>