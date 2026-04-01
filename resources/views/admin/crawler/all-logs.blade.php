<x-app.layouts title="All Crawl Logs">

    <div class="page-header">
        <h1>📜 Crawl Activity Log</h1>
        <p>Full audit trail of all crawl attempts across all links.</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.crawler.index') }}">Crawler</a>
        <a href="{{ route('admin.crawler.logs') }}" class="active">Crawl Logs</a>
    </nav>

    {{-- Aggregate Stats --}}
    <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-value" style="font-size:1.1rem;">{{ number_format($logStats['total']) }}</div>
            <div class="stat-label">Total Logs</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size:1.1rem;color:var(--accent-green);">{{ number_format($logStats['success']) }}</div>
            <div class="stat-label">Success</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size:1.1rem;color:#f85149;">{{ number_format($logStats['failed']) }}</div>
            <div class="stat-label">Failed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size:1.1rem;color:#e3b341;">{{ number_format($logStats['skipped']) }}</div>
            <div class="stat-label">Skipped</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-body" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
            <span style="color:var(--text-muted);font-size:0.82rem;font-weight:600;">Filter:</span>
            @php $statuses = ['all', 'success', 'failed', 'skipped', 'timeout']; @endphp
            @foreach($statuses as $s)
                <a href="{{ route('admin.crawler.logs', ['status' => $s]) }}"
                   class="btn btn-sm {{ $statusFilter === $s ? 'btn-primary' : '' }}"
                   style="padding:0.2rem 0.6rem;font-size:0.75rem;text-decoration:none;{{ $statusFilter !== $s ? 'background:rgba(139,148,158,0.1);color:var(--text-secondary);border:1px solid rgba(139,148,158,0.3);' : '' }}">
                    {{ ucfirst($s) }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card">
        <div class="card-header">
            {{ $logs->total() }} log entries{{ $statusFilter !== 'all' ? " (filtered: {$statusFilter})" : '' }}
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                    <thead>
                        <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border-light);">
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">URL</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Status</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">HTTP</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Time</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Found</th>
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Error / Note</th>
                            <th style="padding:0.6rem 1rem;text-align:right;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr style="border-bottom:1px solid var(--border-light);">
                                <td style="padding:0.5rem 1rem;max-width:280px;">
                                    @if($log->link)
                                        <a href="{{ route('admin.crawler.link-logs', $log->link_id) }}" style="font-family:var(--font-mono);font-size:0.75rem;color:var(--accent-cyan);text-decoration:none;" title="{{ $log->link->url }}">
                                            {{ Str::limit($log->link->url, 45) }}
                                        </a>
                                    @else
                                        <span style="color:var(--text-muted);font-style:italic;">Deleted link #{{ $log->link_id }}</span>
                                    @endif
                                </td>
                                <td style="padding:0.5rem 0.8rem;text-align:center;">
                                    @php
                                        $styles = match($log->status) {
                                            'success' => ['bg' => 'rgba(63,185,80,0.15)', 'c' => '#3fb950', 'b' => 'rgba(63,185,80,0.3)', 'i' => '✓'],
                                            'failed'  => ['bg' => 'rgba(248,81,73,0.15)', 'c' => '#f85149', 'b' => 'rgba(248,81,73,0.3)', 'i' => '✗'],
                                            'skipped' => ['bg' => 'rgba(226,183,20,0.15)', 'c' => '#e3b341', 'b' => 'rgba(226,183,20,0.3)', 'i' => '⏭'],
                                            'timeout' => ['bg' => 'rgba(210,153,34,0.15)', 'c' => '#d29922', 'b' => 'rgba(210,153,34,0.3)', 'i' => '⏰'],
                                            default   => ['bg' => 'rgba(139,148,158,0.15)', 'c' => '#8b949e', 'b' => 'rgba(139,148,158,0.3)', 'i' => '…'],
                                        };
                                    @endphp
                                    <span style="display:inline-block;padding:0.15rem 0.5rem;border-radius:4px;font-size:0.72rem;font-weight:700;text-transform:uppercase;background:{{ $styles['bg'] }};color:{{ $styles['c'] }};border:1px solid {{ $styles['b'] }};">
                                        {{ $styles['i'] }} {{ $log->status }}
                                    </span>
                                </td>
                                <td style="padding:0.5rem 0.8rem;text-align:center;color:var(--text-muted);font-family:var(--font-mono);font-size:0.78rem;">
                                    {{ $log->http_status ?? '—' }}
                                </td>
                                <td style="padding:0.5rem 0.8rem;text-align:center;color:var(--text-muted);font-size:0.78rem;">
                                    {{ $log->response_time_ms ? $log->response_time_ms . 'ms' : '—' }}
                                </td>
                                <td style="padding:0.5rem 0.8rem;text-align:center;color:var(--accent-cyan);font-weight:600;">
                                    {{ $log->discovered_count > 0 ? $log->discovered_count : '—' }}
                                </td>
                                <td style="padding:0.5rem 1rem;font-size:0.75rem;color:#f85149;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->error_message }}">
                                    {{ $log->error_message ?? '' }}
                                </td>
                                <td style="padding:0.5rem 1rem;text-align:right;color:var(--text-muted);white-space:nowrap;font-size:0.78rem;">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:2rem;text-align:center;color:var(--text-muted);">No crawl logs recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
            <div style="padding:1rem;border-top:1px solid var(--border-light);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</x-app.layouts>
