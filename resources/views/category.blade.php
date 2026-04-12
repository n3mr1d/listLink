<x-app.layouts title="{{ $category->label() }} .Onion Links"
    description="Browse all verified Tor hidden services in the {{ $category->label() }} category. Updated daily with uptime status.">

    <style>
        .category-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 1024px) {
            .category-layout {
                grid-template-columns: 1fr 300px;
            }
        }

        .cat-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cat-table th {
            font-size: .6rem;
            font-weight: 800;
            color: var(--color-gh-dim);
            text-transform: uppercase;
            letter-spacing: .12em;
            padding: .5rem .85rem;
            text-align: left;
            border-bottom: 1px solid var(--color-gh-border);
        }

        .cat-table td {
            padding: .65rem .85rem;
            border-bottom: 1px solid var(--color-gh-border);
            vertical-align: middle;
        }

        .cat-table tr:last-child td {
            border-bottom: none;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .55rem;
            border-radius: 2rem;
            font-size: .6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        @media (max-width: 640px) {
            .cat-table .col-details {
                display: none;
            }
        }
    </style>

    {{-- Breadcrumbs --}}
    <nav
        style="display:flex;align-items:center;gap:.6rem;margin-bottom:1.5rem;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.15em;color:var(--color-gh-dim);">
        <a href="{{ route('home') }}" style="color:var(--color-gh-dim);text-decoration:none;">Core</a>
        <span style="opacity:.3;">/</span>
        <span style="color:#fff;">{{ $category->label() }}</span>
    </nav>

    {{-- Header Ad --}}
    @if (isset($headerAds) && $headerAds->count() > 0)
        @foreach ($headerAds as $headerAd)
            <div
                style="position:relative;width:100%;height:80px;border-radius:.5rem;overflow:hidden;border:1px solid var(--color-gh-border);margin-bottom:2rem;">
                <span
                    style="position:absolute;top:.3rem;right:.5rem;background:rgba(0,0,0,.75);color:var(--color-gh-sponsored);padding:.12rem .4rem;border-radius:.2rem;font-size:10px;font-weight:800;text-transform:uppercase;z-index:1;">Sponsored</span>
                @if ($headerAd->banner_path)
                    <a href="{{ route('ad.track', $headerAd->id) }}" style="display:block;width:100%;height:100%;">
                        <img src="{{ asset('storage/' . $headerAd->banner_path) }}" alt="{{ $headerAd->title }}"
                            style="width:100%;height:100%;object-fit:cover;">
                    </a>
                @else
                    <a href="{{ route('ad.track', $headerAd->id) }}"
                        style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-btn-bg);">
                        <span style="font-size:.85rem;font-weight:800;color:#fff;">{{ $headerAd->title }}</span>
                    </a>
                @endif
            </div>
        @endforeach
    @endif

    <div class="category-layout">

        {{-- ══ Main Column ══ --}}
        <div>
            {{-- Page heading --}}
            <div style="margin-bottom:1.5rem;">
                <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0 0 .25rem;letter-spacing:-.02em;">
                    {{ $category->label() }}
                </h1>
                <p style="color:var(--color-gh-dim);font-size:.85rem;margin:0;">Sector transmission filtered for
                    verified onion signatures.</p>
            </div>

            @if($links->count() > 0)
                <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                    <table class="cat-table">
                        <thead>
                            <tr>
                                <th>Identity / URL</th>
                                <th class="col-details">Details</th>
                                <th style="width:100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($links as $link)
                                @php $isOnline = $link->uptime_status === \App\Enum\UptimeStatus::ONLINE; @endphp
                                <tr>
                                    <td>
                                        <div style="display:flex;flex-direction:column;gap:.25rem;">
                                            <a href="{{ route('link.show', $link->slug) }}"
                                                style="font-size:.85rem;font-weight:700;color:var(--color-gh-accent);text-decoration:none;line-height:1.2;">{{ $link->title }}</a>
                                            <span
                                                style="font-size:.65rem;font-family:monospace;color:var(--color-gh-dim);opacity:.5;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:260px;">{{ $link->url }}</span>
                                        </div>
                                    </td>
                                    <td class="col-details" style="font-size:.7rem;color:var(--color-gh-dim);opacity:.7;">
                                        {{ Str::limit($link->description, 80) }}
                                    </td>
                                    <td>
                                        <span class="status-pill"
                                            style="border:1px solid {{ $isOnline ? 'rgba(74,222,128,.3)' : 'rgba(248,113,113,.3)' }};color:{{ $isOnline ? '#4ade80' : '#f87171' }};">
                                            <span
                                                style="width:4px;height:4px;border-radius:50%;background:{{ $isOnline ? '#4ade80' : '#f87171' }};flex-shrink:0;{{ $isOnline ? 'box-shadow:0 0 5px #4ade80' : '' }}"></span>
                                            {{ $link->uptime_status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:2rem;">
                    {{ $links->links('pagination.simple') }}
                </div>
            @else
                <div
                    style="padding:4rem 2rem;text-align:center;border:1px dashed var(--color-gh-border);border-radius:.75rem;opacity:.5;">
                    <p
                        style="text-transform:uppercase;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);letter-spacing:.2em;">
                        Sector is currently deserted.</p>
                    <a href="{{ route('submit.create') }}"
                        style="display:inline-block;margin-top:1rem;color:var(--color-gh-accent);text-decoration:none;font-size:.75rem;font-weight:700;">Submit
                        Signal Access →</a>
                </div>
            @endif
        </div>

        {{-- ══ Sidebar ══ --}}
        <aside style="display:flex;flex-direction:column;gap:1.5rem;">
            {{-- Category Navigation --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                <div
                    style="padding:.65rem 1rem;border-bottom:1px solid var(--color-gh-border);font-size:.62rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;background:rgba(255,255,255,.02);">
                    Infrastructure Sections
                </div>
                <div style="padding:.4rem 0;">
                    @foreach($categories as $cat)
                        <a href="{{ route('category.show', $cat->value) }}"
                            style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 1rem;text-decoration:none;color:{{ $cat->value === $category->value ? '#fff' : 'var(--color-gh-dim)' }};font-size:.78rem;font-weight:{{ $cat->value === $category->value ? '700' : '500' }};background:{{ $cat->value === $category->value ? 'rgba(88,166,255,.1)' : 'transparent' }}">
                            {{ $cat->label() }}
                            @if($cat->value === $category->value)
                                <span style="width:4px;height:4px;border-radius:50%;background:var(--color-gh-accent);"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Sidebar Ads --}}
            @if (isset($sidebarAds) && $sidebarAds->count() > 0)
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    @foreach ($sidebarAds as $sideAd)
                        <div
                            style="position:relative;width:100%;height:220px;border-radius:.5rem;overflow:hidden;border:1px solid var(--color-gh-border);">
                            <span
                                style="position:absolute;top:.4rem;right:.5rem;background:rgba(0,0,0,.75);color:var(--color-gh-sponsored);padding:.1rem .4rem;border-radius:.2rem;font-size:9px;font-weight:800;text-transform:uppercase;z-index:1;border:1px solid rgba(210,153,34,.2);">Ad</span>
                            @if ($sideAd->banner_path)
                                <a href="{{ route('ad.track', $sideAd->id) }}" style="display:block;width:100%;height:100%;">
                                    <img src="{{ asset('storage/' . $sideAd->banner_path) }}" alt="{{ $sideAd->title }}"
                                        style="width:100%;height:100%;object-fit:cover;">
                                </a>
                            @else
                                <a href="{{ route('ad.track', $sideAd->id) }}"
                                    style="display:flex;width:100%;height:100%;align-items:center;justify-content:center;text-decoration:none;background:var(--color-gh-bar-bg);padding:1.5rem;text-align:center;">
                                    <span
                                        style="font-size:.8rem;font-weight:700;color:#fff;letter-spacing:.05em;">{{ $sideAd->title }}</span>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </aside>

    </div>

</x-app.layouts>