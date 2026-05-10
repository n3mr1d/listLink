<x-app.layouts title="Admin - Edit User #{{ $user->id }}">

    @include('admin._nav')

    <div class="admin-header"
        style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>Edit Account: {{ $user->username }}</h1>
            <p>Update credentials, change system roles, or modify account standing.</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
            style="font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:.3rem;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            Back to Accounts
        </a>
    </div>

    <div style="max-width:800px; display: grid; grid-template-columns: 300px 1fr; gap: 1.5rem;">
        {{-- Profile Sidebar --}}
        <div>
            <div class="panel" style="padding: 1.5rem; text-align: center; background: rgba(255,255,255,.01);">
                <div
                    style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg, var(--color-gh-accent), #238636);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:2rem;color:#0d1117;margin: 0 auto 1rem;">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>
                <h3 style="margin:0; font-size: 1.1rem; color: #fff;">{{ $user->username }}</h3>
                <p
                    style="font-size: .6rem; color: var(--color-gh-dim); text-transform: uppercase; letter-spacing: .05em; margin: .25rem 0 1rem;">
                    Joined {{ $user->created_at->format('M Y') }}</p>

                <div
                    style="display: flex; flex-direction: column; gap: .5rem; text-align: left; padding-top: 1rem; border-top: 1px solid var(--color-gh-border);">
                    <div style="display:flex; justify-content: space-between;">
                        <span style="font-size:.55rem; color: var(--color-gh-dim);">Status</span>
                        <span class="status-badge {{ $user->status === 'active' ? 'sb-active' : 'sb-rejected' }}"
                            style="font-size:.5rem; padding: .05rem .3rem;">{{ $user->status }}</span>
                    </div>
                    <div style="display:flex; justify-content: space-between;">
                        <span style="font-size:.55rem; color: var(--color-gh-dim);">Role</span>
                        <span
                            style="font-size:.6rem; font-weight: 800; color: {{ $user->role === 'admin' ? 'var(--color-gh-accent)' : '#fff' }};">{{ ucfirst($user->role) }}</span>
                    </div>
                    <div style="display:flex; justify-content: space-between;">
                        <span style="font-size:.55rem; color: var(--color-gh-dim);">Total Links</span>
                        <span
                            style="font-size:.6rem; font-weight: 800; color: #fff;">{{ $user->links()->count() }}</span>
                    </div>
                    <div style="display:flex; justify-content: space-between;">
                        <span style="font-size:.55rem; color: var(--color-gh-dim);">Email</span>
                        <span
                            style="font-size:.6rem; font-weight: 800; color: #fff;">{{ $user->email ?? 'No email' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Area --}}
        <div>
            <div class="panel">
                <div class="panel-head">Account Configuration</div>
                <div style="padding: 1.5rem;">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf

                        <div style="margin-bottom: 1.25rem;">
                            <label
                                style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;font-size:.8rem;color:#fff;outline:none;">
                            @error('username') <p style="color:#f87171; font-size:.55rem; margin-top:.3rem;">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                            <div>
                                <label
                                    style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;">System
                                    Role</label>
                                <select name="role"
                                    style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;font-size:.75rem;font-weight:700;color:#fff;outline:none;">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>USER</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>ADMINISTRATOR
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;">Account
                                    Standing</label>
                                <select name="status"
                                    style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;font-size:.75rem;font-weight:700;color:#fff;outline:none;">
                                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>ACTIVE /
                                        GOOD STANDING</option>
                                    <option value="banned" {{ $user->status === 'banned' ? 'selected' : '' }}>BANNED /
                                        RESTRICTED</option>
                                </select>
                            </div>
                        </div>

                        <div
                            style="margin-bottom: 1.5rem; padding: 1rem; border: 1px solid rgba(248,113,113,.1); border-radius: .5rem; background: rgba(248,113,113,.02);">
                            <label
                                style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#f87171;display:block;margin-bottom:.4rem;">Security
                                Override (Password)</label>
                            <input type="password" name="password" placeholder="Leave empty to keep existing password"
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;font-size:.8rem;color:#fff;outline:none;">
                            <p style="font-size: .55rem; color: var(--color-gh-dim); margin-top: .4rem;">Only change
                                this if the user has lost access or for emergency administration.</p>
                            @error('password') <p style="color:#f87171; font-size:.55rem; margin-top:.3rem;">
                                {{ $message }}
                            </p> @enderror
                        </div>

                        <div style="display:flex; gap: .75rem;">
                            <button type="submit"
                                style="flex:1;padding:.75rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;cursor:pointer;">
                                Commit Changes
                            </button>
                            <a href="{{ route('admin.users.index') }}"
                                style="padding:.75rem 1.5rem;border:1px solid var(--color-gh-border);border-radius:.4rem;font-size: .65rem; font-weight: 800; color: var(--color-gh-dim); text-decoration: none; text-transform: uppercase;">Discard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app.layouts>