<x-app.layouts title="Discovered Links — {{ $link->url }}">

    <div class="page-header">
        <h1>🔗 Discovered Links</h1>
        <p style="font-family:var(--font-mono);font-size:0.85rem;color:var(--text-muted);">Parent: {{ $link->url }}</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.crawler.index') }}" class="active">Crawler</a>
    </nav>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;">
            <span>{{ number_format($discovered->total()) }} URLs discovered from this page</span>
            <div style="display:flex;gap:0.5rem;align-items:center;">
                <a href="{{ route('admin.crawler.index') }}" class="btn btn-sm btn-secondary">← Back</a>

                <form method="POST" action="{{ route('admin.crawler.discovered.clear', $link->id) }}">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-sm"
                        style="background:rgba(248,81,73,0.15);color:#f85149;border:1px solid rgba(248,81,73,0.4);"
                        onclick="return confirm('Delete all discovered links for this URL?')">
                        🗑 Clear All
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body" style="padding:0;">
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                    <thead>
                        <tr style="background:var(--bg-secondary);border-bottom:1px solid var(--border-light);">
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">#</th>
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Discovered URL</th>
                            <th style="padding:0.6rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;">Recorded At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discovered as $item)
                            <tr style="border-bottom:1px solid var(--border-light);">
                                <td style="padding:0.5rem 1rem;color:var(--text-muted);">{{ $item->id }}</td>
                                <td style="padding:0.5rem 1rem;font-family:var(--font-mono);font-size:0.78rem;max-width:600px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $item->url }}">
                                    <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" style="color:var(--accent-cyan);text-decoration:none;">
                                        {{ Str::limit($item->url, 80) }}
                                    </a>
                                </td>
                                <td style="padding:0.5rem 1rem;color:var(--text-muted);white-space:nowrap;font-size:0.78rem;">
                                    {{ $item->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding:2rem;text-align:center;color:var(--text-muted);">No discovered links yet. Try crawling this URL first.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($discovered->hasPages())
            <div style="padding:1rem;border-top:1px solid var(--border-light);">
                {{ $discovered->links() }}
            </div>
        @endif
    </div>

</x-app.layouts>
