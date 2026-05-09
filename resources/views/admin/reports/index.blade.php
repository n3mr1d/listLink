<x-app.layouts title="Admin - User Reports">

    @include('admin._nav')

    <div class="admin-header">
        <div>
            <h1>User Reports</h1>
            <p>Review and handle reports submitted by users regarding links.</p>
        </div>
    </div>

    @if ($reports->count() > 0)
        <div class="panel">
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Report Details</th>
                            <th>Link</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <div style="font-size:.75rem;font-weight:700;color:#fff;margin-bottom:.2rem;">
                                        {{ $report->type->label() }}
                                    </div>
                                    @if($report->message)
                                        <div style="font-size:.68rem;color:var(--color-gh-dim);line-height:1.4;max-width:300px;">
                                            {{ $report->message }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($report->link)
                                        <a href="{{ route('link.show', $report->link->slug) }}" target="_blank" style="font-size:.7rem;font-weight:600;color:var(--color-gh-accent);text-decoration:none;">
                                            {{ Str::limit($report->link->title, 30) }}
                                        </a>
                                        <div style="font-size:.55rem;font-family:monospace;color:var(--color-gh-dim);opacity:.6;margin-top:.1rem;">
                                            {{ Str::limit($report->link->url, 40) }}
                                        </div>
                                    @else
                                        <span style="font-size:.7rem;color:#f87171;opacity:.6;">Link Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size:.7rem;color:#fff;">
                                        {{ $report->user ? $report->user->username : 'Anonymous' }}
                                    </div>
                                    @if($report->user_id)
                                        <div style="font-size:.55rem;color:var(--color-gh-dim);">ID: #{{ $report->user_id }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($report->status === 'accepted')
                                        <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;color:#4ade80;background:rgba(74,222,128,.1);padding:.15rem .45rem;border-radius:.2rem;border:1px solid rgba(74,222,128,.2);">Accepted</span>
                                    @elseif($report->status === 'rejected')
                                        <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;color:#f87171;background:rgba(248,113,113,.1);padding:.15rem .45rem;border-radius:.2rem;border:1px solid rgba(248,113,113,.2);">Rejected</span>
                                    @else
                                        <span style="font-size:.55rem;font-weight:800;text-transform:uppercase;color:var(--color-gh-accent);background:rgba(88,166,255,.1);padding:.15rem .45rem;border-radius:.2rem;border:1px solid rgba(88,166,255,.2);">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size:.6rem;color:var(--color-gh-dim);">{{ $report->created_at->diffForHumans() }}</span>
                                </td>
                                <td style="text-align:right;">
                                    @if($report->status === 'pending')
                                        <form action="{{ route('admin.reports.accept', $report->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="color:#4ade80;border-color:rgba(74,222,128,.2);" title="Accept Report">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reports.reject', $report->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.2);" title="Reject Report">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.reports.delete', $report->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this report record?')">
                                        @csrf
                                        <button type="submit" class="btn-sm" style="color:var(--color-gh-dim);border-color:var(--color-gh-border);" title="Delete Record">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                    {{ $reports->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state" style="border:1px dashed var(--color-gh-border);border-radius:.6rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <p>No reports found.</p>
        </div>
    @endif

</x-app.layouts>
