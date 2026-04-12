<x-app.layouts title="Admin - Link Inventory">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>Link Inventory</h1>
            <p>Review, filter, and moderate all submitted .onion services.</p>
        </div>
        <a href="{{ route('submit.create') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .85rem;border:1px solid var(--color-gh-border);border-radius:.4rem;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#fff;text-decoration:none;">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            New Link
        </a>
    </div>

    {{-- Filter Bars --}}
    <div class="filter-bar">
        @foreach([
            ['all', 'All Links'],
            ['registered', 'Registered'],
            ['anonymous', 'Anonymous']
        ] as $tab)
            <a href="{{ route('admin.links', ['filter' => $tab[0]]) }}" class="{{ $filter === $tab[0] ? 'active' : '' }}">{{ $tab[1] }}</a>
        @endforeach
    </div>

    @if ($links->count() > 0)
        <div class="panel">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Node Profile</th>
                            <th class="hide-mobile">Onion Address</th>
                            <th class="hide-mobile">Category</th>
                            <th>Source</th>
                            <th class="hide-mobile">Created</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($links as $link)
                            <tr>
                                <td>
                                    <a href="{{ route('link.show', $link->slug) }}" style="font-size:.8rem;font-weight:700;color:#fff;text-decoration:none;">{{ $link->title }}</a>
                                    <div style="font-size:.55rem;color:var(--color-gh-dim);margin-top:.15rem;">
                                        @if ($link->user)
                                            by <span style="color:var(--color-gh-text);">{{ $link->user->username }}</span>
                                        @elseif ($link->user_id)
                                            by <span style="color:var(--color-gh-text);">user #{{ $link->user_id }}</span>
                                        @else
                                            <span style="opacity:.5;font-style:italic;">anonymous source</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-family:monospace;font-size:.65rem;color:var(--color-gh-dim);display:block;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $link->url }}">{{ $link->url }}</span>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size:.6rem;font-weight:700;color:var(--color-gh-dim);border:1px solid var(--color-gh-border);padding:.15rem .4rem;border-radius:.3rem;">{{ $link->category->label() }}</span>
                                </td>
                                <td>
                                    @if ($link->user_id)
                                        <div style="display:flex;align-items:center;gap:.3rem;">
                                            <div style="width:5px;height:5px;border-radius:50%;background:#4ade80;"></div>
                                            <span style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#4ade80;">Directory</span>
                                        </div>
                                    @else
                                        <div style="display:flex;align-items:center;gap:.3rem;">
                                            <div style="width:5px;height:5px;border-radius:50%;background:#58a6ff;"></div>
                                            <span style="font-size:.6rem;font-weight:700;text-transform:uppercase;color:#58a6ff;">Search</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size:.6rem;color:var(--color-gh-dim);">{{ $link->created_at->diffForHumans() }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('admin.links.delete', $link->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Confirm permanent deletion?')">
                                        @csrf
                                        <button type="submit" class="btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.2);" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($links->hasPages())
                <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                    {{ $links->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state" style="border:1px dashed var(--color-gh-border);border-radius:.6rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <p>No links found for filter: {{ ucfirst($filter) }}</p>
            <a href="{{ route('admin.links') }}" style="font-size:.65rem;color:var(--color-gh-accent);text-decoration:none;margin-top:.5rem;display:inline-block;">Reset Filters →</a>
        </div>
    @endif

</x-app.layouts>