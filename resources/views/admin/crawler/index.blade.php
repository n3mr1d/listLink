<x-app.layouts title="Crawler Management">

    <div class="page-header">
        <h1>🕷 Crawler Management</h1>
        <p>Automated link crawling inspired by Ahmia architecture — queue-based, recursive, content-indexed, deduplication-aware.</p>
    </div>

    {{-- Admin Navigation --}}
    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.ads') }}">Ads</a>
        <a href="{{ route('admin.uptime-logs') }}">Uptime Logs</a>
        <a href="{{ route('admin.blacklist') }}">Blacklist</a>
        <a href="{{ route('admin.crawler.index') }}" class="active">Crawler</a>
    </nav>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:1.5rem;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom:1.5rem;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); margin-bottom:2rem;">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Links</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-blue);">{{ $stats['never_crawled'] }}</div>
            <div class="stat-label">Never Crawled</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-green);">{{ $stats['success'] }}</div>
            <div class="stat-label">Crawl Success</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-red, #f85149);">{{ $stats['failed'] }}</div>
            <div class="stat-label">Crawl Failed</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-yellow, #e3b341);">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-purple);">{{ $stats['force_queued'] }}</div>
            <div class="stat-label">Force-Flag</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-cyan);">{{ number_format($stats['discovered']) }}</div>
            <div class="stat-label">Discovered URLs</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-green);">{{ number_format($stats['indexed']) }}</div>
            <div class="stat-label">Pages Indexed</div>
        </div>
    </div>

    {{-- 24h Performance Strip --}}
    <div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); margin-bottom:2rem;">
        <div class="stat-card" style="border-left:3px solid var(--accent-cyan);">
            <div class="stat-value" style="font-size:1.2rem;">{{ $stats['crawls_24h'] }}</div>
            <div class="stat-label">Crawls (24h)</div>
        </div>
        <div class="stat-card" style="border-left:3px solid var(--accent-green);">
            <div class="stat-value" style="font-size:1.2rem;">{{ $stats['success_24h'] }}</div>
            <div class="stat-label">Successful (24h)</div>
        </div>
        <div class="stat-card" style="border-left:3px solid var(--accent-purple);">
            <div class="stat-value" style="font-size:1.2rem;">
                {{ $stats['avg_response_ms'] ? $stats['avg_response_ms'] . 'ms' : '—' }}
            </div>
            <div class="stat-label">Avg Response (24h)</div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header" style="display:flex;align-items:center;gap:0.5rem;">
            ⚡ Crawler Actions
        </div>
        <div class="card-body" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-start;">

            {{-- Smart Dispatch --}}
            <div style="flex:1;min-width:230px;">
                <form method="POST" action="{{ route('admin.crawler.dispatch') }}">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="btn-smart-dispatch"
                        style="width:100%;justify-content:center;"
                        onclick="return confirm('Dispatch smart crawl? Only uncrawled / overdue / force-flagged links will be queued.')">
                        🔍 Smart Dispatch
                    </button>
                </form>
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;">
                    Queues only links that are: never crawled, flagged, or ≥{{ $crawlInterval }} days old.
                </small>
            </div>

            {{-- Crawl All --}}
            <div style="flex:1;min-width:230px;">
                <form method="POST" action="{{ route('admin.crawler.crawl-all') }}">
                    @csrf
                    <button
                        type="submit"
                        class="btn"
                        id="btn-crawl-all"
                        style="width:100%;justify-content:center;background:rgba(248,81,73,0.15);color:#f85149;border:1px solid rgba(248,81,73,0.4);"
                        onclick="return confirm('⚠️ This will re-crawl ALL links regardless of history. Continue?')">
                        🔥 Crawl ALL Links
                    </button>
                </form>
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;">
                    Manual override — crawls every link unconditionally.
                </small>
            </div>

            {{-- View All Logs --}}
            <div style="flex:1;min-width:230px;">
                <a href="{{ route('admin.crawler.logs') }}"
                   class="btn"
                   style="width:100%;justify-content:center;background:rgba(139,148,158,0.15);color:var(--text-secondary);border:1px solid rgba(139,148,158,0.3);display:inline-flex;text-decoration:none;">
                    📜 View All Crawl Logs
                </a>
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;">
                    Full audit trail of all crawl attempts.
                </small>
            </div>
        </div>
    </div>

    {{-- Recent Activity Feed --}}
    @if($recentLogs->count() > 0)
    <div class="card" style="margin-bottom:2rem;">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <span>📡 Recent Crawl Activity</span>
            <a href="{{ route('admin.crawler.logs') }}" style="font-size:0.8rem;color:var(--accent-cyan);text-decoration:none;">View all →</a>
        </div>
        <div class="card-body" style="padding:0;max-height:320px;overflow-y:auto;">
            @foreach($recentLogs as $log)
                <div style="padding:0.45rem 1rem;border-bottom:1px solid var(--border-light);display:flex;align-items:center;gap:0.6rem;font-size:0.78rem;">
                    @php
                        $logIcon = match($log->status) {
                            'success' => '✅',
                            'failed'  => '❌',
                            'skipped' => '⏭',
                            'timeout' => '⏰',
                            default   => '⚪',
                        };
                    @endphp
                    <span>{{ $logIcon }}</span>
                    <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-family:var(--font-mono);font-size:0.72rem;color:var(--text-primary);">
                        {{ $log->link ? Str::limit($log->link->url, 50) : 'Deleted link' }}
                    </span>
                    @if($log->response_time_ms)
                        <span style="color:var(--text-muted);font-size:0.7rem;">{{ $log->response_time_ms }}ms</span>
                    @endif
                    @if($log->discovered_count > 0)
                        <span style="color:var(--accent-cyan);font-size:0.7rem;">{{ $log->discovered_count }} URLs</span>
                    @endif
                    <span style="color:var(--text-muted);font-size:0.68rem;white-space:nowrap;">
                        {{ $log->created_at->diffForHumans() }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Links Table --}}
    <div class="card">
        <div class="card-header">
            📋 Link Crawl Status
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                    <thead>
                        <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border-light);">
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">URL</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">#</th>
                            <th style="padding:0.6rem 0.8rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Last Crawled</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Found URLs</th>
                            <th style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Indexed</th>
                            <th style="padding:0.6rem 1rem;text-align:right;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($links as $link)
                            <tr style="border-bottom:1px solid var(--border-light);">
                                {{-- URL --}}
                                <td style="padding:0.6rem 1rem;max-width:280px;">
                                    <div style="font-family:var(--font-mono);font-size:0.78rem;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $link->url }}">
                                        {{ Str::limit($link->url, 45) }}
                                    </div>
                                    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:0.1rem;">{{ $link->title }}</div>
                                    @if($link->force_recrawl)
                                        <span style="display:inline-block;padding:0.1rem 0.35rem;border-radius:3px;font-size:0.65rem;font-weight:700;text-transform:uppercase;background:rgba(226,183,20,0.15);color:#e3b341;border:1px solid rgba(226,183,20,0.3);margin-top:0.2rem;">FORCE</span>
                                    @endif
                                </td>

                                {{-- Crawl Status --}}
                                <td style="padding:0.6rem 0.8rem;text-align:center;">
                                    @php
                                        $cs = $link->crawl_status;
                                        if ($cs === 'success') {
                                            $badgeBg = 'rgba(63,185,80,0.15)'; $badgeColor = '#3fb950'; $badgeBorder = 'rgba(63,185,80,0.3)'; $badgeIcon = '✓';
                                        } elseif ($cs === 'failed') {
                                            $badgeBg = 'rgba(248,81,73,0.15)'; $badgeColor = '#f85149'; $badgeBorder = 'rgba(248,81,73,0.3)'; $badgeIcon = '✗';
                                        } else {
                                            $badgeBg = 'rgba(139,148,158,0.15)'; $badgeColor = '#8b949e'; $badgeBorder = 'rgba(139,148,158,0.3)'; $badgeIcon = '…';
                                        }
                                    @endphp
                                    <span style="display:inline-block;padding:0.15rem 0.5rem;border-radius:4px;font-size:0.72rem;font-weight:700;text-transform:uppercase;background:{{ $badgeBg }};color:{{ $badgeColor }};border:1px solid {{ $badgeBorder }};">
                                        {{ $badgeIcon }} {{ $cs }}
                                    </span>
                                </td>

                                {{-- Crawl Count --}}
                                <td style="padding:0.6rem 0.8rem;text-align:center;color:var(--text-muted);">
                                    {{ $link->crawl_count }}
                                </td>

                                {{-- Last Crawled --}}
                                <td style="padding:0.6rem 0.8rem;color:var(--text-muted);white-space:nowrap;">
                                    @if($link->last_crawled_at)
                                        {{ $link->last_crawled_at->diffForHumans() }}
                                    @else
                                        <span style="color:var(--accent-blue);">Never</span>
                                    @endif
                                </td>

                                {{-- Discovered URLs count --}}
                                <td style="padding:0.6rem 0.8rem;text-align:center;">
                                    @php $foundCount = $link->discoveredLinks()->count(); @endphp
                                    @if($foundCount > 0)
                                        <a href="{{ route('admin.crawler.discovered', $link->id) }}" style="color:var(--accent-cyan);text-decoration:none;font-weight:600;">
                                            {{ number_format($foundCount) }}
                                        </a>
                                    @else
                                        <span style="color:var(--text-muted);">—</span>
                                    @endif
                                </td>

                                {{-- Indexed Status --}}
                                <td style="padding:0.6rem 0.8rem;text-align:center;">
                                    @php $hasContent = $link->crawlContent()->exists(); @endphp
                                    @if($hasContent)
                                        <span style="color:var(--accent-green);font-size:0.8rem;" title="Content indexed">✓</span>
                                    @else
                                        <span style="color:var(--text-muted);">—</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td style="padding:0.6rem 1rem;text-align:right;">
                                    <div style="display:flex;gap:0.4rem;justify-content:flex-end;flex-wrap:wrap;">
                                        {{-- Force Crawl --}}
                                        <form method="POST" action="{{ route('admin.crawler.crawl-single', $link->id) }}" style="display:inline;">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="btn btn-sm"
                                                title="Force crawl this link"
                                                style="padding:0.2rem 0.5rem;font-size:0.75rem;background:rgba(88,166,255,0.15);color:var(--accent-blue);border:1px solid rgba(88,166,255,0.3);">
                                                🔄 Crawl
                                            </button>
                                        </form>

                                        {{-- View Logs --}}
                                        <a href="{{ route('admin.crawler.link-logs', $link->id) }}"
                                           class="btn btn-sm"
                                           title="View crawl logs"
                                           style="padding:0.2rem 0.5rem;font-size:0.75rem;background:rgba(139,148,158,0.1);color:var(--text-secondary);border:1px solid rgba(139,148,158,0.3);text-decoration:none;">
                                            📜 Logs
                                        </a>

                                        {{-- Reset Flag --}}
                                        @if($link->force_recrawl)
                                            <form method="POST" action="{{ route('admin.crawler.reset-force', $link->id) }}" style="display:inline;">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm"
                                                    title="Clear force_recrawl flag"
                                                    style="padding:0.2rem 0.5rem;font-size:0.75rem;background:rgba(226,183,20,0.1);color:#e3b341;border:1px solid rgba(226,183,20,0.3);">
                                                    ✖ Reset
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:2rem;text-align:center;color:var(--text-muted);">No links found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($links->hasPages())
            <div style="padding:1rem;border-top:1px solid var(--border-light);">
                {{ $links->links() }}
            </div>
        @endif
    </div>

    {{-- Architecture Info --}}
    <div class="card" style="margin-top:2rem;">
        <div class="card-header">ℹ️ Crawler Architecture (Ahmia-Inspired)</div>
        <div class="card-body" style="font-size:0.85rem;color:var(--text-muted);line-height:1.7;">
            <ul style="padding-left:1.2rem;margin:0;">
                <li><strong style="color:var(--text-primary);">Queue-based</strong> — Each link crawl is an isolated Laravel <code>CrawlLinkJob</code> with <code>WithoutOverlapping</code> middleware, dispatched to the <code>{{ config('crawler.queue', 'crawler') }}</code> queue.</li>
                <li><strong style="color:var(--text-primary);">{{ $crawlInterval }}-day scheduler</strong> — The <code>crawl:dispatch</code> command runs every 6 hours, but only queues links not crawled in the last {{ $crawlInterval }} days.</li>
                <li><strong style="color:var(--text-primary);">Smart deduplication</strong> — Links already crawled within {{ $crawlInterval }} days are skipped unless <code>force_recrawl = true</code>.</li>
                <li><strong style="color:var(--text-primary);">Content indexing</strong> — Page title, h1, meta description, and body text are stored in <code>crawl_contents</code> with MySQL FULLTEXT indexing for fast search.</li>
                <li><strong style="color:var(--text-primary);">Blacklist filter</strong> — Domains in the blacklist table are automatically skipped (like Ahmia's <code>FilterBannedDomains</code>).</li>
                <li><strong style="color:var(--text-primary);">Tor proxy</strong> — All requests route through <code>{{ config('crawler.proxy') }}</code>.</li>
                <li><strong style="color:var(--text-primary);">Link extraction</strong> — All <code>&lt;a href&gt;</code> tags are extracted and stored in <code>discovered_links</code>, deduplicated per parent (cap: {{ number_format(config('crawler.max_links_per_page')) }}/page).</li>
                <li><strong style="color:var(--text-primary);">Audit logs</strong> — Every crawl attempt is recorded in <code>crawl_logs</code> with HTTP status, response time, and error details.</li>
                <li><strong style="color:var(--text-primary);">Run queue worker</strong>: <code>php artisan queue:work --queue=crawler --tries=3 --timeout=90</code></li>
                <li><strong style="color:var(--text-primary);">Manual cron</strong>: <code>* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1</code></li>
            </ul>
        </div>
    </div>

</x-app.layouts>
