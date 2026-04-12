<x-app.layouts title="Admin - Uptime Surveillance">

    @include('admin._nav')

    <div class="admin-header">
        <h1>Network Surveillance</h1>
        <p>Historical record of uptime verification requests across the Tor network.</p>
    </div>

    @if ($logs->count() > 0)
        <div class="panel">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Target Node</th>
                            <th>Status</th>
                            <th style="text-align:center;">Latency</th>
                            <th class="hide-mobile" style="text-align:center;">Source</th>
                            <th style="text-align:right;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>
                                    @if ($log->link)
                                        <a href="{{ route('link.show', $log->link->slug) }}" style="font-size:.78rem;font-weight:700;color:#fff;text-decoration:none;">{{ $log->link->title }}</a>
                                        <div style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);margin-top:.1rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->link->url }}</div>
                                    @else
                                        <span style="font-size:.55rem;color:var(--color-gh-dim);font-style:italic;opacity:.5;text-transform:uppercase;">Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $sbClass = match($log->status) {
                                            'online' => 'sb-online',
                                            'offline' => 'sb-offline',
                                            'timeout' => 'sb-timeout',
                                            default => 'sb-unknown',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $sbClass }}">{{ $log->status }}</span>
                                </td>
                                <td style="text-align:center;">
                                    @if ($log->response_time_ms)
                                        <span style="font-size:.7rem;font-family:monospace;font-weight:700;color:var(--color-gh-accent);">{{ $log->response_time_ms }}<span style="font-size:.55rem;opacity:.6;margin-left:.1rem;">ms</span></span>
                                    @else
                                        <span style="color:var(--color-gh-dim);opacity:.3;">—</span>
                                    @endif
                                </td>
                                <td class="hide-mobile" style="text-align:center;">
                                    <span style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.5;border:1px solid var(--color-gh-border);padding:.1rem .35rem;border-radius:.25rem;" title="{{ $log->checked_by_ip_hash }}">
                                        {{ substr($log->checked_by_ip_hash, 0, 8) }}…
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <span style="font-size:.6rem;font-weight:600;color:var(--color-gh-dim);">{{ $log->checked_at ? $log->checked_at->diffForHumans() : '—' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                    {{ $logs->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state" style="border:1px dashed var(--color-gh-border);border-radius:.6rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <p>No uptime logs recorded yet.</p>
        </div>
    @endif

</x-app.layouts>
