<x-app.layouts title="Admin - Uptime Logs">

    <div class="page-header">
        <h1>Uptime Check Logs</h1>
        <p>Monitor all uptime checks performed by users.</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.ads') }}">Ads</a>
        <a href="{{ route('admin.uptime-logs') }}" class="active">Uptime Logs</a>
        <a href="{{ route('admin.blacklist') }}">Blacklist</a>
    </nav>

    @if ($logs->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Link</th>
                    <th>Status</th>
                    <th>Response Time</th>
                    <th class="hide-mobile">IP Hash</th>
                    <th>Checked At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td>
                            @if ($log->link)
                                <a href="{{ route('link.show', $log->link->slug) }}">{{ $log->link->title }}</a>
                            @else
                                <span class="text-muted">[Deleted]</span>
                            @endif
                        </td>
                        <td>
                            <span class="uptime-badge uptime-{{ $log->status }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem;">
                            @if ($log->response_time_ms)
                                {{ $log->response_time_ms }}ms
                            @else
                                —
                            @endif
                        </td>
                        <td class="hide-mobile text-muted mono" style="font-size:0.7rem;">
                            {{ Str::limit($log->checked_by_ip_hash, 12) }}...
                        </td>
                        <td style="font-size:0.8rem;">
                            {{ $log->checked_at ? $log->checked_at->diffForHumans() : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $logs->links('pagination.simple') }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted">
                No uptime checks recorded yet.
            </div>
        </div>
    @endif

</x-app.layouts>

