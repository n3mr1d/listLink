<x-app.layouts title="Admin - {{ isset($ad) ? 'Edit' : 'Create' }} Ad Campaign">

    @include('admin._nav')

    <div class="admin-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.75rem;">
        <div>
            <h1>{{ isset($ad) ? 'Edit Campaign' : 'Deploy New Ad' }}</h1>
            <p>Define execution parameters and creative assets for network placement.</p>
        </div>
        <a href="{{ route('admin.ads') }}" style="font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;letter-spacing:.06em;display:flex;align-items:center;gap:.3rem;">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to Queue
        </a>
    </div>

    <div style="max-width:800px;">
        <div class="panel">
            <div class="panel-head" style="justify-content:space-between;">
                <span>Campaign Config</span>
                @if(isset($ad))
                    <span style="font-size:.55rem;color:var(--color-gh-accent);border:1px solid rgba(88,166,255,.2);padding:.1rem .35rem;border-radius:.25rem;">Record #{{ $ad->id }}</span>
                @endif
            </div>
            <div style="padding:1rem;">
                <form action="{{ isset($ad) ? route('admin.ads.update', $ad->id) : route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem;">
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Title *</label>
                            <input type="text" name="title" value="{{ old('title', $ad->title ?? '') }}" required
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.78rem;color:#fff;outline:none;"
                                placeholder="Campaign headline">
                        </div>
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">URL *</label>
                            <input type="text" name="url" value="{{ old('url', $ad->url ?? '') }}" required
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.78rem;color:#fff;outline:none;font-family:monospace;"
                                placeholder="https://node.onion">
                        </div>
                    </div>

                    <div style="margin-bottom:.75rem;">
                        <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Description</label>
                        <textarea name="description" rows="3"
                            style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.78rem;color:#fff;outline:none;resize:none;line-height:1.5;"
                            placeholder="Service value proposition...">{{ old('description', $ad->description ?? '') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.6rem;margin-bottom:.75rem;">
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Tier *</label>
                            <select name="ad_type" required
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.7rem;font-weight:700;color:#fff;outline:none;text-transform:uppercase;">
                                @foreach ($adTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('ad_type', $ad->ad_type->value ?? '') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Placement *</label>
                            <select name="placement" required
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.7rem;font-weight:700;color:#fff;outline:none;text-transform:uppercase;">
                                @foreach ($placements as $placement)
                                    <option value="{{ $placement->value }}" {{ old('placement', $ad->placement->value ?? '') === $placement->value ? 'selected' : '' }}>{{ $placement->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Status *</label>
                            <select name="status" required
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.7rem;font-weight:700;color:#fff;outline:none;text-transform:uppercase;">
                                @foreach(['pending', 'active', 'expired', 'rejected'] as $st)
                                    <option value="{{ $st }}" {{ old('status', $ad->status ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.6rem;margin-bottom:.75rem;">
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Contact</label>
                            <input type="text" name="contact_info" value="{{ old('contact_info', $ad->contact_info ?? '') }}"
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.78rem;color:#fff;outline:none;font-family:monospace;"
                                placeholder="Identity handle">
                        </div>
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Activation</label>
                            <input type="datetime-local" name="starts_at"
                                value="{{ old('starts_at', isset($ad->starts_at) ? $ad->starts_at->format('Y-m-d\TH:i') : '') }}"
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.65rem;color:#fff;outline:none;text-transform:uppercase;">
                        </div>
                        <div>
                            <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.3rem;">Expiration</label>
                            <input type="datetime-local" name="expires_at"
                                value="{{ old('expires_at', isset($ad->expires_at) ? $ad->expires_at->format('Y-m-d\TH:i') : '') }}"
                                style="width:100%;background:var(--color-gh-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.5rem .75rem;font-size:.65rem;color:#fff;outline:none;text-transform:uppercase;">
                        </div>
                    </div>

                    {{-- Banner Upload --}}
                    <div style="border-top:1px solid var(--color-gh-border);padding-top:.75rem;margin-bottom:.75rem;">
                        <label style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:var(--color-gh-dim);display:block;margin-bottom:.4rem;">Banner Image</label>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            @if(isset($ad) && $ad->banner_path)
                                <div style="border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.3rem;">
                                    <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="Banner" style="width:80px;height:auto;border-radius:.3rem;display:block;" loading="lazy">
                                </div>
                            @endif
                            <label style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed var(--color-gh-border);border-radius:.5rem;padding:1rem;cursor:pointer;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="2" style="margin-bottom:.3rem;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.06em;">Upload Image</span>
                                <span style="font-size:.5rem;color:var(--color-gh-dim);opacity:.5;margin-top:.15rem;">JPG, PNG, GIF (max 2MB)</span>
                                <input type="file" name="banner" style="display:none;">
                            </label>
                        </div>
                    </div>

                    <div style="display:flex;gap:.5rem;padding-top:.5rem;">
                        <button type="submit" style="flex:1;padding:.6rem;background:var(--color-gh-accent);color:#0d1117;border:none;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;">
                            {{ isset($ad) ? 'Save Changes' : 'Deploy Ad' }}
                        </button>
                        <a href="{{ route('admin.ads') }}" style="padding:.6rem 1rem;border:1px solid var(--color-gh-border);border-radius:.4rem;font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-decoration:none;text-transform:uppercase;display:flex;align-items:center;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app.layouts>