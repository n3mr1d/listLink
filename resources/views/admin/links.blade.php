<x-app.layouts title="Admin - Links">

    <div class="page-header">
        <h1>Link Management</h1>
        <p>Manage submitted .onion links. All links are auto-published — you can only delete links.</p>
    </div>

    {{-- Admin Navigation --}}
    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}" class="active">Links</a>
        <a href="{{ route('admin.ads') }}">Ads</a>
        <a href="{{ route('admin.uptime-logs') }}">Uptime Logs</a>
        <a href="{{ route('admin.blacklist') }}">Blacklist</a>
    </nav>

    {{-- Filter Tabs --}}
    <div class="filter-tabs">
        <a href="{{ route('admin.links', ['filter' => 'all']) }}"
            class="{{ $filter === 'all' ? 'active' : '' }}">All Links</a>
        <a href="{{ route('admin.links', ['filter' => 'registered']) }}"
            class="{{ $filter === 'registered' ? 'active' : '' }}">Registered Users</a>
        <a href="{{ route('admin.links', ['filter' => 'anonymous']) }}"
            class="{{ $filter === 'anonymous' ? 'active' : '' }}">Anonymous</a>
    </div>

    @if ($links->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th class="hide-mobile">URL</th>
                    <th class="hide-mobile">Category</th>
                    <th>Source</th>
                    <th class="hide-mobile">Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($links as $link)
                    <tr>
                        <td>
                            <strong>{{ $link->title }}</strong>
                            @if ($link->user)
                                <div class="text-muted" style="font-size:0.7rem;">by {{ $link->user->username }}</div>
                            @elseif ($link->user_id)
                                <div class="text-muted" style="font-size:0.7rem;">user #{{ $link->user_id }}</div>
                            @else
                                <div class="text-muted" style="font-size:0.7rem;">anonymous</div>
                            @endif
                        </td>
                        <td class="link-url hide-mobile">{{ Str::limit($link->url, 35) }}</td>
                        <td class="hide-mobile" style="font-size:0.8rem;">{{ $link->category->label() }}</td>
                        <td>
                            @if ($link->user_id)
                                <span style="display:inline-block;padding:0.1rem 0.3rem;border-radius:3px;font-size:0.65rem;font-weight:700;text-transform:uppercase;background:rgba(63,185,80,0.15);color:var(--accent-green);border:1px solid rgba(63,185,80,0.3);">
                                    Directory + Search
                                </span>
                            @else
                                <span style="display:inline-block;padding:0.1rem 0.3rem;border-radius:3px;font-size:0.65rem;font-weight:700;text-transform:uppercase;background:rgba(88,166,255,0.15);color:var(--accent-blue);border:1px solid rgba(88,166,255,0.3);">
                                    Search Only
                                </span>
                            @endif
                        </td>
                        <td class="hide-mobile text-muted" style="font-size:0.75rem;">
                            {{ $link->created_at->diffForHumans() }}
                        </td>
                        <td>
                            <div class="admin-actions">
                                <form action="{{ route('admin.links.delete', $link->id) }}" method="POST"
                                    class="inline-form">
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
            {{ $links->links('pagination.simple') }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted">
                No {{ $filter === 'all' ? '' : $filter }} links found.
            </div>
        </div>
    @endif

</x-app.layouts>