<x-app.layouts title="Advertiser Analytics Dashboard - Hidden Line">
    <div class="max-w-[1200px] mx-auto px-4 py-8">
        {{-- ═══ Breadcrumbs & Header ═══ --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <div class="flex items-center gap-2 text-gh-dim text-[10px] font-black uppercase tracking-widest mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-gh-accent no-underline transition-colors">Nodes</a>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    <span class="text-white">Advertiser Hub</span>
                </div>
                <h1 class="text-3xl font-black text-white italic tracking-tighter uppercase leading-none">Campaign Analytics</h1>
                <p class="text-gh-dim text-sm font-medium mt-2">Real-time performance metrics for your broadcasted protocols.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('advertise.create') }}" class="bg-gh-accent text-gh-bg px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-300 transition-all shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> New Campaign
                </a>
            </div>
        </div>

        {{-- ═══ Summary Matrix ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-eye text-4xl text-gh-accent"></i>
                </div>
                <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest mb-4 block">Total Exposure</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white leading-none">{{ number_format($totalImpressions) }}</span>
                    <span class="text-xs font-bold text-gh-dim">hits</span>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <div class="w-full h-1 bg-gh-bg rounded-full overflow-hidden">
                        <div class="h-full bg-gh-accent rounded-full" style="width: 70%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-mouse-pointer text-4xl text-green-500"></i>
                </div>
                <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest mb-4 block">Link Interaction</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-green-400 leading-none">{{ number_format($totalClicks) }}</span>
                    <span class="text-xs font-bold text-gh-dim">clicks</span>
                </div>
                <div class="mt-4 text-[10px] font-bold text-green-500/80 uppercase tracking-tighter italic">Converting at {{ $ctr }}%</div>
            </div>

            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-bolt text-4xl text-yellow-500"></i>
                </div>
                <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest mb-4 block">Active Nodes</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white leading-none">{{ $ads->where('status', 'active')->count() }}</span>
                    <span class="text-xs font-bold text-gh-dim">campaigns</span>
                </div>
                <div class="mt-4 text-[10px] font-bold text-gh-dim uppercase tracking-tighter italic">{{ $ads->count() }} total registered</div>
            </div>

            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-xl flex flex-col justify-center">
                <div class="text-center">
                    <span class="text-[10px] font-black text-gh-dim uppercase tracking-widest mb-3 block">Efficiency Score</span>
                    <div class="inline-flex items-center justify-center p-4 rounded-full border-4 border-gh-accent shadow-inner">
                        <span class="text-2xl font-black text-white">8.4</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8 mb-12">
            {{-- ═══ Core Performance Graph ═══ --}}
            <div class="bg-gh-bar-bg border border-gh-border rounded-3xl p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-3">
                        <i class="fa-solid fa-chart-line text-gh-accent"></i> Traffic Propagation
                    </h3>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 rounded-md bg-gh-bg border border-gh-border text-[9px] font-black text-gh-dim uppercase tracking-tighter">Last 30 Days</span>
                    </div>
                </div>
                <div class="h-[350px] w-full relative">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            {{-- ═══ Placement Distribution ═══ --}}
            <div class="flex flex-col gap-8">
                <div class="bg-gh-bar-bg border border-gh-border rounded-3xl p-8 shadow-2xl">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-3 mb-8">
                        <i class="fa-solid fa-layer-group text-gh-accent"></i> Distribution
                    </h3>
                    <div class="space-y-6">
                        @foreach($placementStats as $pStat)
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-gh-dim">{{ $pStat->placement->label() }}</span>
                                    <span class="text-white">{{ number_format($pStat->total_impressions) }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-gh-bg rounded-full">
                                    <div class="h-full bg-gh-accent rounded-full" style="width: {{ $totalImpressions > 0 ? ($pStat->total_impressions / $totalImpressions) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-8 bg-gradient-to-br from-gh-accent/10 to-transparent border border-gh-accent/20 rounded-3xl shadow-xl">
                    <h4 class="text-xs font-black text-white uppercase tracking-widest mb-2 italic">Optimization Tip</h4>
                    <p class="text-[10px] text-gh-dim leading-loose m-0">Protocol nodes in the <span class="text-gh-accent">HEADER</span> placement currently yield 3.4x higher engagement than sidebar clusters. Consider reallocating resources.</p>
                </div>
            </div>
        </div>

        {{-- ═══ Campaign Registry ═══ --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-3xl overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-gh-border bg-gh-bg/30">
                <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-list-check text-gh-accent"></i> Deployment Log
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gh-bg/50 border-b border-gh-border">
                            <th class="px-8 py-5 text-[10px] font-black text-gh-dim uppercase tracking-[0.2em]">Signature</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gh-dim uppercase tracking-[0.2em]">Placement</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gh-dim uppercase tracking-[0.2em]">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gh-dim uppercase tracking-[0.2em]">Performance</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gh-dim uppercase tracking-[0.2em] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gh-border/30">
                        @forelse ($ads as $ad)
                            <tr class="hover:bg-gh-bg/40 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        @if($ad->banner_path)
                                            <img src="{{ asset('storage/' . $ad->banner_path) }}" class="w-12 h-6 object-cover rounded border border-gh-border opacity-70">
                                        @else
                                            <div class="w-12 h-6 bg-gh-bg border border-gh-border rounded flex items-center justify-center text-[8px] font-black text-gh-dim uppercase">TEXT</div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-white">{{ $ad->title }}</span>
                                            <span class="text-[10px] font-mono text-gh-dim opacity-50">{{ Str::limit($ad->url, 30) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-2.5 py-1 rounded bg-blue-500/5 border border-blue-500/20 text-[9px] font-black text-blue-400 uppercase tracking-tighter">
                                        {{ $ad->placement->label() }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $ad->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                                        <span class="text-[10px] font-black uppercase tracking-widest {{ $ad->status === 'active' ? 'text-green-500' : 'text-red-500' }}">
                                            {{ strtoupper($ad->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php 
                                        $adImpressions = $ad->stats->sum('impressions');
                                        $adClicks = $ad->stats->sum('clicks');
                                        $adCtr = $adImpressions > 0 ? round(($adClicks / $adImpressions) * 100, 2) : 0;
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-4 text-[10px] font-black text-white/80">
                                            <span>{{ number_format($adImpressions) }} <span class="text-gh-dim">IMP</span></span>
                                            <span class="text-gh-accent">{{ number_format($adClicks) }} <span class="text-gh-dim">CLK</span></span>
                                        </div>
                                        <span class="text-[9px] font-bold text-gh-dim">CTR: {{ $adCtr }}%</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('advertise.create', ['edit' => $ad->id]) }}" class="p-2 text-gh-dim hover:text-white transition-colors" title="Modify Node">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        @if($ad->status !== 'active')
                                        <a href="{{ route('payment.show', $ad->id) }}" class="p-2 text-yellow-500 hover:text-yellow-400 transition-colors" title="Billing Center">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-full bg-gh-bg border border-gh-border border-dashed flex items-center justify-center text-gh-dim/30">
                                            <i class="fa-solid fa-bullhorn text-2xl"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-white font-black uppercase tracking-widest text-sm m-0">No Active Deployments</p>
                                            <p class="text-gh-dim text-[10px] font-medium uppercase tracking-[0.2em]">Launch a new protocol to begin exposure tracking.</p>
                                        </div>
                                        <a href="{{ route('advertise.create') }}" class="mt-4 px-8 py-3 bg-gh-accent text-gh-bg rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-white transition-all shadow-xl shadow-gh-accent/10">
                                            <i class="fa-solid fa-plus mr-2"></i> Launch Campaign
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart Logic --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            // Disable animations for Tor optimization as requested
            Chart.defaults.animation = false;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Impressions',
                            data: @json($impressions),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.1, // Minimal tension for crisp look
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#3b82f6',
                        },
                        {
                            label: 'Clicks',
                            data: @json($clicks),
                            borderColor: '#22c55e',
                            backgroundColor: 'transparent',
                            borderWidth: 3,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#22c55e',
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#161b22',
                            titleColor: '#8b949e',
                            bodyColor: '#ffffff',
                            borderColor: '#30363d',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            bodyFont: {
                                weight: 'bold',
                                size: 10
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#8b949e',
                                font: {
                                    size: 9,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(48, 54, 61, 0.5)'
                            },
                            ticks: {
                                color: '#8b949e',
                                font: {
                                    size: 9,
                                    weight: 'bold'
                                }
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                color: '#22c55e',
                                font: {
                                    size: 9,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app.layouts>
