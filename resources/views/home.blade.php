<x-app.layouts title="Home">

    {{-- Header Banner Ad --}}
    {{-- Header Banner Ads --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div class="ad-banner" style="margin-bottom: 1.5rem;">
                <span class="ad-label">Sponsored</span>
                @if ($headerAd->banner_path)
                    <a href="{{ $headerAd->url }}">
                        <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}">
                    </a>
                @endif
                <div class="ad-banner-text">
                    <div class="ad-title"><a href="{{ $headerAd->url }}">{{ $headerAd->title }}</a></div>
                    @if ($headerAd->description)
                        <div class="ad-desc">{{ $headerAd->description }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif

    {{-- Contact Announcement --}}
    <div class="alert alert-info" style="margin-bottom:1.5rem; border-left: 4px solid var(--accent-blue);">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <span style="font-size:1.2rem;"><i class="fa fa-envelope"></i></span>
                <span style="font-size:0.9rem; color:var(--text-primary);">
                    Want to advertise or have suggestions? Contact:
                    <a href="mailto:treixnox@protonmail.com"
                        style="color:var(--accent-blue); font-weight:700;">treixnox@protonmail.com</a>
                </span>
            </div>
            <a href="{{ route('advertise.create') }}" class="btn btn-secondary"
                style="font-size:0.75rem; padding:0.3rem 0.6rem;">
                Ad Pricing & Details
            </a>
        </div>
    </div>

    <div class="page-grid">
        <div>
            <div class="page-header">
                <h1>Tor Directory</h1>
                <p>Privacy-focused directory of verified .onion websites. we dont have rules here</p>
            </div>

            {{-- Links grouped by category --}}
            @php
                $grouped = $links->groupBy(fn($link) => $link->category->value);
            @endphp

            @foreach ($categories as $category)
                @if (isset($grouped[$category->value]) && $grouped[$category->value]->count() > 0)
                    <div class="category-section">
                        <div class="category-header">
                            <h2>{{ $category->label() }}</h2>
                            <a href="{{ route('category.show', $category->value) }}">View All &rarr;</a>
                        </div>

                        <table class="links-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="hide-mobile">Last Check</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Sponsored Links at the top of first category --}}
                                @if ($loop->first && isset($sponsoredLinks) && $sponsoredLinks->count() > 0)
                                    @foreach ($sponsoredLinks as $sponsoredLink)
                                        <tr class="sponsored-row">
                                            <td class="link-title">
                                                <span class="badge-sponsored">Ad</span>
                                                <a href="{{ $sponsoredLink->url }}">{{ $sponsoredLink->title }}</a>
                                            </td>
                                            <td class="link-url hide-mobile"><i class="fa fa-crown"></i> Premium</td>
                                            <td><span class="badge-sponsored">Sponsored</span></td>
                                        </tr>
                                    @endforeach
                                @endif

                                @foreach ($grouped[$category->value]->take(5) as $link)
                                    <tr>
                                        <td class="link-title">
                                            <a href="{{ route('link.show', $link->slug) }}">{{ $link->title }} <i
                                                    class="fas fa-external-link"></i></a>
                                        </td>
                                        <td class="link-url hide-mobile">{{ $link->last_check->diffForHumans() }}</td>
                                        <td>
                                            <span class="uptime-badge {{ $link->uptime_status->cssClass() }}">
                                                {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endforeach

            {{-- Pagination --}}
            <div class="pagination">
                {{ $links->links('pagination.simple') }}
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="sidebar">
            {{-- Stats --}}
            <div class="sidebar-card">
                <div class="sidebar-card-header">Directory Stats</div>
                <div class="sidebar-card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div class="text-center">
                            <div style="font-size:1.4rem;font-weight:700;color:var(--accent-green);">
                                {{ $stats['total_links'] }}
                            </div>
                            <div class="text-muted" style="font-size:0.7rem;">TOTAL LINKS</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size:1.4rem;font-weight:700;color:var(--accent-green);">
                                {{ $stats['online_links'] }}
                            </div>
                            <div class="text-muted" style="font-size:0.7rem;">ONLINE</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Categories --}}
            <div class="sidebar-card">
                <div class="sidebar-card-header">Categories</div>
                <div class="sidebar-card-body" style="padding:0;">
                    <ul class="categories-list">
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('category.show', $category->value) }}">
                                    {{ $category->label() }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Sidebar Ads --}}
            @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                @foreach ($sidebarAds as $sideAd)
                    <div class="ad-banner">
                        <span class="ad-label">Ad</span>
                        <div class="ad-banner-text">
                            <div class="ad-title"><a href="{{ $sideAd->url }}">{{ $sideAd->title }}</a></div>
                            @if ($sideAd->description)
                                <div class="ad-desc">{{ $sideAd->description }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Quick Links --}}
        </div>
    </div>

</x-app.layouts>