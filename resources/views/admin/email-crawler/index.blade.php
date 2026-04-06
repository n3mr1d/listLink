<x-app.layouts title="Email Crawler — Dashboard">

<style>
/* ── Email Crawler Dashboard Styles ──────────────────────────────────── */
.ec-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}
.ec-page-header h1 {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin: 0;
}
.ec-page-header h1 i { color: #58a6ff; }
.ec-subtitle {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.3rem;
}

/* Stats grid */
.ec-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 0.8rem;
    margin-bottom: 1.5rem;
}
.ec-stat-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: 10px;
    padding: 0.9rem 1rem;
    text-align: center;
    transition: border-color 0.2s, transform 0.15s;
}
.ec-stat-card:hover {
    border-color: rgba(88,166,255,0.35);
    transform: translateY(-2px);
}
.ec-stat-value {
    font-size: 1.7rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
}
.ec-stat-label {
    font-size: 0.72rem;
    color: var(--text-muted);
    margin-top: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

/* Action panels */
.ec-panel-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.ec-panel {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    overflow: hidden;
}
.ec-panel-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255,255,255,0.02);
    border-bottom: 1px solid var(--border-light);
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--text-primary);
}
.ec-panel-header i { font-size: 0.9rem; }
.ec-panel-body { padding: 1rem; }

/* Form elements */
.ec-input {
    width: 100%;
    background: var(--bg-primary);
    border: 1px solid var(--border-light);
    border-radius: 7px;
    padding: 0.55rem 0.8rem;
    color: var(--text-primary);
    font-size: 0.85rem;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
}
.ec-input:focus {
    border-color: #58a6ff;
    box-shadow: 0 0 0 3px rgba(88,166,255,0.12);
}
.ec-textarea {
    min-height: 90px;
    resize: vertical;
    font-family: var(--font-mono);
    font-size: 0.8rem;
}
.ec-label {
    display: block;
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 0.35rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.ec-form-group { margin-bottom: 0.75rem; }

/* Buttons */
.ec-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1rem;
    border-radius: 7px;
    font-size: 0.83rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all 0.15s;
    white-space: nowrap;
}
.ec-btn-primary {
    background: rgba(88,166,255,0.18);
    color: #58a6ff;
    border-color: rgba(88,166,255,0.35);
}
.ec-btn-primary:hover { background: rgba(88,166,255,0.28); }
.ec-btn-success {
    background: rgba(63,185,80,0.15);
    color: #3fb950;
    border-color: rgba(63,185,80,0.3);
}
.ec-btn-success:hover { background: rgba(63,185,80,0.25); }
.ec-btn-danger {
    background: rgba(248,81,73,0.12);
    color: #f85149;
    border-color: rgba(248,81,73,0.3);
}
.ec-btn-danger:hover { background: rgba(248,81,73,0.22); }
.ec-btn-muted {
    background: rgba(139,148,158,0.1);
    color: var(--text-secondary);
    border-color: rgba(139,148,158,0.25);
}
.ec-btn-muted:hover { background: rgba(139,148,158,0.2); }
.ec-btn-sm {
    padding: 0.25rem 0.55rem;
    font-size: 0.75rem;
    gap: 0.3rem;
}
.ec-btn-full { width: 100%; justify-content: center; }

/* Filter bar */
.ec-filter-bar {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: 10px;
    padding: 0.85rem 1rem;
    margin-bottom: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    align-items: flex-end;
}
.ec-filter-item { display: flex; flex-direction: column; gap: 0.2rem; min-width: 130px; }
.ec-filter-item .ec-label { margin-bottom: 0; }
.ec-filter-select {
    background: var(--bg-primary);
    border: 1px solid var(--border-light);
    border-radius: 6px;
    padding: 0.45rem 0.7rem;
    color: var(--text-primary);
    font-size: 0.82rem;
    outline: none;
    cursor: pointer;
}
.ec-filter-search {
    flex: 1;
    min-width: 200px;
}

/* Email table */
.ec-table-wrap {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    overflow: hidden;
}
.ec-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border-light);
    flex-wrap: wrap;
    gap: 0.5rem;
}
.ec-table-title {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.ec-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.ec-table th {
    padding: 0.55rem 0.9rem;
    text-align: left;
    color: var(--text-muted);
    font-weight: 600;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    background: rgba(255,255,255,0.02);
    border-bottom: 1px solid var(--border-light);
    white-space: nowrap;
}
.ec-table td {
    padding: 0.6rem 0.9rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    vertical-align: middle;
}
.ec-table tr:last-child td { border-bottom: none; }
.ec-table tr:hover td { background: rgba(255,255,255,0.02); }

/* Badge */
.ec-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.ec-badge-active   { background: rgba(63,185,80,0.15); color: #3fb950; border: 1px solid rgba(63,185,80,0.3); }
.ec-badge-invalid  { background: rgba(248,81,73,0.12); color: #f85149; border: 1px solid rgba(248,81,73,0.3); }
.ec-badge-unsub    { background: rgba(226,183,20,0.12); color: #e3b341; border: 1px solid rgba(226,183,20,0.3); }
.ec-badge-auto     { background: rgba(88,166,255,0.12); color: #58a6ff; border: 1px solid rgba(88,166,255,0.3); }
.ec-badge-manual   { background: rgba(188,140,255,0.12); color: #bc8cff; border: 1px solid rgba(188,140,255,0.3); }
.ec-badge-exported { background: rgba(63,185,80,0.1); color: #3fb950; }

/* Domain bar */
.ec-domain-bars { display: flex; flex-direction: column; gap: 0.35rem; }
.ec-domain-row  { display: flex; align-items: center; gap: 0.6rem; font-size: 0.78rem; }
.ec-domain-name { color: var(--text-secondary); min-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ec-domain-bar  { flex: 1; height: 5px; background: var(--bg-primary); border-radius: 3px; overflow: hidden; }
.ec-domain-fill { height: 100%; background: linear-gradient(90deg, #58a6ff, #bc8cff); border-radius: 3px; transition: width 0.4s; }
.ec-domain-cnt  { color: var(--text-muted); font-weight: 700; min-width: 30px; text-align: right; }

/* Empty state */
.ec-empty {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--text-muted);
    font-size: 0.9rem;
}
.ec-empty i { font-size: 2.5rem; display: block; margin-bottom: 0.8rem; opacity: 0.4; }

/* Proxy toggle */
.ec-check-row {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 0.4rem;
}
.ec-check-row input[type="checkbox"] { accent-color: #58a6ff; width: 14px; height: 14px; }

/* Alert */
.ec-alert {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}
.ec-alert-success { background: rgba(63,185,80,0.1); border: 1px solid rgba(63,185,80,0.3); color: #3fb950; }
.ec-alert-error   { background: rgba(248,81,73,0.1); border: 1px solid rgba(248,81,73,0.3); color: #f85149; }

/* Export bar */
.ec-export-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}
</style>

<!-- Page Header -->
<div class="ec-page-header">
    <div>
        <h1><i class="fa-solid fa-envelope-open-text"></i> Email Crawler</h1>
        <p class="ec-subtitle">Collect, validate &amp; export publicly available emails from websites — ethically.</p>
    </div>
</div>

<!-- Admin Nav -->
<nav class="admin-nav">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a href="{{ route('admin.links') }}">Links</a>
    <a href="{{ route('admin.ads') }}">Ads</a>
    <a href="{{ route('admin.uptime-logs') }}">Uptime Logs</a>
    <a href="{{ route('admin.blacklist') }}">Blacklist</a>
    <a href="{{ route('admin.crawler.index') }}">Crawler</a>
    <a href="{{ route('admin.email-crawler.index') }}" class="active">Email Crawler</a>
</nav>

<!-- Flash Messages -->
@if(session('success'))
    <div class="ec-alert ec-alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="ec-alert ec-alert-error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="ec-alert ec-alert-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>@foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach</div>
    </div>
@endif

<!-- Stats Grid -->
<div class="ec-stat-grid">
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#58a6ff;">{{ number_format($stats['total']) }}</div>
        <div class="ec-stat-label">Total Emails</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#3fb950;">{{ number_format($stats['active']) }}</div>
        <div class="ec-stat-label">Active</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#f85149;">{{ number_format($stats['invalid']) }}</div>
        <div class="ec-stat-label">Invalid</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#bc8cff;">{{ number_format($stats['domains']) }}</div>
        <div class="ec-stat-label">Domains</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#e3b341;">{{ number_format($stats['not_exported']) }}</div>
        <div class="ec-stat-label">Not Exported</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#3fb950;">{{ number_format($stats['exported']) }}</div>
        <div class="ec-stat-label">Exported</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#58a6ff;">{{ number_format($stats['auto_crawl']) }}</div>
        <div class="ec-stat-label">Auto-Crawled</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#bc8cff;">{{ number_format($stats['manual']) }}</div>
        <div class="ec-stat-label">Manual</div>
    </div>
    <div class="ec-stat-card">
        <div class="ec-stat-value" style="color:#e3b341;">{{ number_format($stats['today']) }}</div>
        <div class="ec-stat-label">Today</div>
    </div>
</div>

<!-- Action Panels -->
<div class="ec-panel-grid">

    {{-- Scan Single URL --}}
    <div class="ec-panel">
        <div class="ec-panel-header">
            <i class="fa-solid fa-magnifying-glass" style="color:#58a6ff;"></i> Scan Single URL
        </div>
        <div class="ec-panel-body">
            <form method="POST" action="{{ route('admin.email-crawler.scan-url') }}">
                @csrf
                <div class="ec-form-group">
                    <label class="ec-label">Target URL</label>
                    <input type="url" name="url" class="ec-input" placeholder="https://example.com/contact" required>
                </div>
                <div class="ec-check-row">
                    <input type="checkbox" name="use_proxy" value="1" id="proxy-single">
                    <label for="proxy-single">Route through Tor proxy (for .onion or restricted sites)</label>
                </div>
                <div style="margin-top:0.75rem;">
                    <button type="submit" class="ec-btn ec-btn-primary ec-btn-full">
                        <i class="fa-solid fa-radar"></i> Scan for Emails
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk URL Scan --}}
    <div class="ec-panel">
        <div class="ec-panel-header">
            <i class="fa-solid fa-layer-group" style="color:#bc8cff;"></i> Bulk URL Scan
        </div>
        <div class="ec-panel-body">
            <form method="POST" action="{{ route('admin.email-crawler.scan-bulk') }}">
                @csrf
                <div class="ec-form-group">
                    <label class="ec-label">URLs (one per line or comma-separated)</label>
                    <textarea name="urls" class="ec-input ec-textarea" placeholder="https://site1.com&#10;https://site2.com/team&#10;https://site3.org/about"></textarea>
                </div>
                <div class="ec-check-row">
                    <input type="checkbox" name="use_proxy" value="1" id="proxy-bulk">
                    <label for="proxy-bulk">Use Tor proxy</label>
                </div>
                <div style="margin-top:0.75rem;">
                    <button type="submit" class="ec-btn ec-btn-muted ec-btn-full" style="color:#bc8cff;border-color:rgba(188,140,255,0.3);background:rgba(188,140,255,0.1);">
                        <i class="fa-solid fa-bolt"></i> Queue Bulk Scan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Manual Add --}}
    <div class="ec-panel">
        <div class="ec-panel-header">
            <i class="fa-solid fa-plus-circle" style="color:#3fb950;"></i> Add Email Manually
        </div>
        <div class="ec-panel-body">
            <form method="POST" action="{{ route('admin.email-crawler.manual-add') }}">
                @csrf
                <div class="ec-form-group">
                    <label class="ec-label">Email Address</label>
                    <input type="email" name="email" class="ec-input" placeholder="user@domain.com" required>
                </div>
                <div class="ec-form-group">
                    <label class="ec-label">Source URL (optional)</label>
                    <input type="url" name="source_url" class="ec-input" placeholder="https://...">
                </div>
                <div class="ec-form-group">
                    <label class="ec-label">Notes (optional)</label>
                    <input type="text" name="notes" class="ec-input" placeholder="How you found this email">
                </div>
                <button type="submit" class="ec-btn ec-btn-success ec-btn-full">
                    <i class="fa-solid fa-circle-plus"></i> Add Email
                </button>
            </form>
        </div>
    </div>

    {{-- Export & Manage --}}
    <div class="ec-panel">
        <div class="ec-panel-header">
            <i class="fa-solid fa-file-csv" style="color:#e3b341;"></i> Export &amp; Manage
        </div>
        <div class="ec-panel-body">
            <!-- Export -->
            <p style="font-size:0.8rem;color:var(--text-muted);margin:0 0 0.75rem;">Export emails as UTF-8 CSV (Excel-compatible with BOM).</p>
            <div style="display:flex;flex-direction:column;gap:0.5rem;">
                <a href="{{ route('admin.email-crawler.export', ['status'=>'active','exported'=>'no','mark_exported'=>1]) }}"
                   class="ec-btn ec-btn-full" style="background:rgba(226,183,20,0.12);color:#e3b341;border-color:rgba(226,183,20,0.3);">
                    <i class="fa-solid fa-download"></i> Export Active (Not Yet Exported)
                </a>
                <a href="{{ route('admin.email-crawler.export', ['status'=>'all']) }}"
                   class="ec-btn ec-btn-muted ec-btn-full">
                    <i class="fa-solid fa-file-export"></i> Export All Emails
                </a>
            </div>

            <hr style="border-color:var(--border-light);margin:0.9rem 0;">

            <!-- Reset exported flag -->
            <form method="POST" action="{{ route('admin.email-crawler.reset-exported') }}">
                @csrf
                <button type="submit" class="ec-btn ec-btn-muted ec-btn-full"
                    onclick="return confirm('Reset exported flag on all emails?')">
                    <i class="fa-solid fa-rotate-left"></i> Reset Exported Flag ({{ number_format($stats['exported']) }})
                </button>
            </form>

            <hr style="border-color:var(--border-light);margin:0.9rem 0;">

            <!-- Bulk Delete -->
            <form method="POST" action="{{ route('admin.email-crawler.bulk-delete') }}">
                @csrf
                <div style="display:flex;gap:0.4rem;flex-wrap:wrap;margin-bottom:0.5rem;">
                    <select name="status" class="ec-filter-select" style="flex:1;">
                        <option value="">All Statuses</option>
                        <option value="invalid">Invalid only</option>
                        <option value="unsubscribed">Unsubscribed only</option>
                    </select>
                </div>
                <button type="submit" class="ec-btn ec-btn-danger ec-btn-full"
                    onclick="return confirm('⚠️ Delete all matching emails? This cannot be undone.')">
                    <i class="fa-solid fa-trash"></i> Bulk Delete
                </button>
            </form>
        </div>
    </div>

</div>

{{-- ── Crawl from Database Panel ────────────────────────────────────────────── --}}
<div class="ec-panel" style="margin-bottom:1.5rem;border-color:rgba(226,183,20,0.3);">
    <div class="ec-panel-header" style="background:rgba(226,183,20,0.05);border-bottom-color:rgba(226,183,20,0.25);">
        <i class="fa-solid fa-database" style="color:#e3b341;"></i>
        Crawl from Database
        <span style="margin-left:auto;font-size:0.72rem;color:var(--text-muted);font-weight:400;">
            Mine emails from links already in your database
        </span>
    </div>
    <div class="ec-panel-body">
        <form method="POST" action="{{ route('admin.email-crawler.crawl-from-db') }}">
            @csrf
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0.75rem;align-items:end;">
                <div class="ec-form-group" style="margin:0;">
                    <label class="ec-label">Data Source</label>
                    <select name="source" class="ec-input" style="padding:0.52rem 0.8rem;cursor:pointer;">
                        <option value="links">Links Table only (registered URLs)</option>
                        <option value="discovered">Discovered Links only (crawled URLs)</option>
                        <option value="both" selected>Both — Links + Discovered Links</option>
                    </select>
                </div>
                <div class="ec-form-group" style="margin:0;">
                    <label class="ec-label">Max URLs to Queue</label>
                    <input type="number" name="limit" class="ec-input" value="500" min="1" max="5000"
                           placeholder="500" style="padding:0.52rem 0.8rem;">
                </div>
                <div style="display:flex;flex-direction:column;gap:0.4rem;">
                    <div class="ec-check-row" style="margin:0;">
                        <input type="checkbox" name="use_proxy" value="1" id="proxy-db">
                        <label for="proxy-db">Use Tor proxy (required for .onion URLs)</label>
                    </div>
                    <button type="submit" class="ec-btn ec-btn-full"
                        style="background:rgba(226,183,20,0.12);color:#e3b341;border-color:rgba(226,183,20,0.35);"
                        onclick="return confirm('Queue email scan jobs for URLs in the database? This may take a while.')">
                        <i class="fa-solid fa-database"></i> Start DB Crawl
                    </button>
                </div>
            </div>
            <p style="margin:0.6rem 0 0;font-size:0.76rem;color:var(--text-muted);">
                <i class="fa-solid fa-circle-info" style="color:#e3b341;"></i>
                .onion URLs are automatically skipped unless Tor proxy is enabled.
                Duplicate URLs are deduplicated before queuing.
                Run queue worker: <code>php artisan queue:work --queue=email-crawler</code>
            </p>
        </form>
    </div>
</div>

<!-- Top Domains -->
@if($topDomains->count() > 0)
<div class="ec-panel" style="margin-bottom:1.5rem;">
    <div class="ec-panel-header">
        <i class="fa-solid fa-chart-bar" style="color:#58a6ff;"></i> Top Source Domains
    </div>
    <div class="ec-panel-body">
        @php $maxCnt = $topDomains->first()?->cnt ?? 1; @endphp
        <div class="ec-domain-bars">
            @foreach($topDomains as $d)
            <div class="ec-domain-row">
                <span class="ec-domain-name" title="{{ $d->source_domain }}">
                    <a href="{{ route('admin.email-crawler.index', ['domain' => $d->source_domain]) }}"
                       style="color:var(--text-secondary);text-decoration:none;">{{ $d->source_domain }}</a>
                </span>
                <div class="ec-domain-bar">
                    <div class="ec-domain-fill" style="width:{{ ($d->cnt / $maxCnt) * 100 }}%"></div>
                </div>
                <span class="ec-domain-cnt">{{ number_format($d->cnt) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Filter Bar -->
<form method="GET" action="{{ route('admin.email-crawler.index') }}" id="filter-form">
    <div class="ec-filter-bar">
        <div class="ec-filter-item ec-filter-search">
            <label class="ec-label">Search</label>
            <input type="text" name="q" class="ec-input" placeholder="email, domain, title…" value="{{ $search }}">
        </div>
        <div class="ec-filter-item">
            <label class="ec-label">Status</label>
            <select name="status" class="ec-filter-select" onchange="this.form.submit()">
                <option value="all" {{ $status==='all'?'selected':'' }}>All</option>
                <option value="active" {{ $status==='active'?'selected':'' }}>Active</option>
                <option value="invalid" {{ $status==='invalid'?'selected':'' }}>Invalid</option>
                <option value="unsubscribed" {{ $status==='unsubscribed'?'selected':'' }}>Unsubscribed</option>
            </select>
        </div>
        <div class="ec-filter-item">
            <label class="ec-label">Source</label>
            <select name="source" class="ec-filter-select" onchange="this.form.submit()">
                <option value="all" {{ $source==='all'?'selected':'' }}>All</option>
                <option value="auto_crawl" {{ $source==='auto_crawl'?'selected':'' }}>Auto-Crawl</option>
                <option value="manual" {{ $source==='manual'?'selected':'' }}>Manual</option>
            </select>
        </div>
        <div class="ec-filter-item">
            <label class="ec-label">Exported</label>
            <select name="exported" class="ec-filter-select" onchange="this.form.submit()">
                <option value="all" {{ $exported==='all'?'selected':'' }}>All</option>
                <option value="no" {{ $exported==='no'?'selected':'' }}>Not Exported</option>
                <option value="yes" {{ $exported==='yes'?'selected':'' }}>Exported</option>
            </select>
        </div>
        @if($domain)
            <input type="hidden" name="domain" value="{{ $domain }}">
        @endif
        <div class="ec-filter-item" style="justify-content:flex-end;">
            <label class="ec-label" style="opacity:0;">.</label>
            <div style="display:flex;gap:0.4rem;">
                <button type="submit" class="ec-btn ec-btn-primary" style="padding:0.45rem 0.8rem;">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="{{ route('admin.email-crawler.index') }}" class="ec-btn ec-btn-muted" style="padding:0.45rem 0.8rem;">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </div>
    </div>
</form>

<!-- Email Table -->
<div class="ec-table-wrap">
    <div class="ec-table-header">
        <div class="ec-table-title">
            <i class="fa-solid fa-table-list" style="color:#58a6ff;"></i>
            Email Records
            <span style="font-size:0.78rem;color:var(--text-muted);font-weight:400;">({{ number_format($emails->total()) }} total)</span>
        </div>
        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            <a href="{{ route('admin.email-crawler.export', array_merge(request()->query(), ['mark_exported'=>1])) }}"
               class="ec-btn ec-btn-sm" style="background:rgba(226,183,20,0.1);color:#e3b341;border-color:rgba(226,183,20,0.3);">
                <i class="fa-solid fa-download"></i> Export Filtered
            </a>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="ec-table">
            <thead>
                <tr>
                    <th style="min-width:220px;">Email</th>
                    <th>Domain</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>First Seen</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($emails as $email)
                <tr>
                    {{-- Email --}}
                    <td>
                        <div style="font-family:var(--font-mono);font-size:0.8rem;color:var(--text-primary);font-weight:500;">
                            {{ $email->email }}
                        </div>
                        @if($email->page_title)
                            <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.1rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:250px;" title="{{ $email->page_title }}">
                                {{ Str::limit($email->page_title, 45) }}
                            </div>
                        @endif
                        @if($email->exported)
                            <span class="ec-badge ec-badge-exported" style="font-size:0.65rem;margin-top:0.15rem;">✓ exported</span>
                        @endif
                    </td>

                    {{-- Domain --}}
                    <td style="font-size:0.78rem;color:var(--text-muted);">
                        @if($email->source_domain)
                            <a href="{{ route('admin.email-crawler.index', ['domain'=>$email->source_domain]) }}"
                               style="color:var(--accent-cyan, #39d0d8);text-decoration:none;">
                                {{ $email->source_domain }}
                            </a>
                        @else
                            <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>

                    {{-- Source URL --}}
                    <td style="max-width:180px;">
                        @if($email->source_url)
                            <a href="{{ $email->source_url }}" target="_blank" rel="noopener noreferrer"
                               style="font-family:var(--font-mono);font-size:0.7rem;color:var(--text-muted);text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;"
                               title="{{ $email->source_url }}">
                                {{ Str::limit($email->source_url, 32) }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.6rem;"></i>
                            </a>
                        @else
                            <span style="color:var(--text-muted);font-size:0.78rem;">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>
                        @php
                            $stClass = match($email->status) {
                                'active'       => 'ec-badge-active',
                                'invalid'      => 'ec-badge-invalid',
                                'unsubscribed' => 'ec-badge-unsub',
                                default        => 'ec-badge-muted',
                            };
                        @endphp
                        <span class="ec-badge {{ $stClass }}">{{ $email->status }}</span>
                    </td>

                    {{-- Source Type --}}
                    <td>
                        <span class="ec-badge {{ $email->source_type === 'manual' ? 'ec-badge-manual' : 'ec-badge-auto' }}">
                            {{ $email->source_type === 'manual' ? 'manual' : 'auto' }}
                        </span>
                    </td>

                    {{-- First Seen --}}
                    <td style="color:var(--text-muted);font-size:0.77rem;white-space:nowrap;">
                        {{ $email->first_seen_at?->diffForHumans() }}
                    </td>

                    {{-- Actions --}}
                    <td style="text-align:right;">
                        <div style="display:flex;gap:0.3rem;justify-content:flex-end;flex-wrap:wrap;">

                            {{-- Status dropdown --}}
                            <form method="POST" action="{{ route('admin.email-crawler.update-status', $email->id) }}">
                                @csrf
                                <select name="status" class="ec-filter-select" style="padding:0.2rem 0.45rem;font-size:0.72rem;"
                                    onchange="this.form.submit()">
                                    <option value="active"       {{ $email->status==='active'?'selected':'' }}>Active</option>
                                    <option value="invalid"      {{ $email->status==='invalid'?'selected':'' }}>Invalid</option>
                                    <option value="unsubscribed" {{ $email->status==='unsubscribed'?'selected':'' }}>Unsub</option>
                                </select>
                            </form>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.email-crawler.delete', $email->id) }}">
                                @csrf
                                <button type="submit" class="ec-btn ec-btn-sm ec-btn-danger"
                                    title="Delete" onclick="return confirm('Delete {{ $email->email }}?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="ec-empty">
                                <i class="fa-solid fa-inbox"></i>
                                No emails found. Start by scanning a URL or adding one manually.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($emails->hasPages())
        <div style="padding:0.9rem 1rem;border-top:1px solid var(--border-light);">
            {{ $emails->links() }}
        </div>
    @endif
</div>

<!-- Info Box -->
<div class="ec-panel" style="margin-top:1.5rem;">
    <div class="ec-panel-header">
        <i class="fa-solid fa-circle-info" style="color:#58a6ff;"></i> How It Works
    </div>
    <div class="ec-panel-body" style="font-size:0.83rem;color:var(--text-muted);line-height:1.7;">
        <ul style="padding-left:1.2rem;margin:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:0.2rem 1.5rem;">
            <li><strong style="color:var(--text-primary);">Queue-based</strong> — Each URL is dispatched as an <code>EmailCrawlJob</code> on the <code>email-crawler</code> queue. Run: <code>php artisan queue:work --queue=email-crawler</code></li>
            <li><strong style="color:var(--text-primary);">Dual extraction</strong> — Scans both <code>mailto:</code> links and regex-matched emails in visible page text.</li>
            <li><strong style="color:var(--text-primary);">Smart validation</strong> — Filters disposable domains, noreply prefixes, and malformed addresses automatically.</li>
            <li><strong style="color:var(--text-primary);">Deduplication</strong> — Emails are unique at DB level; re-seeing an email updates <code>last_seen_at</code>, never creates a duplicate.</li>
            <li><strong style="color:var(--text-primary);">CSV Export</strong> — UTF-8 BOM CSV for Excel compatibility, with optional mark-as-exported tracking.</li>
            <li><strong style="color:var(--text-primary);">Tor support</strong> — Optionally route requests through your configured Tor proxy (<code>{{ config('crawler.proxy', 'socks5h://127.0.0.1:9050') }}</code>).</li>
            <li><strong style="color:var(--text-primary);">Ethical use only</strong> — Only index publicly visible emails; honor unsubscribe requests; never scrape behind authentication.</li>
        </ul>
    </div>
</div>

</x-app.layouts>
