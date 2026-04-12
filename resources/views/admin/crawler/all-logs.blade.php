<x-app.layouts title="Admin - Global Crawl Logs">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>Intelligence Stream</h1>
            <p>Consolidated audit trail of all crawl operations across the network.</p>
        </div>
        <a href="{{ route('admin.crawler.index') }}" style="font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:.3rem;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Crawler
        </a>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.5rem;margin-bottom:1.25rem;">
        @foreach([
            [number_format($logStats['total']), 'Total'],
            [number_format($logStats['success']), 'Success'],
            [number_format($logStats['failed']), 'Failed'],
            [number_format($logStats['skipped']), 'Skipped'],
        ] as $s)
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.65rem .8rem;text-align:center;">
                <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.3rem;">{{ $s[1] }}</div>
                <div style="font-size:1.2rem;font-weight:900;color:#fff;">{{ $s[0] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        @foreach(['all', 'success', 'failed', 'skipped', 'timeout'] as $s)
            <a href="{{ route('admin.crawler.logs', ['status' => $s]) }}" class="{{ $statusFilter === $s ? 'active' : '' }}">{{ $s }}</a>
        @endforeach
    </div>

    {{-- Logs Table --}}
    <div class="panel">
        <div class="panel-head" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.4rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Audit Feed
            </div>
            <span style="font-size:.55rem;font-weight:700;color:var(--color-gh-dim);">{{ $logs->total() }} events</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Node</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;" class="hide-mobile">HTTP</th>
                        <th style="text-align:center;" class="hide-mobile">Latency</th>
                        <th class="hide-mobile">Error</th>
                        <th style="text-align:right;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                @if($log->link)
                                    <a href="{{ route('admin.crawler.link-logs', $log->link_id) }}" style="font-family:monospace;font-size:.65rem;color:var(--color-gh-accent);text-decoration:none;display:block;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->link->url }}">{{ $log->link->url }}</a>
                                @else
                                    <span style="font-size:.55rem;color:var(--color-gh-dim);opacity:.5;">Deleted #{{ $log->link_id }}</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @php
                                    $sbCls = match($log->status) {
                                        'success' => 'sb-success',
                                        'failed' => 'sb-failed',
                                        'skipped' => 'sb-skipped',
                                        'timeout' => 'sb-timeout',
                                        default => 'sb-unknown',
                                    };
                                @endphp
                                <span class="status-badge {{ $sbCls }}">{{ $log->status }}</span>
                            </td>
                            <td style="text-align:center;" class="hide-mobile">
                                <span style="font-family:monospace;font-size:.65rem;color:{{ $log->http_status == 200 ? '#4ade80' : ($log->http_status >= 400 ? '#f87171' : 'var(--color-gh-dim)') }};">{{ $log->http_status ?? '—' }}</span>
                            </td>
                            <td style="text-align:center;" class="hide-mobile">
                                <span style="font-family:monospace;font-size:.65rem;color:var(--color-gh-accent);font-weight:700;">{{ $log->response_time_ms ? $log->response_time_ms . 'ms' : '—' }}</span>
                            </td>
                            <td class="hide-mobile">
                                <span style="font-size:.6rem;color:#f87171;opacity:.7;display:block;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->error_message }}">{{ $log->error_message ?: '—' }}</span>
                            </td>
                            <td style="text-align:right;">
                                <span style="font-size:.6rem;font-weight:700;color:#fff;">{{ $log->created_at->diffForHumans() }}</span>
                                <div style="font-size:.5rem;font-family:monospace;color:var(--color-gh-dim);opacity:.5;margin-top:.1rem;">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <p>No events recorded.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                {{ $logs->links('pagination.simple') }}
            </div>
        @endif
    </div>

</x-app.layouts>
