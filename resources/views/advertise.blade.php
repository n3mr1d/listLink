<x-app.layouts title="Advertise">

    <div class="page-full" style="max-width:900px;">
        <div class="page-header">
            <h1>Advertise on Hidden Line</h1>
            <p>Promote your .onion service to a privacy-conscious audience.</p>
        </div>

        {{-- Contact Announcement --}}
        <div class="alert alert-info" style="margin-bottom:2rem; border-left: 4px solid var(--accent-blue);">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <span style="font-size:1.5rem;"><i class="fa fa-envelope"></i></span>
                <div>
                    <strong style="color:var(--text-primary); display:block; margin-bottom:0.25rem;">Want to advertise
                        or have suggestions?</strong>
                    <p style="margin:0; font-size:0.9rem;">
                        For advertisement inquiries or any suggestions to improve Hidden Line, please contact us
                        directly at:
                        <a href="mailto:treixnox@protonmail.com"
                            style="color:var(--accent-blue); font-weight:700; text-decoration:underline;">treixnox@protonmail.com</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══ Visual Ad Examples ═══ --}}
        <div style="margin-bottom:2rem;">
            <h2 style="font-size:1.1rem;margin-bottom:1rem;color:var(--text-primary);">Ad Types & Examples</h2>

            {{-- 1. Header Banner --}}
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:0.9rem;margin-bottom:0.1rem;">Header Banner</h3>
                        <p style="font-size:0.7rem;color:var(--text-muted);font-weight:400;">Full-width banner at the
                            top of every page</p>
                    </div>
                    <span
                        style="padding:0.2rem 0.5rem;border-radius:4px;font-size:0.7rem;font-weight:700;background:rgba(63,185,80,0.15);color:var(--accent-green);border:1px solid rgba(63,185,80,0.3);">
                        728 × 90 px
                    </span>
                </div>
                <div class="card-body">
                    {{-- Example Preview --}}
                    <div
                        style="position:relative;border-radius:6px;overflow:hidden;border:1px solid var(--border-color);background:linear-gradient(135deg, #1a2332 0%, #0d1117 100%);min-height:90px;display:flex;align-items:center;justify-content:center;padding:1.5rem 1rem;">
                        <span
                            style="position:absolute;top:0.4rem;right:0.4rem;background:rgba(0,0,0,0.7);color:var(--text-muted);padding:0.1rem 0.4rem;border-radius:3px;font-size:0.6rem;font-weight:700;text-transform:uppercase;">Sponsored</span>
                        <div style="text-align:center;">
                            <div style="font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:0.2rem;">Your Service
                                Name Here</div>
                            <div style="font-family:var(--font-mono);font-size:0.75rem;color:var(--accent-cyan);">
                                http://yourservice.onion</div>
                        </div>
                    </div>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;">
                        <strong style="color:var(--text-secondary);">Recommended:</strong> 728×90px or 970×90px image
                        (PNG/WebP). Maximum visibility — displayed above all content.
                    </p>
                </div>
            </div>

            {{-- 2. Sidebar Banner --}}
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:0.9rem;margin-bottom:0.1rem;">Sidebar Banner</h3>
                        <p style="font-size:0.7rem;color:var(--text-muted);font-weight:400;">Sidebar placement visible
                            on homepage</p>
                    </div>
                    <span
                        style="padding:0.2rem 0.5rem;border-radius:4px;font-size:0.7rem;font-weight:700;background:rgba(88,166,255,0.15);color:var(--accent-blue);border:1px solid rgba(88,166,255,0.3);">
                        300 × 250 px
                    </span>
                </div>
                <div class="card-body">
                    <div style="max-width:300px;margin:0 auto;">
                        <div
                            style="position:relative;border-radius:6px;overflow:hidden;border:1px solid var(--border-color);background:linear-gradient(180deg, #161b22 0%, #0d1117 100%);min-height:250px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:1rem;">
                            <span
                                style="position:absolute;top:0.4rem;right:0.4rem;background:rgba(0,0,0,0.7);color:var(--text-muted);padding:0.1rem 0.4rem;border-radius:3px;font-size:0.6rem;font-weight:700;text-transform:uppercase;">Ad</span>
                            <div
                                style="width:48px;height:48px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:900;color:var(--bg-primary);margin-bottom:0.75rem;">
                                <span class="text-white">HL</span>
                            </div>
                            <div style="font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:0.2rem;">Your Banner
                                Ad</div>
                            <div
                                style="font-size:0.75rem;color:var(--text-muted);text-align:center;margin-bottom:0.75rem;">
                                Promote your .onion service in the sidebar</div>
                            <div style="font-family:var(--font-mono);font-size:0.65rem;color:var(--accent-cyan);">
                                http://example.onion</div>
                        </div>
                    </div>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;text-align:center;">
                        <strong style="color:var(--text-secondary);">Recommended:</strong> 300×250px image (PNG/WebP).
                        Persistent sidebar visibility.
                    </p>
                </div>
            </div>

            {{-- 3. Sponsored Link --}}
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:0.9rem;margin-bottom:0.1rem;">Sponsored Link</h3>
                        <p style="font-size:0.7rem;color:var(--text-muted);font-weight:400;">Appears within link
                            listings with "Sponsored" label</p>
                    </div>
                    <span
                        style="padding:0.2rem 0.5rem;border-radius:4px;font-size:0.7rem;font-weight:700;background:rgba(188,140,255,0.15);color:var(--accent-purple);border:1px solid rgba(188,140,255,0.3);">
                        Text Only
                    </span>
                </div>
                <div class="card-body">
                    <table class="links-table" style="max-width:600px;margin-bottom:0;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th style="width:100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="link-title">
                                    <a href="#">Regular Link Example</a>
                                </td>
                                <td><span class="uptime-badge uptime-online">● Online</span></td>
                            </tr>
                            <tr class="sponsored-row">
                                <td class="link-title">
                                    <span class="badge-sponsored">Ad</span>
                                    <a href="#">Your Service Name Here</a>
                                </td>
                                <td><span class="badge-sponsored">Sponsored</span></td>
                            </tr>
                            <tr>
                                <td class="link-title">
                                    <a href="#">Another Regular Link</a>
                                </td>
                                <td><span class="uptime-badge uptime-online">● Online</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;">
                        <strong style="color:var(--text-secondary);">Recommended:</strong> Text-based. No image
                        required. Blends within directory listings.
                    </p>
                </div>
            </div>

            {{-- 4. Featured Spot --}}
            <div class="card" style="margin-bottom:1.5rem;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <h3 style="font-size:0.9rem;margin-bottom:0.1rem;">Featured Spot</h3>
                        <p style="font-size:0.7rem;color:var(--text-muted);font-weight:400;">Pinned to the top of a
                            category page</p>
                    </div>
                    <span
                        style="padding:0.2rem 0.5rem;border-radius:4px;font-size:0.7rem;font-weight:700;background:rgba(210,153,34,0.15);color:var(--accent-yellow);border:1px solid rgba(210,153,34,0.3);">
                        468 × 60 px
                    </span>
                </div>
                <div class="card-body">
                    <div style="max-width:468px;margin:0 auto;">
                        <div
                            style="position:relative;border-radius:6px;overflow:hidden;background:rgba(210,153,34,0.05);border:1px dashed rgba(210,153,34,0.3);min-height:60px;padding:0.75rem;display:flex;align-items:center;gap:0.75rem;">
                            <span
                                style="position:absolute;top:0.4rem;right:0.4rem;background:rgba(210,153,34,0.2);color:var(--accent-yellow);padding:0.1rem 0.4rem;border-radius:3px;font-size:0.6rem;font-weight:700;text-transform:uppercase;">Featured</span>
                            <div
                                style="width:32px;height:32px;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:700;background:rgba(210,153,34,0.2);color:var(--accent-yellow);flex-shrink:0;">
                                ★</div>
                            <div>
                                <div style="font-size:0.85rem;font-weight:700;color:var(--text-primary);">Your Featured
                                    Service</div>
                                <div style="font-family:var(--font-mono);font-size:0.65rem;color:var(--accent-cyan);">
                                    http://featured.onion</div>
                            </div>
                        </div>
                    </div>
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-top:0.75rem;text-align:center;">
                        <strong style="color:var(--text-secondary);">Recommended:</strong> 468×60px image or text-only.
                        Pinned at the top of your chosen category.
                    </p>
                </div>
            </div>


            {{-- Pricing Note --}}
            <div
                style="padding:0.75rem;border-radius:6px;text-align:center;font-size:0.85rem;background:rgba(88,166,255,0.05);border:1px solid rgba(88,166,255,0.15);color:var(--text-secondary);">
                Contact us for pricing. Payment via <strong style="color:var(--text-primary);">BTC</strong> or <strong
                    style="color:var(--text-primary);">ETH</strong>. All ads are clearly labeled as "Sponsored."
            </div>
        </div>

        {{-- ═══ Submission Form ═══ --}}
        <div class="card">
            <div class="card-header">Submit Ad Request</div>
            <div class="card-body">
                <form action="{{ route('advertise.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Honeypot --}}
                    <div class="hp-field">
                        <label for="website_url_hp">Website</label>
                        <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="ad-title">Ad Title *</label>
                        <input type="text" name="title" id="ad-title" value="{{ old('title') }}"
                            placeholder="Your service name" required minlength="3" maxlength="100">
                    </div>

                    <div class="form-group">
                        <label for="ad-url">.onion URL *</label>
                        <input type="text" name="url" id="ad-url" value="{{ old('url') }}"
                            placeholder="http://yourservice.onion" required>
                        <div class="form-hint">Must be a valid .onion URL.</div>
                    </div>

                    <div class="form-group">
                        <label for="ad-type">Ad Type *</label>
                        <select name="ad_type" id="ad-type" required>
                            @foreach ($adTypes as $type)
                                <option value="{{ $type->value }}" {{ old('ad_type') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-hint">See examples above to understand each type.</div>
                    </div>

                    <div class="form-group">
                        <label for="ad-placement">Preferred Placement *</label>
                        <select name="placement" id="ad-placement" required>
                            @foreach ($placements as $placement)
                                <option value="{{ $placement->value }}" {{ old('placement') === $placement->value ? 'selected' : '' }}>
                                    {{ $placement->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ad-banner">Banner Image (optional)</label>
                        <input type="file" name="banner" id="ad-banner"
                            accept="image/png,image/jpg,image/jpeg,image/gif,image/webp">
                        <div class="form-hint">
                            Max 512KB. PNG, JPG, GIF, or WebP.<br>
                            Recommended sizes: Header 728×90px · Sidebar 300×250px · Featured 468×60px
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ad-contact">Contact Information *</label>
                        <input type="text" name="contact_info" id="ad-contact" value="{{ old('contact_info') }}"
                            placeholder="Email, XMPP, or Session ID for payment discussion" required>
                    </div>

                    <div class="form-group">
                        <label for="ad-challenge">{{ $challenge }} *</label>
                        <input type="number" name="challenge" id="ad-challenge" required placeholder="Your answer"
                            style="max-width:150px;">
                        <div class="form-hint">Anti-spam verification.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Submit Ad Request</button>
                </form>
            </div>
        </div>
    </div>

</x-app.layouts>