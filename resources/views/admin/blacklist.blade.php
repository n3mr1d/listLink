<x-app.layouts title="Admin - Blacklist Control">

    @include('admin._nav')

    <div class="admin-header">
        <h1>Access Blacklist</h1>
        <p>Define pattern-based restrictions to neutralize malicious submissions.</p>
    </div>

    {{-- Add to Blacklist --}}
    <div class="panel" style="margin-bottom:1.5rem;">
        <div class="panel-head">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Register New Ban Pattern
        </div>
        <div style="padding:1rem;">
            <form action="{{ route('admin.blacklist.add') }}" method="POST" style="display:grid;grid-template-columns:1fr;gap:.75rem;">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:.6rem;align-items:end;">
                    <div>
                        <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Pattern (e.g., domain.onion)</label>
                        <input type="text" name="url_pattern" placeholder="Enter target string..." required
                            style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.78rem;color:#fff;font-family:monospace;outline:none;">
                    </div>
                    <div>
                        <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Reason</label>
                        <input type="text" name="reason" placeholder="Brief rationale..."
                            style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.78rem;color:#fff;outline:none;">
                    </div>
                    <button type="submit" style="padding:.5rem 1rem;background:#f87171;color:#fff;border:none;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;white-space:nowrap;">
                        Sanction
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($entries->count() > 0)
        <div class="panel">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pattern</th>
                            <th>Reason</th>
                            <th class="hide-mobile">Date</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entries as $entry)
                            <tr>
                                <td>
                                    <span style="font-family:monospace;font-size:.75rem;color:#f87171;border:1px solid rgba(248,113,113,.15);padding:.15rem .4rem;border-radius:.3rem;">{{ $entry->url_pattern }}</span>
                                </td>
                                <td>
                                    <span style="font-size:.72rem;color:var(--color-gh-text);">{{ $entry->reason ?: 'Policy violation' }}</span>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size:.6rem;color:var(--color-gh-dim);">{{ $entry->created_at->diffForHumans() }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <form action="{{ route('admin.blacklist.remove', $entry->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove from blacklist?')">
                                        @csrf
                                        <button type="submit" class="btn-sm" style="color:var(--color-gh-dim);" title="Remove">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($entries->hasPages())
                <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                    {{ $entries->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state" style="border:1px dashed var(--color-gh-border);border-radius:.6rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <p>No blacklisted patterns detected.</p>
        </div>
    @endif

</x-app.layouts>
