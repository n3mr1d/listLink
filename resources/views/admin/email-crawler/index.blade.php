<x-app.layouts title="Admin - Email Harvesting">

    @include('admin._nav')

    <div class="admin-header">
        <h1>Email Harvesting</h1>
        <p>Automated collection and validation of public-facing communication nodes.</p>
    </div>

    @if(session('success'))
        <div style="margin-bottom:1rem;padding:.6rem 1rem;border-radius:.4rem;border:1px solid rgba(74,222,128,.2);color:#4ade80;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(90px,1fr));gap:.4rem;margin-bottom:1.25rem;">
        @foreach([
            ['Verified', $stats['total']],
            ['Active', $stats['active']],
            ['Invalid', $stats['invalid']],
            ['Domains', $stats['domains']],
            ['New', $stats['not_exported']],
            ['Exported', $stats['exported']],
            ['Auto', $stats['auto_crawl']],
            ['Manual', $stats['manual']],
            ['Today', $stats['today']],
        ] as $stat)
            <div style="border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .6rem;text-align:center;">
                <div style="font-size:.45rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--color-gh-dim);margin-bottom:.2rem;">{{ $stat[0] }}</div>
                <div style="font-size:1rem;font-weight:900;color:#fff;">{{ number_format($stat[1]) }}</div>
            </div>
        @endforeach
    </div>

    {{-- Operation Panels --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;margin-bottom:1.5rem;">
        {{-- Targeted Scan --}}
        <div class="panel" style="margin-bottom:0;">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                Targeted Scan
            </div>
            <div style="padding:.75rem 1rem;">
                <form method="POST" action="{{ route('admin.email-crawler.scan-url') }}">
                    @csrf
                    <input type="url" name="url" placeholder="https://target.onion/contact" required
                        style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.45rem .6rem;font-size:.72rem;color:#fff;outline:none;margin-bottom:.4rem;">
                    <label style="display:flex;align-items:center;gap:.35rem;margin-bottom:.5rem;cursor:pointer;font-size:.6rem;font-weight:700;color:var(--color-gh-dim);">
                        <input type="checkbox" name="use_proxy" value="1"> Route through Tor
                    </label>
                    <button type="submit" style="width:100%;padding:.45rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.35rem;font-size:.6rem;font-weight:900;text-transform:uppercase;cursor:pointer;">Scan</button>
                </form>
            </div>
        </div>

        {{-- Batch Process --}}
        <div class="panel" style="margin-bottom:0;">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2.5"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/></svg>
                Batch Process
            </div>
            <div style="padding:.75rem 1rem;">
                <form method="POST" action="{{ route('admin.email-crawler.scan-bulk') }}">
                    @csrf
                    <textarea name="urls" placeholder="Enter multiple URLs..." rows="3"
                        style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.45rem .6rem;font-size:.72rem;color:#fff;outline:none;resize:none;font-family:monospace;margin-bottom:.4rem;"></textarea>
                    <label style="display:flex;align-items:center;gap:.35rem;margin-bottom:.5rem;cursor:pointer;font-size:.6rem;font-weight:700;color:var(--color-gh-dim);">
                        <input type="checkbox" name="use_proxy" value="1"> Force Proxy
                    </label>
                    <button type="submit" style="width:100%;padding:.45rem;border:1px solid rgba(168,85,247,.3);background:rgba(168,85,247,.08);color:#a855f7;border-radius:.35rem;font-size:.6rem;font-weight:900;text-transform:uppercase;cursor:pointer;">Queue Batch</button>
                </form>
            </div>
        </div>

        {{-- Manual Add --}}
        <div class="panel" style="margin-bottom:0;">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Direct Ingestion
            </div>
            <div style="padding:.75rem 1rem;">
                <form method="POST" action="{{ route('admin.email-crawler.manual-add') }}">
                    @csrf
                    <input type="email" name="email" placeholder="alias@domain.onion" required
                        style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.45rem .6rem;font-size:.72rem;color:#fff;outline:none;margin-bottom:.3rem;">
                    <input type="url" name="source_url" placeholder="Source URL (optional)"
                        style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.45rem .6rem;font-size:.72rem;color:#fff;outline:none;margin-bottom:.5rem;">
                    <button type="submit" style="width:100%;padding:.45rem;border:1px solid rgba(74,222,128,.3);background:rgba(74,222,128,.08);color:#4ade80;border-radius:.35rem;font-size:.6rem;font-weight:900;text-transform:uppercase;cursor:pointer;">Commit</button>
                </form>
            </div>
        </div>

        {{-- Export --}}
        <div class="panel" style="margin-bottom:0;">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fb923c" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export
            </div>
            <div style="padding:.75rem 1rem;">
                <a href="{{ route('admin.email-crawler.export', ['status'=>'active','exported'=>'no','mark_exported'=>1]) }}" style="display:block;width:100%;text-align:center;padding:.45rem;background:#fb923c;color:#0d1117;border-radius:.35rem;font-size:.6rem;font-weight:900;text-transform:uppercase;text-decoration:none;margin-bottom:.35rem;">New Nodes</a>
                <a href="{{ route('admin.email-crawler.export', ['status'=>'all']) }}" style="display:block;width:100%;text-align:center;padding:.45rem;border:1px solid var(--color-gh-border);border-radius:.35rem;font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;text-decoration:none;margin-bottom:.5rem;">Full Database</a>
                <form method="POST" action="{{ route('admin.email-crawler.bulk-delete') }}" style="display:flex;gap:.3rem;">
                    @csrf
                    <select name="status" style="flex:1;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.3rem;padding:.3rem .5rem;font-size:.6rem;color:var(--color-gh-dim);outline:none;text-transform:uppercase;">
                        <option value="">All</option>
                        <option value="invalid">Invalid</option>
                    </select>
                    <button type="submit" class="btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.2);" onclick="return confirm('Purge data?')" title="Delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- DB Mining --}}
    <div style="border:1px solid rgba(251,146,60,.2);border-radius:.5rem;padding:1rem;margin-bottom:1.5rem;">
        <div style="font-size:.7rem;font-weight:900;color:#fff;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.3rem;">Internal Data Mining</div>
        <p style="font-size:.65rem;color:var(--color-gh-dim);line-height:1.5;margin:0 0 .75rem;">Traverse your database to extract communication identifiers. Runs asynchronously.</p>
        <form method="POST" action="{{ route('admin.email-crawler.crawl-from-db') }}" style="display:flex;gap:.5rem;align-items:end;">
            @csrf
            <div style="flex:1;">
                <label style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.2rem;">Scope</label>
                <select name="source" style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.4rem .6rem;font-size:.65rem;color:#fff;outline:none;">
                    <option value="both">Global (Links + Discovery)</option>
                    <option value="links">Verified Directory</option>
                    <option value="discovered">Scraped Inventory</option>
                </select>
            </div>
            <div style="width:80px;">
                <label style="font-size:.5rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.2rem;">Limit</label>
                <input type="number" name="limit" value="500" style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.4rem .6rem;font-size:.65rem;color:#fff;outline:none;font-family:monospace;">
            </div>
            <button type="submit" style="padding:.45rem 1rem;background:#fb923c;color:#0d1117;border:none;border-radius:.35rem;font-size:.6rem;font-weight:900;text-transform:uppercase;cursor:pointer;white-space:nowrap;">Mine</button>
        </form>
    </div>

    {{-- Records Table --}}
    <div class="panel">
        <div class="panel-head" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.4rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Record Stream
            </div>
            <form method="GET" action="{{ route('admin.email-crawler.index') }}" style="display:flex;gap:.3rem;align-items:center;">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search..."
                    style="background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.3rem;padding:.3rem .5rem;font-size:.65rem;color:#fff;outline:none;width:150px;">
                <select name="status" onchange="this.form.submit()"
                    style="background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.3rem;padding:.3rem .5rem;font-size:.6rem;color:var(--color-gh-dim);outline:none;text-transform:uppercase;">
                    @foreach(['all' => 'All', 'active' => 'Active', 'invalid' => 'Invalid'] as $k => $v)
                        <option value="{{ $k }}" {{ $status === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th class="hide-mobile">Source</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr>
                            <td>
                                <div style="font-size:.78rem;font-weight:700;color:#fff;">{{ $email->email }}</div>
                                <div style="display:flex;align-items:center;gap:.3rem;margin-top:.15rem;">
                                    @if($email->exported)
                                        <span style="font-size:.5rem;font-weight:800;color:#4ade80;border:1px solid rgba(74,222,128,.15);padding:.05rem .25rem;border-radius:.2rem;text-transform:uppercase;">Exported</span>
                                    @endif
                                    <span style="font-size:.55rem;color:var(--color-gh-dim);max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $email->page_title ?: 'Untitled' }}</span>
                                </div>
                            </td>
                            <td class="hide-mobile">
                                @if($email->source_domain)
                                    <a href="{{ route('admin.email-crawler.index', ['domain'=>$email->source_domain]) }}" style="font-size:.65rem;color:var(--color-gh-accent);text-decoration:none;">{{ $email->source_domain }}</a>
                                @endif
                                <div style="font-size:.5rem;color:var(--color-gh-dim);text-transform:uppercase;opacity:.5;">{{ $email->source_type }}</div>
                            </td>
                            <td style="text-align:center;">
                                <form method="POST" action="{{ route('admin.email-crawler.update-status', $email->id) }}" style="display:inline;">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()"
                                        style="background:transparent;border:1px solid var(--color-gh-border);border-radius:.25rem;padding:.15rem .3rem;font-size:.55rem;font-weight:800;text-transform:uppercase;color:{{ $email->status === 'active' ? '#4ade80' : ($email->status === 'invalid' ? '#f87171' : '#fb923c') }};outline:none;cursor:pointer;">
                                        <option value="active" {{ $email->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="invalid" {{ $email->status === 'invalid' ? 'selected' : '' }}>Invalid</option>
                                        <option value="unsubscribed" {{ $email->status === 'unsubscribed' ? 'selected' : '' }}>Unsub</option>
                                    </select>
                                </form>
                            </td>
                            <td style="text-align:right;">
                                <form method="POST" action="{{ route('admin.email-crawler.delete', $email->id) }}" style="display:inline;" onclick="return confirm('Delete?')">
                                    @csrf
                                    <button type="submit" class="btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.2);" title="Delete">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    <p>No records detected.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($emails->hasPages())
            <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                {{ $emails->links('pagination.simple') }}
            </div>
        @endif
    </div>

    {{-- Footer Notes --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1rem;">
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.75rem 1rem;">
            <div style="font-size:.6rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;display:flex;align-items:center;gap:.3rem;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Guidelines
            </div>
            <div style="font-size:.6rem;color:var(--color-gh-dim);line-height:1.6;">
                <div style="margin-bottom:.25rem;">/ Only extract publicly visible nodes</div>
                <div style="margin-bottom:.25rem;">/ Deduplication applied on ingestion</div>
                <div>/ Tor proxy required for .onion endpoints</div>
            </div>
        </div>
        <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.75rem 1rem;">
            <div style="font-size:.6rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;display:flex;align-items:center;gap:.3rem;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
                Queue Worker
            </div>
            <code style="font-size:.65rem;color:var(--color-gh-accent);display:block;background:rgba(0,0,0,.3);border:1px solid var(--color-gh-border);padding:.4rem .6rem;border-radius:.35rem;">php artisan queue:work --queue=email-crawler</code>
            <p style="font-size:.55rem;color:var(--color-gh-dim);font-style:italic;margin:.3rem 0 0;">Run under Supervisor for 24/7 collection.</p>
        </div>
    </div>

</x-app.layouts>
