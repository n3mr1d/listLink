<x-app.layouts title="Leaderboard - Top Node Contributors - {{ config('app.name') }}"
    description="Ranking of the most active contributors who have submitted verified Tor hidden services to our directory.">

    <style>
        .leaderboard-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem 5rem;
        }

        .header-section {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--color-gh-border);
        }

        .leaderboard-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 .75rem;
        }

        .leaderboard-table tr {
            background: transparent;
        }

        .leaderboard-table tr:hover {
            background: rgba(48, 54, 61, 0.2);
        }

        .leaderboard-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--color-gh-border);
        }

        .leaderboard-table td:first-child {
            width: 60px;
            text-align: center;
        }

        .leaderboard-table td:last-child {
            text-align: right;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.8rem;
            height: 1.8rem;
            font-weight: 800;
            font-size: .85rem;
            color: var(--color-gh-dim);
        }

        .rank-1 .rank-badge {
            background: rgba(210, 153, 34, 0.15);
            border-color: #d29922;
            color: #d29922;
        }

        .rank-2 .rank-badge {
            background: rgba(139, 148, 158, 0.15);
            border-color: #8b949e;
            color: #8b949e;
        }

        .rank-3 .rank-badge {
            background: rgba(163, 113, 90, 0.15);
            border-color: #a3715a;
            color: #a3715a;
        }

        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: .5rem;
            background: rgba(88, 166, 255, 0.1);
            color: var(--color-gh-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            margin-right: 1rem;
        }

        .username-link {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }

        .stats-count {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .count-value {
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--color-gh-accent);
        }

        .count-label {
            font-size: .6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--color-gh-dim);
        }

        .crown-svg {
            margin-right: .2rem;
            margin-top: -3px;
        }
    </style>

    <div class="leaderboard-container">
        <div class="header-section">
            <div style="display:inline-flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)"
                    stroke-width="2.5">
                    <path d="M12 15l-2 5l-4-10l12 0l-4 10z" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <h1 style="font-size:2rem;font-weight:900;color:#fff;margin:0;">Network Elite</h1>
            </div>
            <p style="color:var(--color-gh-dim);font-size:.9rem;max-width:500px;margin:0 auto;">
                Celebrating the architects of the decentralized web. These users have contributed the highest volume of
                verified nodes to the {{ config('app.name') }} index.
            </p>
        </div>
        <div style="display:flex;justify-content:center;flex-direction:row;gap:1rem;margin-bottom:2rem;">
            <span class="count-value"
                style="font-size:1.25rem;font-weight:800;color:var(--color-gh-sponsored);">Anonymous users
                contributed {{ $contrubutionAnonymous ?? 0 }}</span>
        </div>
        @if($topContributors->count() > 0)
            <div style="margin-bottom:1.5rem;">
                <h2
                    style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;margin-bottom:1rem;border-left:3px solid var(--color-gh-accent);padding-left:.6rem;">
                    Top Node Contributors</h2>
                <table class="leaderboard-table">
                    @foreach($topContributors as $index => $user)
                        @php $rank = $index + 1; @endphp
                        <tr class="rank-{{ $rank }}">
                            <td>
                                <div class="rank-badge">
                                    {{ $rank }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;flex-direction:column;">
                                    <span class="username-link">{{ $user->username }}</span>
                                    <span style="font-size:.65rem;color:var(--color-gh-dim);">Contributor since
                                        {{ $user->created_at->format('M Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="stats-count">
                                    <span class="count-value">{{ number_format($user->links_count) }}</span>
                                    <span class="count-label">Verified Links</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if($topAdvertisers->count() > 0)
            <div style="margin-bottom:3.5rem; margin-top:2rem;">
                <h2
                    style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;margin-bottom:1rem;border-left:3px solid var(--color-gh-sponsored);padding-left:.6rem;">
                    Premium Sponsors</h2>
                <table class="leaderboard-table">
                    @foreach($topAdvertisers as $index => $user)
                        @php $rank = $index + 1; @endphp
                        <tr class="rank-{{ $rank }}">
                            <td>
                                <div class="rank-badge">
                                    {{ $rank }}
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;flex-direction:column;">
                                    <span class="username-link">{{ $user->username }}</span>
                                    <span style="font-size:.65rem;color:var(--color-gh-dim);">Supporting the protocol since
                                        {{ $user->created_at->format('M Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="stats-count">
                                    <span class="count-value"
                                        style="color:var(--color-gh-sponsored);">{{ number_format($user->ads_count) }}</span>
                                    <span class="count-label">Active Ads</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <div
            style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:2.5rem;margin-top:2rem;">
            {{-- Top Ads By Clicks --}}
            @if($topAdsByClicks->count() > 0)
                <div>
                    <h2
                        style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;margin-bottom:1rem;border-left:3px solid var(--color-gh-sponsored);padding-left:.6rem;">
                        High Engagement (Clicks)</h2>
                    <table class="leaderboard-table">
                        @foreach($topAdsByClicks as $index => $ad)
                            @php $rank = $index + 1; @endphp
                            <tr class="rank-{{ $rank }}">
                                <td style="width:40px;padding: .75rem .5rem;">{{ $rank }}</td>
                                <td style="padding: .75rem .5rem;">
                                    <div style="display:flex;flex-direction:column;">
                                        <span
                                            style="font-size:.85rem;font-weight:700;color:#fff;">{{ Str::limit($ad->title, 25) }}</span>
                                        <span
                                            style="font-size:.65rem;font-family:monospace;color:var(--color-gh-dim);">{{ Str::limit($ad->url, 30) }}</span>
                                    </div>
                                </td>
                                <td style="padding: .75rem .5rem;text-align:right;">
                                    <div class="stats-count">
                                        <span class="count-value"
                                            style="font-size:1.1rem;color:var(--color-gh-sponsored);">{{ number_format($ad->total_clicks) }}</span>
                                        <span class="count-label">Clicks</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif

            {{-- Top Ads By Views --}}
            @if($topAdsByViews->count() > 0)
                <div>
                    <h2
                        style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.15em;margin-bottom:1rem;border-left:3px solid #3fb950;padding-left:.6rem;">
                        High Visibility (Views)</h2>
                    <table class="leaderboard-table">
                        @foreach($topAdsByViews as $index => $ad)
                            @php $rank = $index + 1; @endphp
                            <tr class="rank-{{ $rank }}">
                                <td style="width:40px;padding: .75rem .5rem;">{{ $rank }}</td>
                                <td style="padding: .75rem .5rem;">
                                    <div style="display:flex;flex-direction:column;">
                                        <span
                                            style="font-size:.85rem;font-weight:700;color:#fff;">{{ Str::limit($ad->title, 25) }}</span>
                                        <span
                                            style="font-size:.65rem;font-family:monospace;color:var(--color-gh-dim);">{{ Str::limit($ad->url, 30) }}</span>
                                    </div>
                                </td>
                                <td style="padding: .75rem .5rem;text-align:right;">
                                    <div class="stats-count">
                                        <span class="count-value"
                                            style="font-size:1.1rem;color:#3fb950;">{{ number_format($ad->total_views) }}</span>
                                        <span class="count-label">Views</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>

        @if($topContributors->count() == 0 && $topAdsByClicks->count() == 0)
            <div style="text-align:center;padding:5rem 0;opacity:.5;">
                <p>Establishing leadership metrics...</p>
            </div>
        @endif

        <div
            style="margin-top:4rem;text-align:center;padding:2rem;border-radius:1rem;background:rgba(88,166,255,0.03);border:1px dashed var(--color-gh-border);">
            <h3 style="font-size:.9rem;color:#fff;margin-bottom:.5rem;">Want to see your name here?</h3>
            <p style="font-size:.75rem;color:var(--color-gh-dim);margin-bottom:1.5rem;">Submit your hidden services to
                help grow the decentralized directory.</p>
            <a href="{{ route('submit.create') }}"
                style="display:inline-block;padding:.6rem 1.5rem;background:var(--color-gh-accent);color:#0d1117;border-radius:.5rem;text-decoration:none;font-weight:800;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;">Submit
                Link</a>
        </div>
    </div>

</x-app.layouts>