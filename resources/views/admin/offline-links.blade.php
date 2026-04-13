<x-app.layouts title="Admin - Offline Nodes">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.3rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                <span style="font-size:.6rem;font-weight:800;color:#f87171;text-transform:uppercase;letter-spacing:.12em;">Dead Node Registry</span>
            </div>
            <h1>Offline Nodes</h1>
            <p>All .onion services currently unreachable. Edit metadata or trigger re-crawl to verify.</p>
        </div>
        <form action="{{ route('admin.links.bulk-enrich') }}" method="POST" onsubmit="return confirm('Queue all offline nodes for re-crawl?')">
            @csrf
            <button type="submit" style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);color:#f87171;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0115-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 01-15 6.7L3 16"/></svg>
                Re-Crawl All Offline
            </button>
        </form>
    </div>

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:.6rem;margin-bottom:1.5rem;">
        @foreach([
            ['Total Offline', number_format($stats['total_offline']), '#f87171'],
            ['Registered', number_format($stats['offline_registered']), '#fb923c'],
            ['Anonymous', number_format($stats['offline_anonymous']), '#a78bfa'],
            ['Never Crawled', number_format($stats['never_crawled']), '#58a6ff'],
        ] as $stat)
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:.85rem 1rem;">
                <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;">{{ $stat[0] }}</span>
                <span style="font-size:1.4rem;font-weight:900;color:{{ $stat[2] }};line-height:1;">{{ $stat[1] }}</span>
            </div>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.offline-links') }}" style="margin-bottom:1.25rem;">
        <div style="display:flex;align-items:center;border:1px solid var(--color-gh-border);border-radius:.4rem;overflow:hidden;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="2.5" style="margin-left:.75rem;flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="q" value="{{ $search }}" placeholder="Filter by URL or title..."
                style="flex:1;background:transparent;border:none;color:#fff;padding:.65rem;font-size:.82rem;outline:none;">
            @if($search)
                <a href="{{ route('admin.offline-links') }}" style="color:var(--color-gh-dim);padding:.65rem;font-size:.7rem;text-decoration:none;">Clear</a>
            @endif
        </div>
    </form>

    @if ($offlineLinks->count() > 0)
        <div class="panel">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Node Identity</th>
                            <th class="hide-mobile">Onion Address</th>
                            <th class="hide-mobile">Last Crawled</th>
                            <th>Source</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($offlineLinks as $link)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.5rem;">
                                        <div style="width:6px;height:6px;border-radius:50%;background:#f87171;flex-shrink:0;"></div>
                                        <div>
                                            <a href="{{ route('link.show', $link->slug) }}" style="font-size:.8rem;font-weight:700;color:#fff;text-decoration:none;">{{ Str::limit($link->title, 40) }}</a>
                                            <div style="font-size:.55rem;color:var(--color-gh-dim);margin-top:.1rem;">
                                                {{ $link->category->label() }}
                                                @if($link->is_duplicate)
                                                    · <span style="color:#fb923c;">Duplicate</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-family:monospace;font-size:.62rem;color:var(--color-gh-dim);display:block;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $link->url }}">{{ $link->url }}</span>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size:.62rem;color:var(--color-gh-dim);">{{ $link->last_crawled_at ? $link->last_crawled_at->diffForHumans() : 'Never' }}</span>
                                </td>
                                <td>
                                    @if ($link->user_id)
                                        <span style="font-size:.55rem;font-weight:800;color:#4ade80;text-transform:uppercase;">Dir</span>
                                    @else
                                        <span style="font-size:.55rem;font-weight:800;color:#58a6ff;text-transform:uppercase;">Crawl</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex;justify-content:flex-end;gap:.3rem;">
                                        <a href="{{ route('admin.links.edit', $link->id) }}" class="btn-sm" style="color:var(--color-gh-accent);" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.links.enrich', $link->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="color:#4ade80;border-color:rgba(74,222,128,.2);" title="Re-Crawl">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0115-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 01-15 6.7L3 16"/></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.links.delete', $link->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this offline node permanently?')">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.2);" title="Delete">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($offlineLinks->hasPages())
                <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                    {{ $offlineLinks->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state" style="border:1px dashed var(--color-gh-border);border-radius:.6rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 15h8M9 9h.01M15 9h.01"/></svg>
            <p>{{ $search ? 'No offline nodes match your search.' : 'No offline nodes detected — all systems nominal.' }}</p>
        </div>
    @endif

</x-app.layouts>
