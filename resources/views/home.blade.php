<x-app.layouts title="Verified Tor .Onion Directory"
    description="Explore verified .onion services on the Tor network. The most reliable and updated Tor directory with daily uptime monitoring and community comments.">

    {{-- Header Banner Ad --}}
    {{-- Header Banner Ads --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div class="ad-banner"
                style="position:relative; width:100%; max-width:728px; height:90px; margin:0 auto 1.5rem auto; border-radius:6px; overflow:hidden; border:1px solid var(--border-color); background:#0d1117;">

                {{-- Sponsored Label --}}
                <span
                    style="position:absolute; top:0.4rem; right:0.4rem; background:rgba(0,0,0,0.7); color:var(--text-muted); padding:0.1rem 0.4rem; border-radius:3px; font-size:0.6rem; font-weight:700; text-transform:uppercase; z-index:10;">
                    Sponsored
                </span>

                @if ($headerAd->banner_path)
                    <a href="{{ $headerAd->url }}" style="display:block; width:100%; height:100%;">
                        <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}"
                            style="width:100%; height:100%; object-fit:cover;">
                    </a>
                @else
                    <a href="{{ $headerAd->url }}"
                        style="display:flex; width:100%; height:100%; align-items:center; justify-content:center; background:linear-gradient(135deg, #1a2332 0%, #0d1117 100%); text-decoration:none;">
                        <span style="font-size:1.2rem; font-weight:700; color:#fff;">{{ $headerAd->title }}</span>
                    </a>
                @endif

                {{-- Title/Premium Overlay --}}
                <div
                    style="position:absolute; bottom:0; left:0; width:100%; padding:0.5rem; background:linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0)); display:flex; justify-content:space-between; align-items:flex-end;">
                    <div style="display:flex; flex-direction:column;">
                        <a href="{{ $headerAd->url }}"
                            style="font-size:1rem; font-weight:700; color:#fff; text-shadow:0 1px 2px rgba(0,0,0,0.8); text-decoration:none;">{{ $headerAd->title }}</a>
                    </div>
                    <span
                        style="background:rgba(255, 215, 0, 0.15); color:#ffd700; border:1px solid rgba(255, 215, 0, 0.3); padding:0.1rem 0.4rem; border-radius:3px; font-size:0.6rem; font-weight:700; text-transform:uppercase; backdrop-filter:blur(2px);">
                        Premium
                    </span>
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

                                @foreach ($grouped[$category->value]->take(10) as $link)
                                    <tr>
                                        <td class="link-title">
                                            <div style="display:flex; flex-direction:column; gap:0.1rem;">
                                                <a href="{{ route('link.show', $link->slug) }}"
                                                    style="font-weight:600; color:var(--text-primary);">
                                                    {{ $link->title }}
                                                </a>
                                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                                    <span class="onion-v3-shorthand text-muted">{{ $link->url }}</span>
                                                    <span class="geo-tag"><i class="fas fa-globe"></i> Global</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="link-url hide-mobile">
                                            <span class="text-muted" style="font-size:0.75rem;">
                                                <i class="far fa-clock"></i> {{ $link->last_check->diffForHumans() }}
                                            </span>
                                        </td>
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
                    <div class="ad-banner-sidebar" style="margin-bottom:1.5rem; display:flex; justify-content:center;">
                        <div
                            style="position:relative; width:300px; height:250px; border-radius:6px; overflow:hidden; border:1px solid var(--border-color); background:#0d1117;">

                            {{-- Sponsored Label --}}
                            <span
                                style="position:absolute; top:0.4rem; right:0.4rem; background:rgba(0,0,0,0.7); color:var(--text-muted); padding:0.1rem 0.4rem; border-radius:3px; font-size:0.6rem; font-weight:700; text-transform:uppercase; z-index:10;">
                                Sponsored
                            </span>

                            @if ($sideAd->banner_path)
                                <a href="{{ $sideAd->url }}" style="display:block; width:100%; height:100%;">
                                    <img src="{{ asset('storage/' . $sideAd->banner_path) }}" alt="{{ $sideAd->title }}"
                                        style="width:100%; height:100%; object-fit:cover;">
                                </a>
                            @else
                                <a href="{{ $sideAd->url }}"
                                    style="display:flex; flex-direction:column; width:100%; height:100%; align-items:center; justify-content:center; background:linear-gradient(180deg, #161b22 0%, #0d1117 100%); text-decoration:none; padding:1rem; text-align:center;">
                                    <div
                                        style="width:48px;height:48px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:900;background:rgba(255,255,255,0.1);color:#fff;margin-bottom:0.75rem;">
                                        HL</div>
                                    <div style="font-size:1rem;font-weight:700;color:#fff;">{{ $sideAd->title }}</div>
                                </a>
                            @endif

                            {{-- Title/Premium Overlay --}}
                            <div
                                style="position:absolute; bottom:0; left:0; width:100%; padding:0.75rem; background:linear-gradient(to top, rgba(0,0,0,0.95), rgba(0,0,0,0)); display:flex; justify-content:space-between; align-items:flex-end;">
                                <div style="display:flex; flex-direction:column; max-width:70%;">
                                    <a href="{{ $sideAd->url }}"
                                        style="font-size:0.9rem; font-weight:700; color:#fff; text-shadow:0 1px 2px rgba(0,0,0,0.8); text-decoration:none; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $sideAd->title }}</a>
                                </div>
                                <span
                                    style="background:rgba(255, 215, 0, 0.15); color:#ffd700; border:1px solid rgba(255, 215, 0, 0.3); padding:0.1rem 0.4rem; border-radius:3px; font-size:0.6rem; font-weight:700; text-transform:uppercase; backdrop-filter:blur(2px);">
                                    Premium
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Quick Links --}}
        </div>
    </div>

</x-app.layouts>