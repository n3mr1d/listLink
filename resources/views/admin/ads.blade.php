<x-app.layouts title="Admin - Ad Management">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>Advertisement Control</h1>
            <p>Moderate campaign requests and manage active sponsored slots.</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;text-decoration:none;border:none;">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Launch New Ad
        </a>
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        @foreach(['pending', 'active', 'expired', 'rejected', 'all'] as $f)
            <a href="{{ route('admin.ads', ['filter' => $f]) }}" class="{{ $filter === $f ? 'active' : '' }}">{{ $f }}</a>
        @endforeach
    </div>

    @if ($ads->count() > 0)
        <div class="panel">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th class="hide-mobile">Banner</th>
                            <th class="hide-mobile">Tier & Placement</th>
                            <th>Status</th>
                            <th class="hide-mobile" style="text-align:center;">Contact</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ads as $ad)
                            <tr>
                                <td>
                                    <div style="font-size:.8rem;font-weight:700;color:#fff;">{{ $ad->title }}</div>
                                    <div style="font-size:.6rem;font-family:monospace;color:var(--color-gh-dim);max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:.1rem;" title="{{ $ad->url }}">{{ $ad->url }}</div>
                                </td>
                                <td class="hide-mobile">
                                    @if($ad->banner_path)
                                        <img src="{{ asset('storage/' . $ad->banner_path) }}?v={{ $ad->updated_at->timestamp }}"
                                            alt="Banner"
                                            style="width:167px;height:19px;object-fit:cover;border-radius:.25rem;border:1px solid var(--color-gh-border);display:block;"
                                            loading="lazy">
                                    @else
                                        <span style="font-size:.55rem;color:var(--color-gh-dim);font-style:italic;">No banner</span>
                                    @endif
                                </td>
                                <td class="hide-mobile">
                                    <div style="font-size:.6rem;font-weight:800;text-transform:uppercase;color:#fff;">{{ $ad->ad_type->label() }}</div>
                                    <div style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;margin-top:.1rem;">{{ $ad->placement->label() }}</div>
                                </td>
                                <td>
                                    @php
                                        $sbClass = match($ad->status) {
                                            'active' => 'sb-active',
                                            'pending' => 'sb-pending',
                                            'rejected' => 'sb-rejected',
                                            'expired' => 'sb-expired',
                                            default => 'sb-unknown',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $sbClass }}">{{ $ad->status }}</span>
                                </td>
                                <td class="hide-mobile" style="text-align:center;">
                                    <span style="font-size:.65rem;font-family:monospace;color:var(--color-gh-accent);border:1px solid var(--color-gh-border);padding:.15rem .4rem;border-radius:.3rem;">{{ $ad->contact_info }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex;gap:.25rem;align-items:center;">
                                        <a href="{{ route('admin.ads.edit', $ad->id) }}" class="btn-sm" style="color:var(--color-gh-dim);" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        @if ($ad->status === 'pending')
                                            <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-sm" style="color:#4ade80;border-color:rgba(74,222,128,.2);" title="Approve">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.2);" title="Reject">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.ads.delete', $ad->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Confirm ad deletion?')">
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

            @if($ads->hasPages())
                <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                    {{ $ads->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state" style="border:1px dashed var(--color-gh-border);border-radius:.6rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h-8l-2 4h12l-2-4z"/></svg>
            <p>No ads for filter: {{ ucfirst($filter) }}</p>
            <a href="{{ route('admin.ads') }}" style="font-size:.65rem;color:var(--color-gh-accent);text-decoration:none;margin-top:.5rem;display:inline-block;">Reset Filters →</a>
        </div>
    @endif

</x-app.layouts>