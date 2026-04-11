<x-app.layouts title="Advertiser Analytics Dashboard - Hidden Line">

    {{-- Stylesheet: inline page-level styles for Tor load optimization --}}
    <style>
        .stat-card { border: 1px solid var(--color-gh-border); border-radius: .75rem; padding: 1rem 1.25rem; }
        .progress-bar { height: 3px; background: var(--color-gh-border); border-radius: 9px; overflow: hidden; }
        .progress-bar-fill { height: 100%; background: var(--color-gh-accent); border-radius: 9px; }
        .badge { display: inline-flex; align-items: center; padding: .2rem .55rem; border-radius: .35rem; font-size: .65rem; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
        .badge-blue  { border: 1px solid rgba(88,166,255,.25); color: #58a6ff; }
        .badge-green { color: #4ade80; }
        .badge-red   { color: #f87171; }
        /* Campaign card (mobile) */
        .campaign-card { border: 1px solid var(--color-gh-border); border-radius: .75rem; padding: 1rem; margin-bottom: .75rem; }
        .campaign-card:last-child { margin-bottom: 0; }
        /* Table (desktop) */
        .camp-table { width: 100%; border-collapse: collapse; }
        .camp-table th { padding: .6rem 1rem; font-size: .65rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; color: var(--color-gh-dim); border-bottom: 1px solid var(--color-gh-border); text-align: left; white-space: nowrap; }
        .camp-table td { padding: .85rem 1rem; border-bottom: 1px solid rgba(48,54,61,.4); vertical-align: middle; }
        .camp-table tr:last-child td { border-bottom: none; }
        /* Status dot */
        .dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; margin-right: 5px; }
        .dot-green { background: #4ade80; }
        .dot-red   { background: #f87171; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.35} }
        .dot-pulse { animation: pulse 1.6s ease-in-out infinite; }

        /* Responsive toggle */
        @media (min-width: 768px) {
            .mobile-cards  { display: none !important; }
            .desktop-table { display: block !important; }
        }
        @media (max-width: 767px) {
            .mobile-cards  { display: block !important; }
            .desktop-table { display: none !important; }
            .stats-grid    { grid-template-columns: 1fr 1fr !important; }
        }
    </style>

    <div style="max-width:1100px;margin:0 auto;padding:1.5rem 1rem 3rem;">

        {{-- ═══ Header ═══ --}}
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:2rem;">
            <div>
                <div style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.15em;color:var(--color-gh-dim);margin-bottom:.4rem;">
                    <a href="{{ route('dashboard') }}" style="color:inherit;text-decoration:none;">Nodes</a>
                    <span style="margin:0 .35rem;opacity:.5;">/</span>
                    <span style="color:#fff;">Advertiser Hub</span>
                </div>
                <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0 0 .25rem;letter-spacing:-.03em;line-height:1.1;">Campaign Analytics</h1>
                <p style="font-size:.75rem;color:var(--color-gh-dim);margin:0;">Real-time performance metrics for your broadcasted protocols.</p>
            </div>
            <a href="{{ route('advertise.create') }}"
               style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.1rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.5rem;font-size:.7rem;font-weight:900;text-decoration:none;text-transform:uppercase;letter-spacing:.08em;white-space:nowrap;margin-top:.25rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5v14M5 12h14"/></svg>
                New Campaign
            </a>
        </div>

        {{-- ═══ Stats Grid ═══ --}}
        <div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;margin-bottom:2rem;">

            {{-- Impressions --}}
            <div class="stat-card">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);display:block;margin-bottom:.5rem;">Total Exposure</span>
                <span style="font-size:1.6rem;font-weight:900;color:#fff;line-height:1;">{{ number_format($totalImpressions) }}</span>
                <span style="font-size:.65rem;color:var(--color-gh-dim);margin-left:.2rem;">hits</span>
                <div class="progress-bar" style="margin-top:.65rem;">
                    <div class="progress-bar-fill" style="width:70%;"></div>
                </div>
            </div>

            {{-- Clicks --}}
            <div class="stat-card">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);display:block;margin-bottom:.5rem;">Link Interaction</span>
                <span style="font-size:1.6rem;font-weight:900;color:#4ade80;line-height:1;">{{ number_format($totalClicks) }}</span>
                <span style="font-size:.65rem;color:var(--color-gh-dim);margin-left:.2rem;">clicks</span>
                <div style="font-size:.6rem;font-weight:800;color:#4ade80;margin-top:.65rem;opacity:.8;text-transform:uppercase;">CTR {{ $ctr }}%</div>
            </div>

            {{-- Active --}}
            <div class="stat-card">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);display:block;margin-bottom:.5rem;">Active Nodes</span>
                <span style="font-size:1.6rem;font-weight:900;color:#fff;line-height:1;">{{ $ads->where('status', 'active')->count() }}</span>
                <span style="font-size:.65rem;color:var(--color-gh-dim);margin-left:.2rem;">campaigns</span>
                <div style="font-size:.6rem;font-weight:700;color:var(--color-gh-dim);margin-top:.65rem;text-transform:uppercase;">{{ $ads->count() }} total</div>
            </div>

            {{-- Efficiency --}}
            <div class="stat-card" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);display:block;margin-bottom:.6rem;">Efficiency</span>
                <div style="display:inline-flex;align-items:center;justify-content:center;width:3rem;height:3rem;border-radius:50%;border:2px solid var(--color-gh-accent);">
                    <span style="font-size:1rem;font-weight:900;color:#fff;">8.4</span>
                </div>
            </div>

        </div>

        {{-- ═══ Graph + Distribution ═══ --}}
        <div style="display:grid;grid-template-columns:1fr;gap:.75rem;margin-bottom:2rem;">

            {{-- Performance Chart --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.75rem;padding:1.25rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <span style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;display:flex;align-items:center;gap:.4rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        Traffic Propagation
                    </span>
                    <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;border:1px solid var(--color-gh-border);padding:.2rem .5rem;border-radius:.3rem;">Last 30 Days</span>
                </div>
                <div style="height:220px;position:relative;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

        </div>

        {{-- ═══ Distribution + Tip (side-by-side on desktop) ═══ --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:2rem;">

            {{-- Placement Distribution --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.75rem;padding:1.25rem;">
                <span style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;display:block;margin-bottom:1rem;">
                    Distribution
                </span>
                <div style="display:flex;flex-direction:column;gap:.85rem;">
                    @foreach($placementStats as $pStat)
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;margin-bottom:.3rem;">
                                <span style="color:var(--color-gh-dim);">{{ $pStat->placement->label() }}</span>
                                <span style="color:#fff;">{{ number_format($pStat->total_impressions) }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width:{{ $totalImpressions > 0 ? round(($pStat->total_impressions / $totalImpressions) * 100) : 0 }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Optimization Tip --}}
            <div style="border:1px solid rgba(88,166,255,.2);border-radius:.75rem;padding:1.25rem;background:rgba(88,166,255,.04);">
                <span style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;display:block;margin-bottom:.5rem;">Optimization Tip</span>
                <p style="font-size:.72rem;color:var(--color-gh-dim);line-height:1.7;margin:0;">
                    Protocol nodes in the <span style="color:var(--color-gh-accent);">HEADER</span> placement currently yield <strong style="color:#fff;">3.4×</strong> higher engagement than sidebar clusters. Consider reallocating resources.
                </p>
            </div>

        </div>

        {{-- ═══ Campaign Registry ═══ --}}
        <div style="border:1px solid var(--color-gh-border);border-radius:.75rem;overflow:hidden;">

            {{-- Section Header --}}
            <div style="padding:.9rem 1.25rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;gap:.4rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <span style="font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#fff;">Deployment Log</span>
            </div>

            @forelse ($ads as $ad)
                @php
                    $adImpressions = $ad->stats->sum('impressions');
                    $adClicks = $ad->stats->sum('clicks');
                    $adCtr = $adImpressions > 0 ? round(($adClicks / $adImpressions) * 100, 2) : 0;
                    $isActive = $ad->status === 'active';
                @endphp

                {{-- ── Mobile card ── --}}
                <div class="mobile-cards campaign-card" style="border-radius:0;border-left:none;border-right:none;border-top:none;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;">
                        <div style="display:flex;align-items:center;gap:.65rem;flex:1;min-width:0;">
                            @if($ad->banner_path)
                                <img src="{{ asset('storage/' . $ad->banner_path) }}" style="width:40px;height:22px;object-fit:cover;border-radius:.3rem;border:1px solid var(--color-gh-border);opacity:.7;flex-shrink:0;">
                            @else
                                <div style="width:40px;height:22px;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.3rem;display:flex;align-items:center;justify-content:center;font-size:.5rem;font-weight:800;color:var(--color-gh-dim);flex-shrink:0;">TEXT</div>
                            @endif
                            <div style="min-width:0;">
                                <div style="font-size:.8rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ad->title }}</div>
                                <div style="font-size:.6rem;font-family:monospace;color:var(--color-gh-dim);opacity:.6;margin-top:.1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($ad->url, 32) }}</div>
                            </div>
                        </div>
                        {{-- Actions --}}
                        <div style="display:flex;gap:.35rem;align-items:center;flex-shrink:0;">
                            <a href="{{ route('advertise.create', ['edit' => $ad->id]) }}" title="Edit" style="color:var(--color-gh-dim);padding:.3rem;display:inline-flex;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            @if(!$isActive)
                            <a href="{{ route('payment.show', $ad->id) }}" title="Billing" style="color:#eab308;padding:.3rem;display:inline-flex;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.65rem;align-items:center;">
                        <span class="badge badge-blue">{{ $ad->placement->label() }}</span>
                        <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;color:{{ $isActive ? '#4ade80' : '#f87171' }};">
                            <span class="dot {{ $isActive ? 'dot-green dot-pulse' : 'dot-red' }}"></span>{{ strtoupper($ad->status) }}
                        </span>
                        <span style="font-size:.65rem;color:var(--color-gh-dim);margin-left:auto;">
                            {{ number_format($adImpressions) }} IMP · <span style="color:var(--color-gh-accent);">{{ number_format($adClicks) }} CLK</span> · CTR {{ $adCtr }}%
                        </span>
                    </div>
                </div>

            @empty
                {{-- Empty mobile --}}
                <div class="mobile-cards" style="padding:2.5rem 1rem;text-align:center;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="1.5" style="opacity:.35;margin-bottom:.75rem;"><path d="M22 16.92V19a2 2 0 01-2.18 2A19.79 19.79 0 013.19 5.18 2 2 0 015.15 3h2.07a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 10.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 17z"/></svg>
                    <p style="font-size:.8rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.3rem;">No Active Deployments</p>
                    <p style="font-size:.7rem;color:var(--color-gh-dim);margin-bottom:1rem;">Launch a new protocol to begin exposure tracking.</p>
                    <a href="{{ route('advertise.create') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.55rem 1rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.45rem;font-size:.7rem;font-weight:900;text-decoration:none;text-transform:uppercase;letter-spacing:.08em;">
                        + Launch Campaign
                    </a>
                </div>
            @endforelse

            {{-- ── Desktop table ── --}}
            <div class="desktop-table" style="display:none;overflow-x:auto;">
                <table class="camp-table">
                    <thead>
                        <tr>
                            <th>Signature</th>
                            <th>Placement</th>
                            <th>Status</th>
                            <th>Performance</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ads as $ad)
                            @php
                                $adImpressions = $ad->stats->sum('impressions');
                                $adClicks = $ad->stats->sum('clicks');
                                $adCtr = $adImpressions > 0 ? round(($adClicks / $adImpressions) * 100, 2) : 0;
                                $isActive = $ad->status === 'active';
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.65rem;">
                                        @if($ad->banner_path)
                                            <img src="{{ asset('storage/' . $ad->banner_path) }}" style="width:44px;height:24px;object-fit:cover;border-radius:.3rem;border:1px solid var(--color-gh-border);opacity:.7;flex-shrink:0;">
                                        @else
                                            <div style="width:44px;height:24px;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.3rem;display:flex;align-items:center;justify-content:center;font-size:.5rem;font-weight:800;color:var(--color-gh-dim);flex-shrink:0;">TEXT</div>
                                        @endif
                                        <div>
                                            <div style="font-size:.82rem;font-weight:700;color:#fff;">{{ $ad->title }}</div>
                                            <div style="font-size:.6rem;font-family:monospace;color:var(--color-gh-dim);opacity:.55;margin-top:.1rem;">{{ Str::limit($ad->url, 36) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-blue">{{ $ad->placement->label() }}</span></td>
                                <td>
                                    <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;color:{{ $isActive ? '#4ade80' : '#f87171' }};">
                                        <span class="dot {{ $isActive ? 'dot-green dot-pulse' : 'dot-red' }}"></span>{{ strtoupper($ad->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:.7rem;font-weight:800;color:rgba(255,255,255,.8);display:flex;gap:.75rem;align-items:center;">
                                        <span>{{ number_format($adImpressions) }} <span style="color:var(--color-gh-dim);">IMP</span></span>
                                        <span style="color:var(--color-gh-accent);">{{ number_format($adClicks) }} <span style="color:var(--color-gh-dim);">CLK</span></span>
                                    </div>
                                    <div style="font-size:.6rem;color:var(--color-gh-dim);margin-top:.2rem;">CTR: {{ $adCtr }}%</div>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex;gap:.25rem;align-items:center;">
                                        <a href="{{ route('advertise.create', ['edit' => $ad->id]) }}" title="Modify Node" style="color:var(--color-gh-dim);padding:.35rem;display:inline-flex;border-radius:.3rem;transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--color-gh-dim)'">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        @if(!$isActive)
                                        <a href="{{ route('payment.show', $ad->id) }}" title="Billing Center" style="color:#eab308;padding:.35rem;display:inline-flex;border-radius:.3rem;transition:color .15s;" onmouseover="this.style.color='#fde047'" onmouseout="this.style.color='#eab308'">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:3rem 1rem;text-align:center;">
                                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="1.5" style="opacity:.3;display:block;margin:0 auto .75rem;"><path d="M18 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2z"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
                                    <p style="font-size:.8rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.1em;margin:0 0 .3rem;">No Active Deployments</p>
                                    <p style="font-size:.7rem;color:var(--color-gh-dim);margin-bottom:1rem;">Launch a new protocol to begin exposure tracking.</p>
                                    <a href="{{ route('advertise.create') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.55rem 1rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.45rem;font-size:.7rem;font-weight:900;text-decoration:none;text-transform:uppercase;letter-spacing:.08em;">
                                        + Launch Campaign
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>{{-- end Campaign Registry --}}

    </div>

    {{-- Chart.js — loaded only on this page --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('performanceChart');
            if (!ctx) return;
            Chart.defaults.animation = false;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Impressions',
                            data: @json($impressions),
                            borderColor: '#58a6ff',
                            backgroundColor: 'rgba(88,166,255,.06)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointHoverBackgroundColor: '#58a6ff',
                        },
                        {
                            label: 'Clicks',
                            data: @json($clicks),
                            borderColor: '#4ade80',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointHoverBackgroundColor: '#4ade80',
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#161b22',
                            titleColor: '#7d8590',
                            bodyColor: '#fff',
                            borderColor: '#30363d',
                            borderWidth: 1,
                            padding: 10,
                            bodyFont: { weight: 'bold', size: 10 }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#7d8590', font: { size: 9, weight: 'bold' } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(48,54,61,.4)' },
                            ticks: { color: '#7d8590', font: { size: 9, weight: 'bold' } }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { color: '#4ade80', font: { size: 9, weight: 'bold' } }
                        }
                    }
                }
            });
        });
    </script>

</x-app.layouts>
