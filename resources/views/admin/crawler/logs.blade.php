<x-app.layouts title="Crawl Logs — {{ $link->url }}">

    <div class="page-header">
        <h1>📜 Crawl Logs</h1>
        <p style="font-family:var(--font-mono);font-size:0.85rem;color:var(--text-muted);">{{ $link->url }}</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.crawler.index') }}" class="active">Crawler</a>
    </nav>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    {{-- Link Summary --}}
    <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-value" style="font-size:1.1rem;">{{ $link->crawl_count }}</div>
            <div class="stat-label">Total Crawls</div>
        </div>
        <div class="stat-card">
            @php
                $cs = $link->crawl_status;
                $statusColor = match($cs) {
                    'success' => 'var(--accent-green)',
                    'failed'  => '#f85149',
                    default   => 'var(--text-muted)',
                };
            @endphp
            <div class="stat-value" style="font-size:1.1rem;color:{{ $statusColor }};text-transform:uppercase;">{{ $cs }}</div>
            <div class="stat-label">Last Status</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size:1.1rem;">
                {{ $link->last_crawled_at ? $link->last_crawled_at->diffForHumans() : 'Never' }}
            </div>
            <div class="stat-label">Last Crawled</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="font-size:1.1rem;color:var(--accent-cyan);">{{ $link->discoveredLinks()->count() }}</div>
            <div class="stat-label">Discovered URLs</div>
        </div>
    </div>

    {{-- Indexed Content Preview --}}
    @if($content)
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <span>📄 Indexed Content</span>
            @if($content->language)
                <span style="font-size:0.72rem;background:rgba(88,166,255,0.15);color:var(--accent-blue);border:1px solid rgba(88,166,255,0.3);padding:0.1rem 0.4rem;border-radius:3px;">
                    {{ strtoupper($content->language) }}
                </span>
            @endif
        </div>
        <div class="card-body" style="font-size:0.82rem;line-height:1.6;">
            <div style="display:grid;grid-template-columns:100px 1fr;gap:0.4rem 1rem;margin-bottom:1rem;">
                <span style="color:var(--text-muted);font-weight:600;">Domain</span>
                <span style="font-family:var(--font-mono);font-size:0.78rem;">{{ $content->domain ?? '—' }}</span>

                <span style="color:var(--text-muted);font-weight:600;">H1</span>
                <span>{{ $content->h1 ?: '—' }}</span>

                <span style="color:var(--text-muted);font-weight:600;">Meta</span>
                <span>{{ Str::limit($content->meta_description, 200) ?: '—' }}</span>

                <span style="color:var(--text-muted);font-weight:600;">Content-Type</span>
                <span style="font-family:var(--font-mono);font-size:0.78rem;">{{ $content->content_type ?? '—' }}</span>

                <span style="color:var(--text-muted);font-weight:600;">Size</span>
                <span>{{ number_format($content->content_length) }} bytes</span>
            </div>

            @if($content->body_text)
                <details>
                    <summary style="cursor:pointer;color:var(--accent-cyan);font-weight:600;margin-bottom:0.5rem;">
                        Show body text ({{ number_format(strlen($content->body_text)) }} chars)
                    </summary>
                    <div style="background:var(--bg-secondary);border:1px solid var(--border-light);border-radius:6px;padding:1rem;max-height:300px;overflow-y:auto;font-family:var(--font-mono);font-size:0.75rem;color:var(--text-secondary);white-space:pre-wrap;word-break:break-all;">{{ Str::limit($content->body_text, 5000) }}</div>
                </details>
            @endif
        </div>
    </div>
    @endif

    {{-- Crawl Logs Table --}}
    <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <span>{{ $logs->total() }} crawl log{{ $logs->total() !== 1 ? 's' : '' }}</span>
            <a href="{{ route('admin.crawler.index') }}" class="btn btn-sm btn-secondary">← Back</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                    <thead>
                        <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border-light);">
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">When</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Status</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">HTTP</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Time</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Found</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Size</th>
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr style="border-bottom:1px solid var(--border-light);">
                                <td style="padding:0.5rem 1rem;color:var(--text-muted);white-space:nowrap;font-size:0.78rem;">
                                    {{ $log->created_at->diffForHumans() }}
                                    <div style="font-size:0.68rem;color:var(--text-muted);opacity:0.7;">{{ $log->created_at->format('M d, H:i') }}</div>
                                </td>
                                <td style="padding:0.5rem 0.8rem;text-align:center;">
                                    @php
                                        $statusStyles = match($log->status) {
                                            'success' => ['bg' => 'rgba(63,185,80,0.15)', 'color' => '#3fb950', 'border' => 'rgba(63,185,80,0.3)', 'icon' => '✓'],
                                            'failed'  => ['bg' => 'rgba(248,81,73,0.15)', 'color' => '#f85149', 'border' => 'rgba(248,81,73,0.3)', 'icon' => '✗'],
                                            'skipped' => ['bg' => 'rgba(226,183,20,0.15)', 'color' => '#e3b341', 'border' => 'rgba(226,183,20,0.3)', 'icon' => '⏭'],
                                            'timeout' => ['bg' => 'rgba(210,153,34,0.15)', 'color' => '#d29922', 'border' => 'rgba(210,153,34,0.3)', 'icon' => '⏰'],
                                            default   => ['bg' => 'rgba(139,148,158,0.15)', 'color' => '#8b949e', 'border' => 'rgba(139,148,158,0.3)', 'icon' => '…'],
                                        };
                                    @endphp
                                    <span style="display:inline-block;padding:0.15rem 0.5rem;border-radius:4px;font-size:0.72rem;font-weight:700;text-transform:uppercase;background:{{ $statusStyles['bg'] }};color:{{ $statusStyles['color'] }};border:1px solid {{ $statusStyles['border'] }};">
                                        {{ $statusStyles['icon'] }} {{ $log->status }}
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
                                <td style="padding:0.5rem 0.8rem;text-align:center;color:var(--text-muted);font-size:0.78rem;">
                                    @if($log->content_length > 0)
                                        {{ $log->content_length > 1024 ? round($log->content_length / 1024, 1) . 'KB' : $log->content_length . 'B' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="padding:0.5rem 1rem;font-size:0.75rem;color:#f85149;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->error_message }}">
                                    {{ $log->error_message ?? '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:2rem;text-align:center;color:var(--text-muted);">No crawl logs yet.</td>
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
