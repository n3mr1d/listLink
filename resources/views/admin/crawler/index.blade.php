<x-app.layouts title="Admin - Crawler Engine">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>Crawler Engine</h1>
            <p>Automated reconnaissance and indexing — queue management with real-time status tracking.</p>
        </div>
        <form method="POST" action="{{ route('admin.crawler.dispatch') }}">
            @csrf
            <button type="submit" style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;border:none;cursor:pointer;" onclick="return confirm('Initiate smart discovery?')">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                Smart Dispatch
            </button>
        </form>
    </div>

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:.5rem;margin-bottom:1.5rem;">
        @foreach([
            [$stats['total'], 'Total'],
            [$stats['never_crawled'], 'Pending'],
            [$stats['success'], 'Success'],
            [$stats['failed'], 'Failed'],
            [$stats['queue_waiting'], 'In Queue'],
            [$stats['queue_processing'], 'Processing'],
            [number_format($stats['discovered']), 'Discovered'],
            [number_format($stats['indexed']), 'Indexed'],
        ] as $s)
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.65rem .8rem;text-align:center;">
                <div style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);margin-bottom:.3rem;">{{ $s[1] }}</div>
                <div style="font-size:1.2rem;font-weight:900;color:#fff;">{{ $s[0] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Performance Row --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.6rem;margin-bottom:1.5rem;">
        <div style="border:1px solid rgba(34,211,238,.2);border-radius:.5rem;padding:.85rem 1rem;">
            <div style="font-size:1.3rem;font-weight:900;color:#fff;">{{ $stats['crawls_24h'] }}</div>
            <div style="font-size:.55rem;font-weight:800;color:#22d3ee;text-transform:uppercase;">Crawls (24h)</div>
        </div>
        <div style="border:1px solid rgba(74,222,128,.2);border-radius:.5rem;padding:.85rem 1rem;">
            <div style="font-size:1.3rem;font-weight:900;color:#fff;">{{ $stats['success_24h'] }}</div>
            <div style="font-size:.55rem;font-weight:800;color:#4ade80;text-transform:uppercase;">Success (24h)</div>
        </div>
        <div style="border:1px solid rgba(168,85,247,.2);border-radius:.5rem;padding:.85rem 1rem;">
            <div style="font-size:1.3rem;font-weight:900;color:#fff;">{{ $stats['avg_response_ms'] ? $stats['avg_response_ms'] . 'ms' : '—' }}</div>
            <div style="font-size:.55rem;font-weight:800;color:#a855f7;text-transform:uppercase;">Avg Latency</div>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1.25rem;align-items:center;">
        <form action="{{ route('admin.crawler.index') }}" method="GET" style="display:flex;gap:.4rem;flex:1;min-width:0;align-items:center;">
            <div style="flex:1;display:flex;align-items:center;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;overflow:hidden;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="2.5" style="margin-left:.6rem;flex-shrink:0;opacity:.5;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search URL or title..."
                    style="background:transparent;border:none;color:#fff;padding:.45rem .6rem;width:100%;outline:none;font-size:.75rem;">
            </div>
            @if($statusFilter !== 'all')
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <button type="submit" style="padding:.45rem .75rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.4rem;font-size:.6rem;font-weight:900;text-transform:uppercase;cursor:pointer;white-space:nowrap;">Search</button>
            @if($search || $statusFilter !== 'all')
                <a href="{{ route('admin.crawler.index') }}" style="padding:.45rem .65rem;border:1px solid var(--color-gh-border);border-radius:.4rem;font-size:.55rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;white-space:nowrap;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Status Filter Tabs --}}
    <div class="filter-bar">
        @foreach([
            ['all', 'All'],
            ['success', 'Success'],
            ['failed', 'Failed'],
            ['pending', 'Pending'],
        ] as $f)
            <a href="{{ route('admin.crawler.index', array_merge(request()->only('q'), ['status' => $f[0]])) }}" class="{{ $statusFilter === $f[0] ? 'active' : '' }}">{{ $f[1] }}</a>
        @endforeach
    </div>

    {{-- Queue Status + Link Table --}}
    <div class="panel">
        <div class="panel-head" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.4rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><circle cx="6" cy="6" r="1"/><circle cx="6" cy="18" r="1"/></svg>
                Real-time Queue
                @if($search)
                    <span style="font-size:.5rem;color:var(--color-gh-dim);margin-left:.3rem;">results for "{{ $search }}"</span>
                @endif
            </div>
            <a href="{{ route('admin.crawler.logs') }}" style="font-size:.55rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;letter-spacing:.06em;">All Logs →</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Node</th>
                        <th style="text-align:center;">Crawl Status</th>
                        <th style="text-align:center;" class="hide-mobile">Queue</th>
                        <th style="text-align:center;" class="hide-mobile">Last Crawl</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                        <tr>
                            <td>
                                <div style="font-family:monospace;font-size:.68rem;color:#fff;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $link->url }}">{{ Str::limit($link->url, 50) }}</div>
                                <div style="font-size:.6rem;color:var(--color-gh-dim);font-style:italic;margin-top:.1rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $link->title }}</div>
                            </td>
                            <td style="text-align:center;">
                                @php
                                    $stClass = match($link->crawl_status) {
                                        'success' => 'sb-success',
                                        'failed' => 'sb-failed',
                                        'pending' => 'sb-pending',
                                        default => 'sb-unknown',
                                    };
                                @endphp
                                <span class="status-badge {{ $stClass }}">
                                    @if($link->force_recrawl)
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor" style="margin-right:.15rem;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                    @endif
                                    {{ $link->crawl_status ?? 'new' }}
                                </span>
                            </td>
                            <td style="text-align:center;" class="hide-mobile">
                                @php
                                    $qClass = match($link->crawl_queue_status) {
                                        'queued' => 'sb-pending',
                                        'processing' => 'sb-active',
                                        'completed' => 'sb-success',
                                        'failed' => 'sb-failed',
                                        default => 'sb-unknown',
                                    };
                                @endphp
                                <span class="status-badge {{ $qClass }}">{{ $link->crawl_queue_status ?? 'idle' }}</span>
                            </td>
                            <td style="text-align:center;" class="hide-mobile">
                                <span style="font-size:.6rem;color:var(--color-gh-dim);">{{ $link->last_crawled_at ? $link->last_crawled_at->diffForHumans() : '—' }}</span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex;gap:.25rem;align-items:center;">
                                    <form method="POST" action="{{ route('admin.crawler.crawl-single', $link->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-sm" style="color:var(--color-gh-dim);" title="Re-crawl">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.crawler.link-logs', $link->id) }}" class="btn-sm" style="color:var(--color-gh-dim);" title="Logs">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                                    @if($search)
                                        <p>No results for "{{ $search }}"</p>
                                    @else
                                        <p>No links to crawl yet.</p>
                                    @endif
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

    {{-- Sidebar: Actions + Audit Trail --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
        {{-- Strategic Actions --}}
        <div class="panel">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Strategic Overrides
            </div>
            <div style="padding:.75rem 1rem;">
                <form method="POST" action="{{ route('admin.crawler.crawl-all') }}">
                    @csrf
                    <button type="submit" style="width:100%;padding:.55rem;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);color:#f87171;border-radius:.4rem;font-size:.6rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;" onclick="return confirm('Execute mandatory total crawl?')">
                        Force Total Sync
                    </button>
                </form>
                <p style="font-size:.6rem;color:var(--color-gh-dim);font-style:italic;line-height:1.5;margin:.5rem 0 0;">Bypasses interval checks and queues all records for verification.</p>
            </div>
        </div>

        {{-- Audit Trail --}}
        <div class="panel">
            <div class="panel-head" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:.4rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Audit Trail
                </div>
                <div style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></div>
            </div>
            <div style="max-height:360px;overflow-y:auto;">
                @foreach($recentLogs as $log)
                    <div style="padding:.5rem 1rem;border-bottom:1px solid rgba(48,54,61,.3);display:flex;align-items:flex-start;gap:.5rem;">
                        <span style="flex-shrink:0;font-size:.7rem;margin-top:.1rem;">{{ match($log->status) { 'success' => '✓', 'failed' => '✗', 'skipped' => '⊘', 'timeout' => '◌', default => '·' } }}</span>
                        <div style="flex:1;min-width:0;">
                            <div style="font-family:monospace;font-size:.6rem;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:.15rem;">{{ $log->link ? Str::limit($log->link->url, 40) : 'Deleted' }}</div>
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:.3rem;">
                                <span style="font-size:.5rem;color:var(--color-gh-dim);text-transform:uppercase;font-weight:700;">{{ $log->created_at->diffForHumans() }}</span>
                                @if($log->response_time_ms)<span style="font-size:.5rem;font-family:monospace;color:var(--color-gh-accent);">{{ $log->response_time_ms }}ms</span>@endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Architecture Note --}}
    <div style="border:1px solid rgba(88,166,255,.15);border-radius:.5rem;padding:.85rem 1rem;margin-top:1rem;">
        <div style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.3rem;display:flex;align-items:center;gap:.3rem;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            Architecture Notes
        </div>
        <p style="font-size:.68rem;color:var(--color-gh-dim);line-height:1.6;margin:0;font-style:italic;">Active {{ $crawlInterval }}-day rolling cycle. Scheduler dispatches every 6h to stale nodes. New submissions are auto-queued immediately. Queue status: idle → queued → processing → completed/failed.</p>
    </div>

</x-app.layouts>
