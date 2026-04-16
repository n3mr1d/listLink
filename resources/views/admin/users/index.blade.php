<x-app.layouts title="Admin - User Management">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>User Management</h1>
            <p>Monitor system accounts, adjust roles, and manage access control.</p>
        </div>
        <div style="display:flex;gap:.5rem;">
            <div style="position:relative;">
                <input type="text" id="userQuery" placeholder="Filter users..." style="background:rgba(255,255,255,.05);border:1px solid var(--color-gh-border);border-radius:.35rem;padding:.45rem .75rem;font-size:.7rem;color:#fff;width:220px;outline:none;transition:border-color .15s;" onkeyup="filterUsers()">
                <div style="position:absolute;right:.6rem;top:50%;transform:translateY(-50%);opacity:.3;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- User Statistics Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:1.5rem;">
        <div class="panel" style="margin-bottom:0;padding:1rem;background:rgba(255,255,255,.02);">
            <div style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);margin-bottom:.4rem;">Total Accounts</div>
            <div style="font-size:1.5rem;font-weight:900;color:#fff;display:flex;align-items:center;gap:.5rem;">
                {{ \App\Models\User::count() }}
                <span style="font-size:.7rem;font-weight:400;color:#4ade80;background:rgba(74,222,128,.1);padding:.1rem .3rem;border-radius:.2rem;">Active</span>
            </div>
        </div>
        <div class="panel" style="margin-bottom:0;padding:1rem;background:rgba(255,255,255,.02);">
            <div style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);margin-bottom:.4rem;">Administrators</div>
            <div style="font-size:1.5rem;font-weight:900;color:var(--color-gh-accent);">
                {{ \App\Models\User::where('role', 'admin')->count() }}
            </div>
        </div>
        <div class="panel" style="margin-bottom:0;padding:1rem;background:rgba(255,255,255,.02);">
            <div style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);margin-bottom:.4rem;">Banned Users</div>
            <div style="font-size:1.5rem;font-weight:900;color:#f87171;">
                {{ \App\Models\User::where('status', 'banned')->count() }}
            </div>
        </div>
    </div>

    @if ($users->count() > 0)
        <div class="panel">
            <div style="overflow-x:auto;">
                <table class="admin-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>User Profile</th>
                            <th>Role & Status</th>
                            <th class="hide-mobile">Platform Usage</th>
                            <th class="hide-mobile">Joined</th>
                            <th style="text-align:right;">Access Control</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="user-row" data-username="{{ strtolower($user->username) }}" data-role="{{ $user->role }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:.75rem;">
                                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg, var(--color-gh-accent), #238636);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.8rem;color:#0d1117;">
                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-size:.8rem;font-weight:700;color:#fff;">{{ $user->username }}</div>
                                            <div style="font-size:.55rem;color:var(--color-gh-dim);font-family:monospace;">ID: #{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex;gap:.35rem;">
                                        <span class="status-badge {{ $user->role === 'admin' ? 'sb-online' : 'sb-unknown' }}" style="font-size:.5rem;padding:.1rem .35rem;">
                                            {{ $user->role }}
                                        </span>
                                        <span id="status-badge-{{ $user->id }}" class="status-badge {{ $user->status === 'active' ? 'sb-active' : 'sb-rejected' }}" style="font-size:.5rem;padding:.1rem .35rem;">
                                            {{ $user->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <div style="display:flex;gap:1rem;">
                                        <div title="Links Created">
                                            <div style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.05em;">Links</div>
                                            <div style="font-size:.7rem;font-weight:700;color:#fff;">{{ $user->links_count ?? $user->links()->count() }}</div>
                                        </div>
                                        <div title="Active Ads">
                                            <div style="font-size:.55rem;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.05em;">Ads</div>
                                            <div style="font-size:.7rem;font-weight:700;color:#fff;">{{ $user->advertisements_count ?? $user->advertisements()->count() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-mobile">
                                    <span style="font-size:.6rem;color:var(--color-gh-dim);">{{ $user->created_at->format('M d, Y') }}</span>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex;justify-content:flex-end;gap:.35rem;">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-sm" title="Modify User" style="color:var(--color-gh-accent);border-color:rgba(88,166,255,.2);text-decoration:none;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>

                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" style="display:inline;" onsubmit="return handleToggleStatus(event, {{ $user->id }}, '{{ route('admin.users.toggle-status', $user->id) }}')">
                                                @csrf
                                                <button type="submit" class="btn-sm" title="{{ $user->status === 'active' ? 'Ban User' : 'Unban User' }}" style="color:{{ $user->status === 'active' ? '#f87171' : '#4ade80' }};border-color:{{ $user->status === 'active' ? 'rgba(248,113,113,.2)' : 'rgba(74,222,128,.2)' }};">
                                                    @if($user->status === 'active')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                    @else
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                    @endif
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('EXTREME CAUTION: Permanently delete this account? All associated data will be orphaned.')">
                                                @csrf
                                                <button type="submit" class="btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.2);" title="Terminate Account">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div style="padding:.65rem 1rem;border-top:1px solid var(--color-gh-border);">
                    {{ $users->links('pagination.simple') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state" style="border:1px dashed var(--color-gh-border);border-radius:.6rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <p>No user accounts found matching your criteria.</p>
        </div>
    @endif

    <script>
        /**
         * Client-side Table Filtering (Fast Search)
         */
        function filterUsers() {
            const query = document.getElementById('userQuery').value.toLowerCase();
            const rows = document.querySelectorAll('.user-row');
            
            rows.forEach(row => {
                const username = row.getAttribute('data-username');
                const role = row.getAttribute('data-role');
                
                if (username.includes(query) || role.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        /**
         * AJAX Toggle Status (Premium UX)
         */
        function handleToggleStatus(event, userId, url) {
            // If the user is just using standard form, we can enhance it with fetch
            // but for simplicity and reliability in Tor browser environment, 
            // we'll stick to a confirm and standard submit unless we want to go full JS.
            // Let's implement a nice confirmation.
            
            const action = event.submitter.title.toLowerCase();
            if (!confirm(`Are you sure you want to ${action} this user?`)) {
                event.preventDefault();
                return false;
            }
            
            // Experimental: Could use fetch here to avoid reload, but let's keep it robust.
            return true;
        }

        // Add some micro-interactions
        document.getElementById('userQuery').addEventListener('focus', function() {
            this.style.borderColor = 'var(--color-gh-accent)';
            this.style.boxShadow = '0 0 0 3px rgba(88,166,255,.1)';
        });

        document.getElementById('userQuery').addEventListener('blur', function() {
            this.style.borderColor = 'var(--color-gh-border)';
            this.style.boxShadow = 'none';
        });
    </script>
</x-app.layouts>
