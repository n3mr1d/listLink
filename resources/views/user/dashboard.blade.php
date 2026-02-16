<x-app.layouts title="User Dashboard">

    <div class="page-header">
        <h1>Welcome, {{ auth()->user()->username }}!</h1>
        <p>Manage your submitted links and advertisement requests.</p>
    </div>

    <div class="page-grid">
        <div style="grid-column: span 12;">
            {{-- Links Section --}}
            <div class="card mb-2">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>My Submitted Links</span>
                    <a href="{{ route('submit.create') }}" class="btn btn-sm btn-primary">Submit New Link</a>
                </div>
                <div class="card-body" style="padding:0;">
                    @if($links->count() > 0)
                        <table class="links-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Last Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($links as $link)
                                    <tr>
                                        <td>
                                            <a href="{{ route('link.show', $link->slug) }}">{{ $link->title }}</a>
                                            <div class="text-muted" style="font-size:0.7rem;">{{ Str::limit($link->url, 40) }}
                                            </div>
                                        </td>
                                        <td style="font-size:0.8rem;">{{ $link->category->label() }}</td>
                                        <td>
                                            <span class="uptime-badge {{ $link->uptime_status->cssClass() }}">
                                                {{ $link->uptime_status->label() }}
                                            </span>
                                        </td>
                                        <td style="font-size:0.75rem;" class="text-muted">
                                            {{ $link->last_check ? $link->last_check->diffForHumans() : 'Never' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination" style="padding:1rem;">
                            {{ $links->links('pagination.simple') }}
                        </div>
                    @else
                        <div class="text-center py-2 text-muted">
                            You haven't submitted any links yet.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ads Section --}}
            <div class="card">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>My Advertisement Requests</span>
                    <a href="{{ route('advertise.create') }}" class="btn btn-sm btn-secondary">Request New Ad</a>
                </div>
                <div class="card-body" style="padding:0;">
                    @if($ads->count() > 0)
                        <table class="links-table">
                            <thead>
                                <tr>
                                    <th>Ad Title</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Expires At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ads as $ad)
                                    <tr>
                                        <td>
                                            <strong>{{ $ad->title }}</strong>
                                            <div class="text-muted" style="font-size:0.7rem;">{{ Str::limit($ad->url, 40) }}
                                            </div>
                                        </td>
                                        <td style="font-size:0.8rem;">{{ $ad->ad_type->label() }}</td>
                                        <td>
                                            <span
                                                class="status-badge {{ $ad->status === 'active' ? 'status-active' : ($ad->status === 'pending' ? 'status-pending' : 'status-rejected') }}">
                                                {{ ucfirst($ad->status) }}
                                            </span>
                                        </td>
                                        <td style="font-size:0.75rem;" class="text-muted">
                                            {{ $ad->expires_at ? $ad->expires_at->format('M d, Y') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination" style="padding:1rem;">
                            {{ $ads->links('pagination.simple') }}
                        </div>
                    @else
                        <div class="text-center py-2 text-muted">
                            You have no ad requests.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app.layouts>