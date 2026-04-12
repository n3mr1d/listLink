<x-app.layouts title="Admin - Discovered Links">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>Discovered Links</h1>
            <p style="font-family:monospace;font-size:.68rem;color:var(--color-gh-dim);opacity:.6;margin-top:.2rem;max-width:500px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $link->url }}">Parent: {{ $link->url }}</p>
        </div>
        <a href="{{ route('admin.crawler.index') }}" style="font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:.3rem;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    <div class="panel">
        <div class="panel-head" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.4rem;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9c.26.604.852.997 1.51 1H21a2 2 0 010 4h-.09c-.658.003-1.25.396-1.51 1z"/></svg>
                Fragment Inventory
                <span style="font-size:.55rem;color:var(--color-gh-dim);margin-left:.3rem;">{{ number_format($discovered->total()) }} URLs</span>
            </div>
            <form method="POST" action="{{ route('admin.crawler.discovered.clear', $link->id) }}" style="display:inline;" onsubmit="return confirm('Clear all discovered links?')">
                @csrf
                <button type="submit" style="padding:.3rem .65rem;border:1px solid rgba(248,113,113,.2);background:none;color:#f87171;border-radius:.35rem;font-size:.55rem;font-weight:800;text-transform:uppercase;cursor:pointer;">Purge All</button>
            </form>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:80px;">#</th>
                        <th>URL</th>
                        <th style="text-align:right;" class="hide-mobile">Detected</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discovered as $item)
                        <tr>
                            <td><span style="font-family:monospace;font-size:.6rem;color:var(--color-gh-dim);">0x{{ dechex($item->id) }}</span></td>
                            <td>
                                <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" style="font-family:monospace;font-size:.68rem;color:#22d3ee;text-decoration:none;word-break:break-all;line-height:1.5;display:block;max-width:700px;" title="{{ $item->url }}">{{ $item->url }}</a>
                            </td>
                            <td style="text-align:right;white-space:nowrap;" class="hide-mobile">
                                <span style="font-size:.6rem;font-weight:700;color:#fff;">{{ $item->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                                    <p>No fragments captured.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($discovered->hasPages())
            <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                {{ $discovered->links('pagination.simple') }}
            </div>
        @endif
    </div>

</x-app.layouts>
