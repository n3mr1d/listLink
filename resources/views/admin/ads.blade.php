<x-app.layouts title="Admin - Ads">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1>Ad Management</h1>
            <p>Review and manage advertisement submissions.</p>
        </div>
        <a href="{{ route('admin.ads.create') }}" class="btn btn-primary">Create New Ad</a>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.ads') }}" class="active">Ads</a>
        <a href="{{ route('admin.uptime-logs') }}">Uptime Logs</a>
        <a href="{{ route('admin.blacklist') }}">Blacklist</a>
    </nav>

    <div class="filter-tabs">
        <a href="{{ route('admin.ads', ['filter' => 'pending']) }}"
            class="{{ $filter === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.ads', ['filter' => 'active']) }}"
            class="{{ $filter === 'active' ? 'active' : '' }}">Active</a>
        <a href="{{ route('admin.ads', ['filter' => 'expired']) }}"
            class="{{ $filter === 'expired' ? 'active' : '' }}">Expired</a>
        <a href="{{ route('admin.ads', ['filter' => 'rejected']) }}"
            class="{{ $filter === 'rejected' ? 'active' : '' }}">Rejected</a>
        <a href="{{ route('admin.ads', ['filter' => 'all']) }}" class="{{ $filter === 'all' ? 'active' : '' }}">All</a>
    </div>

    @if ($ads->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th class="hide-mobile">Type</th>
                    <th class="hide-mobile">Placement</th>
                    <th>Status</th>
                    <th class="hide-mobile">Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ads as $ad)
                    <tr>
                        <td>
                            <strong>{{ $ad->title }}</strong>
                            <div class="text-muted" style="font-size:0.7rem;">{{ Str::limit($ad->url, 30) }}</div>
                        </td>
                        <td class="hide-mobile" style="font-size:0.8rem;">{{ $ad->ad_type->label() }}</td>
                        <td class="hide-mobile" style="font-size:0.8rem;">{{ $ad->placement->label() }}</td>
                        <td>
                            <span
                                class="status-badge {{ $ad->status === 'active' ? 'status-active' : ($ad->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                {{ ucfirst($ad->status) }}
                            </span>
                        </td>
                        <td class="hide-mobile text-muted" style="font-size:0.75rem;">{{ $ad->contact_info }}</td>
                        <td>
                            <div class="admin-actions" style="display:flex; gap:0.25rem;">
                                <a href="{{ route('admin.ads.edit', $ad->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                
                                @if ($ad->status === 'pending')
                                    <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST"
                                        class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST"
                                        class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.ads.delete', $ad->id) }}" method="POST"
                                    class="inline-form" onsubmit="return confirm('Delete this ad?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $ads->links('pagination.simple') }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted">
                No {{ $filter }} ads found.
            </div>
        </div>
    @endif

</x-app.layouts>