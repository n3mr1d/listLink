<x-app.layouts title="User Management">

    @include('admin._nav')

    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:1.5rem;">
        <div class="admin-header">
            <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.3rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.12em;">Identity Control</span>
            </div>
            <h1>User Command</h1>
            <p>Manage access levels and authority for the {{ config('app.name') }} ecosystem.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:.75rem 1rem;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.2);border-radius:.5rem;color:#4ade80;font-size:.7rem;font-weight:700;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="panel">
        <div class="panel-head">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Member Directory
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Authority</th>
                    <th>Assets</th>
                    <th>Joined</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr id="user-row-{{ $user->id }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem;">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg, var(--color-gh-accent), #3062af);display:flex;align-items:center;justify-content:center;font-weight:900;color:#0d1117;font-size:.8rem;text-transform:uppercase;">
                                    {{ substr($user->username, 0, 1) }}
                                </div>
                                <span style="font-weight:700;color:#fff;">{{ $user->username }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $user->isAdmin() ? 'sb-active' : 'sb-unknown' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:.75rem;font-size:.65rem;font-weight:700;color:var(--color-gh-dim);">
                                <span title="Links Submitted">🔗 {{ $user->links_count }}</span>
                                <span title="Ads Managed">📢 {{ $user->advertisements_count }}</span>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:.65rem;color:var(--color-gh-dim);">{{ $user->created_at->format('M d, Y') }}</span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;justify-content:flex-end;gap:.35rem;">
                                <button onclick="editUser({{ json_encode($user) }})" class="btn-sm" title="Modify Authority">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </button>
                                @if(auth()->id() !== $user->id)
                                    <button onclick="deleteUser({{ $user->id }}, '{{ $user->username }}')" class="btn-sm" style="color:#f87171;" title="Revoke Access">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div style="padding: 1rem 0;">
            {{ $users->links('pagination.simple') }}
        </div>
    @endif

    {{-- Edit Modal --}}
    <div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(13,17,23,.85);backdrop-filter:blur(8px);z-index:1000;align-items:center;justify-content:center;">
        <div style="width:100%;max-width:400px;background:#0d1117;border:1px solid var(--color-gh-border);border-radius:.8rem;box-shadow:0 20px 50px rgba(0,0,0,.5);overflow:hidden;animation:modalIn .3s cubic-bezier(0.16, 1, 0.3, 1);">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:.85rem;font-weight:900;text-transform:uppercase;letter-spacing:.05em;color:#fff;">Adjust Authority</h3>
                <button onclick="closeModal()" style="background:none;border:none;color:var(--color-gh-dim);cursor:pointer;padding:.25rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <form id="editForm" style="padding:1.5rem;">
                @csrf
                @method('PUT')
                <input type="hidden" id="userId">
                
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-size:.65rem;font-weight:800;text-transform:uppercase;color:var(--color-gh-dim);margin-bottom:.4rem;">Username</label>
                    <input type="text" id="username" name="username" required style="width:100%;background:rgba(48,54,61,.2);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.8rem;outline:none;transition:border-color .2s focus:border-color:var(--color-gh-accent);">
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-size:.65rem;font-weight:800;text-transform:uppercase;color:var(--color-gh-dim);margin-bottom:.4rem;">Authority Level</label>
                    <select id="role" name="role" required style="width:100%;background:rgba(48,54,61,.2);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.8rem;outline:none;appearance:none;cursor:pointer;">
                        <option value="user">USER (Member)</option>
                        <option value="admin">ADMIN (Overseer)</option>
                    </select>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-size:.65rem;font-weight:800;text-transform:uppercase;color:var(--color-gh-dim);margin-bottom:.4rem;">Reset Key (Optional)</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current" style="width:100%;background:rgba(48,54,61,.2);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.8rem;outline:none;">
                </div>

                <button type="submit" id="saveBtn" style="width:100%;padding:.75rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.4rem;font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;cursor:pointer;transition:transform .1s, opacity .1s;">
                    Synchronize Changes
                </button>
            </form>
        </div>
    </div>

    <style>
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238b949e' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
        }
        #saveBtn:active { transform: scale(0.98); opacity: 0.9; }
    </style>

    <script>
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        const saveBtn = document.getElementById('saveBtn');

        function editUser(user) {
            document.getElementById('userId').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('role').value = user.role;
            document.getElementById('password').value = '';
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        modal.onclick = (e) => {
            if (e.target === modal) closeModal();
        }

        form.onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('userId').value;
            const formData = new FormData(form);
            
            saveBtn.disabled = true;
            saveBtn.textContent = 'Processing...';

            try {
                const response = await fetch(`/admin/users/${id}`, {
                    method: 'POST', // Spoofed as PUT via _method in FormData
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    location.reload(); // Quickest way to update the complex UI
                } else {
                    alert(data.message || 'Error occurred');
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Synchronize Changes';
                }
            } catch (error) {
                console.error(error);
                alert('Connection failure.');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Synchronize Changes';
            }
        };

        async function deleteUser(id, username) {
            if (!confirm(`Are you sure you want to revoke access for "${username}"?`)) return;

            try {
                const response = await fetch(`/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById(`user-row-${id}`).style.opacity = '0';
                    setTimeout(() => document.getElementById(`user-row-${id}`).remove(), 300);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error(error);
                alert('Revoke operation failed.');
            }
        }
    </script>

</x-app.layouts>
