<x-app.layouts title="Admin - Crawl Logs">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>Node Intelligence</h1>
            <p style="font-family:monospace;font-size:.68rem;color:var(--color-gh-dim);opacity:.6;margin-top:.2rem;max-width:500px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $link->url }}">{{ $link->url }}</p>
        </div>
        <a href="{{ route('admin.crawler.index') }}" style="font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:.3rem;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    {{-- Summary Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.5rem;margin-bottom:1.5rem;">
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.65rem .8rem;text-align:center;">
            <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.3rem;">Total Crawls</div>
            <div style="font-size:1.3rem;font-weight:900;color:#fff;">{{ $link->crawl_count }}</div>
        </div>
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.65rem .8rem;text-align:center;">
            <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.3rem;">Status</div>
            <div style="font-size:1rem;font-weight:900;color:{{ $link->crawl_status === 'success' ? '#4ade80' : ($link->crawl_status === 'failed' ? '#f87171' : '#fff') }};text-transform:uppercase;">{{ $link->crawl_status }}</div>
        </div>
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.65rem .8rem;text-align:center;">
            <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.3rem;">Last Crawl</div>
            <div style="font-size:.8rem;font-weight:700;color:#fff;">{{ $link->last_crawled_at ? $link->last_crawled_at->diffForHumans() : '—' }}</div>
        </div>
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.65rem .8rem;text-align:center;">
            <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.3rem;">Discovered</div>
            <div style="font-size:1.3rem;font-weight:900;color:#22d3ee;">{{ $link->discoveredLinks()->count() }}</div>
        </div>
    </div>

    {{-- Indexed Content --}}
    @if($content)
        <div class="panel" style="margin-bottom:1.5rem;">
            <div class="panel-head" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:.4rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Indexed Content
                </div>
                @if($content->language)
                    <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;color:var(--color-gh-accent);border:1px solid rgba(88,166,255,.2);padding:.1rem .35rem;border-radius:.25rem;">{{ $content->language }}</span>
                @endif
            </div>
            <div style="padding:1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:.75rem;">
                    <div>
                        <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.25rem;">Domain</div>
                        <div style="font-family:monospace;font-size:.72rem;color:#fff;border:1px solid var(--color-gh-border);padding:.35rem .6rem;border-radius:.35rem;">{{ $content->domain ?: '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.25rem;">Content-Type</div>
                        <div style="display:flex;align-items:center;gap:.4rem;">
                            <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);border:1px solid rgba(88,166,255,.15);padding:.15rem .35rem;border-radius:.25rem;text-transform:uppercase;">{{ $content->content_type ?: '—' }}</span>
                            <span style="font-size:.55rem;color:var(--color-gh-dim);">{{ number_format($content->content_length) }} bytes</span>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.25rem;">H1</div>
                    <div style="font-size:.78rem;font-weight:700;color:#fff;">{{ $content->h1 ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.25rem;">Meta Description</div>
                    <div style="font-size:.68rem;color:var(--color-gh-dim);font-style:italic;line-height:1.5;">{{ $content->meta_description ?: 'No description found.' }}</div>
                </div>

                @if($content->body_text)
                    <details style="margin-top:.75rem;border-top:1px solid var(--color-gh-border);padding-top:.75rem;">
                        <summary style="cursor:pointer;font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.06em;">
                            ▸ Body Text ({{ number_format(strlen($content->body_text)) }} chars)
                        </summary>
                        <div style="margin-top:.5rem;border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.75rem;max-height:300px;overflow-y:auto;font-family:monospace;font-size:.6rem;color:var(--color-gh-dim);opacity:.7;white-space:pre-wrap;line-height:1.6;">{{ Str::limit($content->body_text, 8000) }}</div>
                    </details>
                @endif
            </div>
        </div>
    @endif

    {{-- Crawl History --}}
    <div class="panel">
        <div class="panel-head" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.4rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Scan History
            </div>
            <span style="font-size:.55rem;font-weight:700;color:var(--color-gh-dim);">{{ $logs->total() }} records</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;" class="hide-mobile">HTTP</th>
                        <th style="text-align:center;" class="hide-mobile">Latency</th>
                        <th class="hide-mobile">Error</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div style="font-size:.7rem;font-weight:700;color:#fff;">{{ $log->created_at->diffForHumans() }}</div>
                                <div style="font-size:.5rem;font-family:monospace;color:var(--color-gh-dim);opacity:.5;margin-top:.1rem;">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
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
                                <span style="font-family:monospace;font-size:.65rem;color:{{ $log->http_status == 200 ? '#4ade80' : '#fb923c' }};">{{ $log->http_status ?: '—' }}</span>
                            </td>
                            <td style="text-align:center;" class="hide-mobile">
                                <span style="font-family:monospace;font-size:.65rem;color:var(--color-gh-accent);font-weight:700;">{{ $log->response_time_ms ? $log->response_time_ms . 'ms' : '—' }}</span>
                            </td>
                            <td class="hide-mobile">
                                <span style="font-size:.6rem;color:#f87171;opacity:.7;display:block;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->error_message }}">{{ $log->error_message ?: '—' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <p>No scan history.</p>
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
