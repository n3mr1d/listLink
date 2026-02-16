<x-app.layouts title="Admin Dashboard">

    <div class="page-header">
        <h1>Admin Dashboard</h1>
        <p>Manage links, ads, and monitor site health.</p>
    </div>

    {{-- Admin Navigation --}}
    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links ({{ $stats['total_links'] }})</a>
        <a href="{{ route('admin.ads') }}">Ads ({{ $stats['pending_ads'] }})</a>
        <a href="{{ route('admin.uptime-logs') }}">Uptime Logs</a>
        <a href="{{ route('admin.blacklist') }}">Blacklist</a>
    </nav>

    {{-- Stats Grid --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_links'] }}</div>
            <div class="stat-label">Total Links</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['active_links'] }}</div>
            <div class="stat-label">Active Links</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-green);">{{ $stats['registered_links'] }}</div>
            <div class="stat-label">Directory (Registered)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-blue);">{{ $stats['anonymous_links'] }}</div>
            <div class="stat-label">Search Only (Anon)</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-purple);">{{ $stats['pending_ads'] }}</div>
            <div class="stat-label">Pending Ads</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:var(--accent-cyan);">{{ $stats['recent_checks'] }}</div>
            <div class="stat-label">Checks (24h)</div>
        </div>
    </div>

    <div>
        {{-- Recent Links --}}
        <div class="card">
            <div class="card-header">Recent Links</div>
            <div class="card-body">
                @forelse($recentLinks as $link)
                    <div style="padding:0.4rem 0;border-bottom:1px solid var(--border-light);font-size:0.85rem;">
                        <strong style="font-weight:600;color:var(--text-primary);">{{ $link->title }}</strong>
                        @if ($link->user_id)
                            <span
                                style="display:inline-block;padding:0.1rem 0.3rem;border-radius:3px;font-size:0.65rem;font-weight:700;text-transform:uppercase;background:rgba(63,185,80,0.15);color:var(--accent-green);border:1px solid rgba(63,185,80,0.3);margin-left:0.3rem;">DIR</span>
                        @else
                            <span
                                style="display:inline-block;padding:0.1rem 0.3rem;border-radius:3px;font-size:0.65rem;font-weight:700;text-transform:uppercase;background:rgba(88,166,255,0.15);color:var(--accent-blue);border:1px solid rgba(88,166,255,0.3);margin-left:0.3rem;">SEARCH</span>
                        @endif
                        <div
                            style="font-size:0.75rem;color:var(--text-muted);margin-top:0.1rem;font-family:var(--font-mono);">
                            {{ Str::limit($link->url, 40) }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted" style="font-size:0.85rem;">No links yet.</p>
                @endforelse
                @if ($recentLinks->count() > 0)
                    <a href="{{ route('admin.links') }}" class="btn btn-sm btn-secondary" style="margin-top:1rem;">View
                        All</a>
                @endif
            </div>
        </div>

    </div>

</x-app.layouts>