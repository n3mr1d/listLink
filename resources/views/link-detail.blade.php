<x-app.layouts title="{{ $link->title }} - Tor .Onion Directory"
    description="Details for {{ $link->title }}: {{ Str::limit($link->description, 150) }} - Verified .onion link on Hidden Line.">

    <div class="page-full">
        {{-- Breadcrumbs (SEO Friendly) --}}
        <nav style="margin-bottom: 1rem; font-size: 0.8rem; color: var(--text-muted);">
            <a href="{{ route('home') }}">Home</a> &raquo;
            <a href="{{ route('category.show', $link->category->value) }}">{{ $link->category->label() }}</a> &raquo;
            <span>{{ $link->title }}</span>
        </nav>

        {{-- Link Detail Card --}}
        <div class="link-detail">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
                <h1>{{ $link->title }}</h1>
                <div class="geo-tag" style="background:rgba(0,0,0,0.05); padding: 0.2rem 0.6rem; border-radius: 20px;">
                    <i class="fas fa-globe-americas"></i> Global / Multi-Regional
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.5rem; margin-bottom:1rem;">
                <div class="onion-v3">{{ $link->url }}</div>
                <button onclick="copyToClipboard('{{ $link->url }}')" class="btn btn-sm btn-secondary"
                    title="Copy Onion Address">
                    <i class="far fa-copy"></i>
                </button>
            </div>

            <script>
                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        alert('Onion address copied to clipboard!');
                    });
                }
            </script>

            <div class="link-description">{{ $link->description }}</div>

            <div class="link-meta">
                <dl>
                    <dt>Category</dt>
                    <dd><a
                            href="{{ route('category.show', $link->category->value) }}">{{ $link->category->label() }}</a>
                    </dd>
                </dl>
                <dl>
                    <dt>Status</dt>
                    <dd>
                        <span class="uptime-badge {{ $link->uptime_status->cssClass() }}">
                            {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                        </span>
                    </dd>
                </dl>
                <dl>
                    <dt>Last Checked</dt>
                    <dd>{{ $link->last_check ? $link->last_check->diffForHumans() : 'Never' }}</dd>
                </dl>
                <dl>
                    <dt>Times Checked</dt>
                    <dd>{{ $link->check_count }}</dd>
                </dl>
                <dl>
                    <dt>Submitted</dt>
                    <dd>{{ $link->created_at->diffForHumans() }}</dd>
                </dl>

                <dl>
                    <dt>Submitted By</dt>
                    <dd>{{ $link->user->username ?? 'Anonymous' }}</dd>
                </dl>




            </div>

            {{-- Check Status Button --}}
            <form action="{{ route('link.check', $link->id) }}" method="POST" style="margin-top:1rem;">
                @csrf
                <button type="submit" class="btn btn-primary">
                    &#9654; Check Status Now
                </button>
                <a class="btn text-white btn-secondary" href="{{ $link->url }}" rel="noreferrer noopener">
                    <i class="fas fa-external-link"></i>
                    Visit Now</a>

                <span class="text-muted" style="font-size:0.75rem;margin-left:0.5rem;">
                    Server connects via Tor proxy. May take up to 15 seconds.
                </span>
            </form>
        </div>

        {{-- Comments Section --}}
        <div class="comments-section">
            <h2>Comments ({{ $link->comments->count() }})</h2>

            @forelse($link->comments as $comment)
                <div class="comment">
                    <div class="comment-header">
                        <span class="comment-author">{{ $comment->username }}</span>
                        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="comment-body">{{ $comment->content }}</div>
                </div>
            @empty
                <p class="text-muted" style="font-size:0.875rem;">No comments yet. Be the first to comment.</p>
            @endforelse

            {{-- Add Comment Form --}}
            <div class="card mt-2">
                <div class="card-header">Add a Comment</div>
                <div class="card-body">
                    <form action="{{ route('link.comment', $link->id) }}" method="POST">
                        @csrf
                        {{-- Honeypot --}}
                        <div class="hp-field">
                            <label for="website_url_hp">Website</label>
                            <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1"
                                autocomplete="off">
                        </div>
                        @guest
                            <div class="form-group">
                                <label for="username">Name (optional) </label>
                                <input type="text" name="username" id="username" placeholder="Anonymous"
                                    value="{{ old('username') }}">
                            </div>
                        @endguest
                        @auth
                            <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                        @endauth
                        <div class="form-group">
                            <label for="content">Comment</label>
                            <textarea name="content" id="content" placeholder="Write your comment..."
                                required>{{ old('content') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="challenge">{{ $challenge }}</label>
                            <input type="number" name="challenge" id="challenge" required placeholder="Your answer"
                                style="max-width:150px;">
                            <div class="form-hint">Anti-spam verification</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app.layouts>