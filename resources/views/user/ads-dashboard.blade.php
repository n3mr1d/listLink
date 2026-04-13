<x-app.layouts title="Advertiser Analytics Dashboard - {{ config('app.name') }}">

    <style>
        .ad-stat{border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.85rem 1rem;}
        .ad-stat .label{font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;}
        .ad-stat .value{font-size:1.45rem;font-weight:900;line-height:1;}
        .ad-progress{height:3px;background:var(--color-gh-border);border-radius:9px;overflow:hidden;margin-top:.55rem;}
        .ad-progress-fill{height:100%;background:var(--color-gh-accent);border-radius:9px;}
        .ad-panel{border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;margin-bottom:1rem;}
        .ad-panel-head{padding:.7rem 1rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;gap:.4rem;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;}
        .ad-panel-head svg{color:var(--color-gh-accent);flex-shrink:0;}
        .ad-table{width:100%;border-collapse:collapse;}
        .ad-table th{padding:.55rem 1rem;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);border-bottom:1px solid var(--color-gh-border);text-align:left;white-space:nowrap;}
        .ad-table td{padding:.65rem 1rem;border-bottom:1px solid rgba(48,54,61,.35);vertical-align:middle;}
        .ad-table tr:last-child td{border-bottom:none;}
        .ad-badge{display:inline-flex;align-items:center;padding:.15rem .4rem;border-radius:.3rem;font-size:.55rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase;}
        .ad-badge-blue{border:1px solid rgba(88,166,255,.25);color:#58a6ff;}
        .ad-dot{display:inline-block;width:5px;height:5px;border-radius:50%;margin-right:.3rem;}
        @keyframes dot-pulse{0%,100%{opacity:1}50%{opacity:.3}}
        .ad-dot-pulse{animation:dot-pulse 1.6s ease-in-out infinite;}
        @media(max-width:640px){
            .ad-table th:nth-child(n+3),.ad-table td:nth-child(n+3){display:none;}
            .stats-grid{grid-template-columns:1fr 1fr!important;}
        }
        @media(min-width:768px){
            .mobile-cards{display:none!important;}
            .desktop-table{display:block!important;}
        }
        @media(max-width:767px){
            .mobile-cards{display:block!important;}
            .desktop-table{display:none!important;}
        }
    </style>

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:1.5rem;">
        <div>
            <div style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--color-gh-dim);margin-bottom:.35rem;">
                <a href="{{ route('dashboard') }}" style="color:inherit;text-decoration:none;">Nodes</a>
                <span style="margin:0 .3rem;opacity:.5;">/</span>
                <span style="color:#fff;">Advertiser Hub</span>
            </div>
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .2rem;letter-spacing:-.02em;">Campaign Analytics</h1>
            <p style="font-size:.72rem;color:var(--color-gh-dim);margin:0;">Real-time performance metrics for your campaigns.</p>
        </div>
        <a href="{{ route('advertise.create') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.5rem 1rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;font-size:.65rem;font-weight:900;text-decoration:none;text-transform:uppercase;letter-spacing:.06em;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14"/></svg>
            New Campaign
        </a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:.6rem;margin-bottom:1.5rem;">
        <div class="ad-stat">
            <span class="label">Total Exposure</span>
            <span class="value" style="color:#fff;">{{ number_format($totalImpressions) }}</span>
            <span style="font-size:.6rem;color:var(--color-gh-dim);margin-left:.15rem;">hits</span>
            <div class="ad-progress"><div class="ad-progress-fill" style="width:70%;"></div></div>
        </div>
        <div class="ad-stat">
            <span class="label">Link Interaction</span>
            <span class="value" style="color:#4ade80;">{{ number_format($totalClicks) }}</span>
            <span style="font-size:.6rem;color:var(--color-gh-dim);margin-left:.15rem;">clicks</span>
            <div style="font-size:.55rem;font-weight:800;color:#4ade80;margin-top:.55rem;text-transform:uppercase;">CTR {{ $ctr }}%</div>
        </div>
        <div class="ad-stat">
            <span class="label">Active Nodes</span>
            <span class="value" style="color:#fff;">{{ $ads->where('status', 'active')->count() }}</span>
            <span style="font-size:.6rem;color:var(--color-gh-dim);margin-left:.15rem;">campaigns</span>
            <div style="font-size:.55rem;font-weight:700;color:var(--color-gh-dim);margin-top:.55rem;text-transform:uppercase;">{{ $ads->count() }} total</div>
        </div>
        <div class="ad-stat" style="text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <span class="label">Efficiency</span>
            <div style="display:inline-flex;align-items:center;justify-content:center;width:2.5rem;height:2.5rem;border-radius:50%;border:2px solid var(--color-gh-accent);margin-top:.25rem;">
                <span style="font-size:.9rem;font-weight:900;color:#fff;">8.4</span>
            </div>
        </div>
    </div>

    {{-- Performance Chart --}}
    <div class="ad-panel">
        <div class="ad-panel-head">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Traffic Propagation
            <span style="margin-left:auto;font-size:.55rem;font-weight:800;color:var(--color-gh-dim);border:1px solid var(--color-gh-border);padding:.15rem .4rem;border-radius:.25rem;">Last 30 Days</span>
        </div>
        <div style="padding:1rem;height:220px;position:relative;">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

    {{-- Monthly Growth Chart --}}
    <div class="ad-panel">
        <div class="ad-panel-head">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            Monthly Performance Trends
            <span style="margin-left:auto;font-size:.55rem;font-weight:800;color:var(--color-gh-dim);border:1px solid var(--color-gh-border);padding:.15rem .4rem;border-radius:.25rem;">Last 12 Months</span>
        </div>
        <div style="padding:1rem;height:220px;position:relative;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    {{-- Distribution + Tip --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem;">
        <div class="ad-panel" style="margin-bottom:0;">
            <div class="ad-panel-head">Distribution</div>
            <div style="padding:.75rem 1rem;">
                @foreach($placementStats as $pStat)
                    <div style="margin-bottom:.65rem;">
                        <div style="display:flex;justify-content:space-between;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.2rem;">
                            <span style="color:var(--color-gh-dim);">{{ $pStat->placement->label() }}</span>
                            <span style="color:#fff;">{{ number_format($pStat->total_impressions) }}</span>
                        </div>
                        <div class="ad-progress"><div class="ad-progress-fill" style="width:{{ $totalImpressions > 0 ? round(($pStat->total_impressions / $totalImpressions) * 100) : 0 }}%;"></div></div>
                    </div>
                @endforeach
            </div>
        </div>
        <div style="border:1px solid rgba(88,166,255,.2);border-radius:.6rem;padding:1rem;background:rgba(88,166,255,.02);">
            <span style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;display:block;margin-bottom:.4rem;">Optimization Tip</span>
            <p style="font-size:.68rem;color:var(--color-gh-dim);line-height:1.65;margin:0;">
                Protocol nodes in the <span style="color:var(--color-gh-accent);">HEADER</span> placement yield <strong style="color:#fff;">3.4×</strong> higher engagement than sidebar clusters. Consider reallocating resources.
            </p>
        </div>
    </div>

    {{-- Campaign Registry --}}
    <div class="ad-panel">
        <div class="ad-panel-head">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            Deployment Log
        </div>

        @forelse ($ads as $ad)
            @php
                $adImpressions = $ad->stats->sum('impressions');
                $adClicks = $ad->stats->sum('clicks');
                $adCtr = $adImpressions > 0 ? round(($adClicks / $adImpressions) * 100, 2) : 0;
                $isActive = $ad->status === 'active';
            @endphp

            {{-- Mobile card --}}
            <div class="mobile-cards" style="padding:.75rem 1rem;border-bottom:1px solid rgba(48,54,61,.35);">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;">
                    <div style="min-width:0;">
                        <div style="font-size:.78rem;font-weight:700;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ad->title }}</div>
                        <div style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.55;margin-top:.1rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Str::limit($ad->url, 32) }}</div>
                    </div>
                    <div style="display:flex;gap:.25rem;flex-shrink:0;">
                        <a href="{{ route('advertise.create', ['edit' => $ad->id]) }}" title="Edit" style="color:var(--color-gh-dim);padding:.3rem;display:inline-flex;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        @if(!$isActive)
                        <a href="{{ route('payment.show', $ad->id) }}" title="Billing" style="color:#eab308;padding:.3rem;display:inline-flex;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.5rem;align-items:center;">
                    <span class="ad-badge ad-badge-blue">{{ $ad->placement->label() }}</span>
                    <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;color:{{ $isActive ? '#4ade80' : '#f87171' }};">
                        <span class="ad-dot {{ $isActive ? 'ad-dot-pulse' : '' }}" style="background:{{ $isActive ? '#4ade80' : '#f87171' }};"></span>{{ strtoupper($ad->status) }}
                    </span>
                    <span style="font-size:.6rem;color:var(--color-gh-dim);margin-left:auto;">
                        {{ number_format($adImpressions) }} IMP · <span style="color:var(--color-gh-accent);">{{ number_format($adClicks) }} CLK</span> · CTR {{ $adCtr }}%
                    </span>
                </div>
            </div>

        @empty
            <div class="mobile-cards" style="padding:2rem 1rem;text-align:center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="1.5" style="opacity:.35;margin:0 auto .5rem;display:block;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
                <p style="font-size:.75rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.08em;">No Active Deployments</p>
                <p style="font-size:.65rem;color:var(--color-gh-dim);margin:.3rem 0 .75rem;">Launch a new campaign to begin tracking.</p>
                <a href="{{ route('advertise.create') }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.45rem .85rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;font-size:.65rem;font-weight:900;text-decoration:none;text-transform:uppercase;">+ Launch Campaign</a>
            </div>
        @endforelse

        {{-- Desktop table --}}
        <div class="desktop-table" style="display:none;overflow-x:auto;">
            <table class="ad-table">
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
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    @if($ad->banner_path)
                                        <img src="{{ asset('storage/' . $ad->banner_path) }}" style="width:40px;height:22px;object-fit:cover;border-radius:.25rem;border:1px solid var(--color-gh-border);opacity:.7;flex-shrink:0;" loading="lazy">
                                    @else
                                        <div style="width:40px;height:22px;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.25rem;display:flex;align-items:center;justify-content:center;font-size:.45rem;font-weight:800;color:var(--color-gh-dim);flex-shrink:0;">TXT</div>
                                    @endif
                                    <div>
                                        <div style="font-size:.78rem;font-weight:700;color:#fff;">{{ $ad->title }}</div>
                                        <div style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.55;margin-top:.1rem;">{{ Str::limit($ad->url, 36) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="ad-badge ad-badge-blue">{{ $ad->placement->label() }}</span></td>
                            <td>
                                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;color:{{ $isActive ? '#4ade80' : '#f87171' }};">
                                    <span class="ad-dot {{ $isActive ? 'ad-dot-pulse' : '' }}" style="background:{{ $isActive ? '#4ade80' : '#f87171' }};"></span>{{ strtoupper($ad->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size:.65rem;font-weight:800;color:rgba(255,255,255,.8);display:flex;gap:.5rem;">
                                    <span>{{ number_format($adImpressions) }} <span style="color:var(--color-gh-dim);">IMP</span></span>
                                    <span style="color:var(--color-gh-accent);">{{ number_format($adClicks) }} <span style="color:var(--color-gh-dim);">CLK</span></span>
                                </div>
                                <div style="font-size:.55rem;color:var(--color-gh-dim);margin-top:.15rem;">CTR: {{ $adCtr }}%</div>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:.25rem;align-items:center;">
                                    <a href="{{ route('advertise.create', ['edit' => $ad->id]) }}" title="Edit" style="color:var(--color-gh-dim);padding:.3rem;display:inline-flex;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    @if(!$isActive)
                                    <a href="{{ route('payment.show', $ad->id) }}" title="Billing" style="color:#eab308;padding:.3rem;display:inline-flex;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:2.5rem 1rem;text-align:center;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="1.5" style="opacity:.3;display:block;margin:0 auto .5rem;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
                                <p style="font-size:.75rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.08em;margin:0 0 .2rem;">No Active Deployments</p>
                                <p style="font-size:.65rem;color:var(--color-gh-dim);margin:0 0 .75rem;">Launch a new campaign to begin tracking.</p>
                                <a href="{{ route('advertise.create') }}" style="display:inline-flex;align-items:center;gap:.3rem;padding:.45rem .85rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;font-size:.65rem;font-weight:900;text-decoration:none;text-transform:uppercase;">+ Launch Campaign</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart.js — loaded locally, animation disabled for Tor --}}
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.animation = false;
            
            // Performance Chart (Daily)
            var ctx = document.getElementById('performanceChart');
            if (ctx) {
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
            }

            // Monthly Chart (Last 12 Months)
            var monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($monthlyLabels),
                        datasets: [
                            {
                                label: 'Impressions',
                                data: @json($monthlyImpressions),
                                backgroundColor: 'rgba(88,166,255,.5)',
                                borderColor: '#58a6ff',
                                borderWidth: 1,
                                borderRadius: 4,
                            },
                            {
                                label: 'Clicks',
                                data: @json($monthlyClicks),
                                backgroundColor: 'rgba(74,222,128,.5)',
                                borderColor: '#4ade80',
                                borderWidth: 1,
                                borderRadius: 4,
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
            }
        });
    </script>

</x-app.layouts>
