<x-app.layouts title="Admin - Blacklist">

    <div class="page-header">
        <h1>URL Blacklist</h1>
        <p>Manage blacklisted URL patterns to prevent malicious submissions.</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.ads') }}">Ads</a>
        <a href="{{ route('admin.uptime-logs') }}">Uptime Logs</a>
        <a href="{{ route('admin.blacklist') }}" class="active">Blacklist</a>
    </nav>

    {{-- Add to Blacklist --}}
    <div class="card mb-2">
        <div class="card-header">Add URL Pattern</div>
        <div class="card-body">
            <form action="{{ route('admin.blacklist.add') }}" method="POST"
                style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:end;">
                @csrf
                <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
                    <label>URL Pattern</label>
                    <input type="text" name="url_pattern" placeholder="e.g., malicious.onion" required>
                </div>
                <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
                    <label>Reason (optional)</label>
                    <input type="text" name="reason" placeholder="Reason for blacklisting">
                </div>
                <button type="submit" class="btn btn-danger">Add to Blacklist</button>
            </form>
        </div>
    </div>

    {{-- Current Blacklist --}}
    @if ($entries->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>URL Pattern</th>
                    <th>Reason</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td class="mono" style="font-size:0.8rem;">{{ $entry->url_pattern }}</td>
                        <td style="font-size:0.8rem;color:var(--text-secondary);">{{ $entry->reason ?: '—' }}</td>
                        <td style="font-size:0.8rem;">{{ $entry->created_at->diffForHumans() }}</td>
                        <td>
                            <form action="{{ route('admin.blacklist.remove', $entry->id) }}" method="POST"
                                class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $entries->links('pagination.simple') }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted">
                No blacklisted URLs. The blacklist is empty.
            </div>
        </div>
    @endif

</x-app.layouts>

