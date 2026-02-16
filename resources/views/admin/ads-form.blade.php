<x-app.layouts title="Admin - {{ isset($ad) ? 'Edit' : 'Create' }} Ad">

    <div class="page-header">
        <h1>{{ isset($ad) ? 'Edit' : 'Create' }} Advertisement</h1>
        <p>Manually manage advertisement details.</p>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.links') }}">Links</a>
        <a href="{{ route('admin.ads') }}" class="active">Ads</a>
        <a href="{{ route('admin.uptime-logs') }}">Uptime Logs</a>
        <a href="{{ route('admin.blacklist') }}">Blacklist</a>
    </nav>

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">{{ isset($ad) ? 'Update Ad: ' . $ad->title : 'New Advertisement' }}</div>
        <div class="card-body">
            <form action="{{ isset($ad) ? route('admin.ads.update', $ad->id) : route('admin.ads.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="ad-title">Ad Title *</label>
                    <input type="text" name="title" id="ad-title" value="{{ old('title', $ad->title ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="ad-url">URL *</label>
                    <input type="text" name="url" id="ad-url" value="{{ old('url', $ad->url ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="ad-description">Description</label>
                    <textarea name="description" id="ad-description"
                        rows="3">{{ old('description', $ad->description ?? '') }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="ad-type">Ad Type *</label>
                        <select name="ad_type" id="ad-type" required>
                            @foreach ($adTypes as $type)
                                <option value="{{ $type->value }}" {{ old('ad_type', $ad->ad_type->value ?? '') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ad-placement">Placement *</label>
                        <select name="placement" id="ad-placement" required>
                            @foreach ($placements as $placement)
                                <option value="{{ $placement->value }}" {{ old('placement', $ad->placement->value ?? '') === $placement->value ? 'selected' : '' }}>
                                    {{ $placement->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="ad-status">Status *</label>
                        <select name="status" id="ad-status" required>
                            <option value="pending" {{ old('status', $ad->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ old('status', $ad->status ?? '') === 'active' ? 'selected' : '' }}>
                                Active</option>
                            <option value="expired" {{ old('status', $ad->status ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="rejected" {{ old('status', $ad->status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ad-contact">Contact Information</label>
                        <input type="text" name="contact_info" id="ad-contact"
                            value="{{ old('contact_info', $ad->contact_info ?? '') }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="starts_at">Starts At</label>
                        <input type="datetime-local" name="starts_at" id="starts_at"
                            value="{{ old('starts_at', isset($ad->starts_at) ? $ad->starts_at->format('Y-m-d\TH:i') : '') }}">
                    </div>

                    <div class="form-group">
                        <label for="expires_at">Expires At</label>
                        <input type="datetime-local" name="expires_at" id="expires_at"
                            value="{{ old('expires_at', isset($ad->expires_at) ? $ad->expires_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ad-banner">Banner Image (optional)</label>
                    @if(isset($ad) && $ad->banner_path)
                        <div style="margin-bottom: 0.5rem;">
                            <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="Current Banner"
                                style="max-width: 200px; border-radius: 4px; border: 1px solid var(--border-color);">
                        </div>
                    @endif
                    <input type="file" name="banner" id="ad-banner">
                </div>

                <div style="display:flex; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary">{{ isset($ad) ? 'Update' : 'Create' }}
                        Advertisement</button>
                    <a href="{{ route('admin.ads') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-app.layouts>