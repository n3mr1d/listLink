<x-app.layouts title="User Dashboard">

    <style>
        .dash-header{margin-bottom:1.5rem;}
        .dash-header h1{font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .25rem;letter-spacing:-.02em;}
        .dash-header p{font-size:.75rem;color:var(--color-gh-dim);margin:0;}
        .dash-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.6rem;margin-bottom:1.5rem;}
        .dash-stat{border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.85rem 1rem;display:flex;align-items:center;gap:.75rem;}
        .dash-stat .icon{width:36px;height:36px;border-radius:.5rem;display:flex;align-items:center;justify-content:center;border:1px solid var(--color-gh-border);flex-shrink:0;}
        .dash-stat .label{font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.2rem;}
        .dash-stat .value{font-size:1.35rem;font-weight:900;color:#fff;line-height:1;}
        .dash-panel{border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;margin-bottom:1.5rem;}
        .dash-panel-head{padding:.7rem 1rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;justify-content:space-between;gap:.4rem;}
        .dash-panel-head .title{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;display:flex;align-items:center;gap:.4rem;}
        .dash-panel-head svg{color:var(--color-gh-accent);flex-shrink:0;}
        .d-table{width:100%;border-collapse:collapse;}
        .d-table th{padding:.55rem 1rem;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);border-bottom:1px solid var(--color-gh-border);text-align:left;white-space:nowrap;}
        .d-table td{padding:.65rem 1rem;border-bottom:1px solid rgba(48,54,61,.35);vertical-align:middle;}
        .d-table tr:last-child td{border-bottom:none;}
        .d-table tr:hover td{background:rgba(255,255,255,.015);}
        .d-status{display:inline-flex;align-items:center;gap:.3rem;font-size:.55rem;font-weight:800;text-transform:uppercase;padding:.15rem .45rem;border-radius:.3rem;border:1px solid;}
        .d-online{color:#4ade80;border-color:rgba(74,222,128,.25);}
        .d-offline{color:#f87171;border-color:rgba(248,113,113,.25);}
        .d-timeout{color:#fb923c;border-color:rgba(251,146,60,.25);}
        .d-unknown{color:var(--color-gh-dim);border-color:var(--color-gh-border);}
        .d-pending{color:#fb923c;border-color:rgba(251,146,60,.25);}
        .d-checking{color:var(--color-gh-accent);border-color:rgba(88,166,255,.25);}
        .d-empty{padding:3rem 1rem;text-align:center;color:var(--color-gh-dim);opacity:.4;}
        .d-empty svg{display:block;margin:0 auto .5rem;}
        .d-empty p{font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;margin:0;}
        .d-empty .sub{font-size:.65rem;font-weight:500;opacity:.7;margin-top:.3rem;text-transform:none;letter-spacing:0;}
        @media(max-width:640px){
            .d-table th:nth-child(n+3),.d-table td:nth-child(n+3){display:none;}
            .hide-sm{display:none!important;}
        }
    </style>

    {{-- Header --}}
    <div class="dash-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.3rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.12em;">Your Dashboard</span>
            </div>
            <h1>Command Center</h1>
            <p>Monitoring and managing your broadcasted nodes on the network.</p>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
            <a href="{{ route('dashboard.ads') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .85rem;border:1px solid var(--color-gh-border);border-radius:.4rem;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--color-gh-dim);text-decoration:none;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                Advertiser Hub
            </a>
            <a href="{{ route('submit.create') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .85rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;text-decoration:none;border:none;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Deploy Node
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="dash-stats">
        <div class="dash-stat">
            <div class="icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
            </div>
            <div>
                <span class="label">Total Submissions</span>
                <span class="value">{{ $links->total() }}</span>
            </div>
        </div>
        <div class="dash-stat">
            <div class="icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <span class="label">Online Sync</span>
                <span class="value">{{ $links->where('uptime_status', \App\Enum\UptimeStatus::ONLINE)->count() }}</span>
            </div>
        </div>
        <div class="dash-stat">
            <div class="icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-sponsored)" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
            </div>
            <div>
                <span class="label">Active Ad Units</span>
                <span class="value">{{ $ads->where('status', 'active')->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Node Registry Table --}}
    <div class="dash-panel">
        <div class="dash-panel-head">
            <div class="title">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><circle cx="6" cy="6" r="1"/><circle cx="6" cy="18" r="1"/></svg>
                Node Registry
            </div>
            <div style="display:flex;align-items:center;gap:.3rem;">
                <div style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></div>
                <span style="font-size:.55rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;">Live</span>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="d-table">
                <thead>
                    <tr>
                        <th>Identity</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th style="text-align:right;">Last Check</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                        <tr>
                            <td>
                                <a href="{{ route('link.show', $link->slug) }}" style="font-size:.78rem;font-weight:700;color:#fff;text-decoration:none;">{{ $link->title }}</a>
                                <div style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.55;margin-top:.1rem;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Str::limit($link->url, 50) }}</div>
                            </td>
                            <td>
                                <span style="font-size:.55rem;font-weight:700;color:var(--color-gh-accent);border:1px solid rgba(88,166,255,.15);padding:.1rem .35rem;border-radius:.25rem;text-transform:uppercase;">{{ $link->category->label() }}</span>
                            </td>
                            <td>
                                @php
                                    $cls = match($link->uptime_status) {
                                        \App\Enum\UptimeStatus::ONLINE => 'd-online',
                                        \App\Enum\UptimeStatus::OFFLINE => 'd-offline',
                                        \App\Enum\UptimeStatus::TIMEOUT => 'd-timeout',
                                        default => 'd-unknown',
                                    };
                                @endphp
                                <span class="d-status {{ $cls }}">
                                    <span style="width:5px;height:5px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                                    {{ $link->uptime_status->label() }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <span style="font-size:.6rem;font-weight:700;color:var(--color-gh-dim);">{{ $link->last_check ? $link->last_check->diffForHumans() : 'Standby' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="d-empty">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                                    <p>No active signals detected</p>
                                    <p class="sub">Deploy your first node to begin monitoring</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($links->hasPages())
            <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                {{ $links->links('pagination.simple') }}
            </div>
        @endif
    </div>

</x-app.layouts>