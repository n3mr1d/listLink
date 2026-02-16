<x-app.layouts title="Submit Link">
    <div class="page-full" style="max-width:700px;">
        <div class="page-header">
            <h1>Submit a Link</h1>
            <p>Submit a .onion link to be listed. All submissions are published automatically.</p>
        </div>

        {{-- Contact Announcement --}}
        <div class="alert alert-info" style="margin-bottom:1.5rem; border-left: 4px solid var(--accent-blue);">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <span style="font-size:1.2rem;"><i class="fa fa-envelope"></i></span>
                <div>
                    <strong style="color:var(--text-primary); font-size:0.9rem;">Want to advertise or have
                        suggestions?</strong>
                    <p style="margin:0; font-size:0.85rem;">
                        Contact us at <a href="mailto:treixnox@protonmail.com"
                            style="color:var(--accent-blue); font-weight:700;">treixnox@protonmail.com</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Info Banner --}}
        <div class="alert alert-info">
            @auth
                <span style="color:var(--accent-green);font-weight:700;">✓ Logged in as
                    {{ auth()->user()->username }}</span> — Your link will appear in both the <strong>Tor Directory</strong>
                (homepage) and the <strong>Search Engine</strong>.
            @else
                <span style="color:var(--accent-yellow);font-weight:700;">⚠ Submitting anonymously</span> — Your link will
                <strong>only</strong> appear in the <strong>Search Engine</strong>, not on the homepage directory.
                <a href="{{ route('login.form') }}">Log in</a> or
                <a href="{{ route('register.form') }}">register</a> to get full directory listing.
            @endauth
        </div>

        {{-- Crawler Pre-fill Form --}}
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-header" style="display:flex;align-items:center;gap:0.5rem;font-size:0.9rem;">
                <span>🔍</span> Auto-Crawl .onion URL
            </div>
            <div class="card-body">
                <p style="margin-bottom:0.75rem;font-size:0.85rem;color:var(--text-secondary);">
                    Submit the URL below and our Tor crawler will try to auto-fill the title and description for you.
                </p>
                <form action="{{ route('submit.crawl') }}" method="POST">
                    @csrf
                    <div style="display:flex;gap:0.5rem;align-items:flex-start;">
                        <div class="form-group" style="flex:1;margin-bottom:0;">
                            <input type="text" name="crawl_url"
                                value="{{ old('crawl_url', session('crawled_url', '')) }}"
                                placeholder="http://example1234abcdef.onion" required>
                        </div>
                        <button type="submit" class="btn btn-secondary">
                            Crawl
                        </button>
                    </div>
                    <div class="form-hint" style="margin-top:0.3rem;">
                        The crawler connects via Tor — may take up to 15 seconds. If it fails, you can fill in manually.
                    </div>
                </form>

                @if (session('crawl_result'))
                    <div
                        style="margin-top:0.75rem;padding:0.75rem;border-radius:6px;background:rgba(63,185,80,0.06);border:1px solid rgba(63,185,80,0.2);">
                        <div style="color:var(--accent-green);font-weight:700;font-size:0.75rem;margin-bottom:0.3rem;">✓
                            Crawl Successful</div>
                        @if (session('crawl_result.title'))
                            <div style="font-size:0.8rem;margin-bottom:0.2rem;"><strong
                                    style="color:var(--text-primary);">Title:</strong> {{ session('crawl_result.title') }}</div>
                        @endif
                        @if (session('crawl_result.description'))
                            <div style="font-size:0.8rem;"><strong style="color:var(--text-primary);">Description:</strong>
                                {{ Str::limit(session('crawl_result.description'), 120) }}</div>
                        @endif
                        <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.3rem;">These values have been
                            pre-filled in the form below. You can edit them.</div>
                    </div>
                @endif
                @if (session('crawl_error'))
                    <div
                        style="margin-top:0.75rem;padding:0.75rem;border-radius:6px;background:rgba(248,81,73,0.06);border:1px solid rgba(248,81,73,0.2);">
                        <div style="color:var(--accent-red);font-weight:700;font-size:0.75rem;">✗ Crawl Failed —
                            {{ session('crawl_error') }}
                        </div>
                        <div style="font-size:0.7rem;color:var(--text-muted);margin-top:0.2rem;">Please fill in the details
                            manually below.</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Submit Form --}}
        <div class="card">
            <div class="card-header">Link Details</div>
            <div class="card-body">
                <form action="{{ route('submit.store') }}" method="POST">
                    @csrf

                    {{-- Honeypot --}}
                    <div class="hp-field">
                        <label for="website_url_hp">Website</label>
                        <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" name="title" id="title"
                            value="{{ old('title', session('crawl_result.title', '')) }}"
                            placeholder="e.g., Private Email Service" required minlength="3" maxlength="100">
                        <div class="form-hint">3-100 characters. Descriptive name of the service.</div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"
                            placeholder="Describe the service, its purpose, and what users can expect..."
                            maxlength="500">{{ old('description', session('crawl_result.description', '')) }}</textarea>
                        <div class="form-hint">Optional. If left empty, our crawler will try to auto-fill when
                            submitting.</div>
                    </div>

                    <div class="form-group">
                        <label for="url">.onion URL *</label>
                        <input type="text" name="url" id="url" value="{{ old('url', session('crawled_url', '')) }}"
                            placeholder="http://example1234abcdef.onion" required>
                        <div class="form-hint">Must be a valid .onion URL. HTTP or HTTPS.</div>
                    </div>

                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select name="category" id="category" required>
                            <option value="">— Select Category —</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->value }}" {{ old('category') === $category->value ? 'selected' : '' }}>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="challenge">{{ $challenge }} *</label>
                        <input type="number" name="challenge" id="challenge" required placeholder="Your answer"
                            style="max-width:200px;">
                        <div class="form-hint">Anti-spam verification. Solve the math problem.</div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Publish Link</button>
                </form>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-header">Submission Guidelines</div>
            <div class="card-body">
                <ul
                    style="padding-left:1.25rem;list-style:disc;font-size:0.85rem;color:var(--text-secondary);line-height:1.6;">
                    <li>Only valid .onion URLs are accepted.</li>
                    <li>All links are published immediately — no approval queue.</li>
                    <li>Admin may delete inappropriate or duplicate links.</li>
                    <li>Provide an accurate and honest description.</li>
                    <li>Our crawler will attempt to auto-fill title and description from your .onion URL via Tor.</li>
                    <li>
                        @auth
                            <span style="color:var(--accent-green);">✓</span> You are logged in — your link will appear on
                            the homepage directory.
                        @else
                            <span style="color:var(--accent-yellow);">⚠</span> Anonymous links only appear in the Search
                            Engine.
                            <a href="{{ route('login.form') }}">Log in</a> for full directory listing.
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </div>

</x-app.layouts>