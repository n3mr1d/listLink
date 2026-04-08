<x-app.layouts title="{{ $link->title }} - Tor .Onion Directory"
    description="Details for {{ $link->title }}: {{ Str::limit($link->description, 150) }} - Verified .onion link on Hidden Line.">

    <div class="detail-page-wrapper">
        {{-- Top Navigation --}}
        <nav class="breadcrumb-nav">
            <a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a>
            <span class="sep">/</span>
            <a href="{{ route('category.show', $link->category->value) }}">{{ $link->category->label() }}</a>
            <span class="sep">/</span>
            <span class="active">{{ $link->title }}</span>
        </nav>

        <div class="detail-grid">
            {{-- Main Content Column --}}
            <main class="detail-main">
                {{-- Hero Detail Card --}}
                <div class="detail-hero-card">
                    <div class="card-top">
                        <div class="badge-status-wrap">
                            <span class="uptime-badge {{ $link->uptime_status->cssClass() }}">
                                {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                            </span>
                            <span class="geo-badge"><i class="fas fa-globe"></i> Global</span>
                        </div>
                        <h1 class="premium-title">{{ $link->title }}</h1>
                    </div>

                    <div class="url-copy-box">
                        <div class="url-text">{{ $link->url }}</div>
                        <button onclick="copyToClipboard('{{ $link->url }}')" class="copy-btn" title="Copy Address">
                            <i class="far fa-copy"></i> Copy
                        </button>
                    </div>

                    <div class="hero-desc">
                        {{ $link->description ?? 'No detailed description available for this hidden service. This link has been verified by the Hidden Line automated directory system.' }}
                    </div>

                    <div class="action-buttons">
                        <a href="{{ $link->url }}" target="_blank" class="btn-main-action pulse">
                            <i class="fas fa-external-link-alt"></i> Visit Onion Service
                        </a>
                        <form action="{{ route('link.check', $link->id) }}" method="POST" class="inline-form">
                            @csrf
                            <button type="submit" class="btn-secondary-action">
                                <i class="fas fa-sync-alt"></i> Verify Status
                            </button>
                        </form>
                    </div>

                    <p class="service-note">
                        <i class="fas fa-info-circle"></i> Service connections are routed through multiple Tor relays for your privacy.
                    </p>
                </div>

                {{-- Comments Section --}}
                <section class="comments-section">
                    <div class="section-header">
                        <h2><i class="fas fa-comments"></i> Community Comments ({{ $link->comments->count() }})</h2>
                    </div>

                    <div class="comments-list">
                        @forelse($link->comments as $comment)
                            <div class="comment-card">
                                <div class="comment-header">
                                    <div class="author-meta">
                                        <div class="author-avatar">{{ strtoupper(substr($comment->username, 0, 1)) }}</div>
                                        <div class="author-info">
                                            <span class="author-name">{{ $comment->username }}</span>
                                            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="comment-id">#{{ $loop->iteration }}</div>
                                </div>
                                <div class="comment-body">{{ $comment->content }}</div>
                            </div>
                        @empty
                            <div class="empty-comments">
                                <i class="fas fa-comment-slash"></i>
                                <p>No comments yet. Share your experience with this service.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Add Comment --}}
                    <div class="add-comment-box">
                        <h3>Post a Comment</h3>
                        <form action="{{ route('link.comment', $link->id) }}" method="POST">
                            @csrf
                            {{-- Honeypot --}}
                            <div class="hp-field">
                                <label for="website_url_hp">Website</label>
                                <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="form-row">
                                @guest
                                    <div class="form-group">
                                        <input type="text" name="username" placeholder="Your Name (Optional)" value="{{ old('username') }}">
                                    </div>
                                @endguest
                                @auth
                                    <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                                @endauth
                                <div class="form-group">
                                    <input type="number" name="challenge" placeholder="Required: {{ $challenge }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <textarea name="content" placeholder="Write your comment here..." required>{{ old('content') }}</textarea>
                            </div>

                            <button type="submit" class="submit-comment-btn">Submit Comment</button>
                        </form>
                    </div>
                </section>
            </main>

            {{-- Sidebar Column --}}
            <aside class="detail-sidebar">
                {{-- Metadata Widget --}}
                <div class="sidebar-widget metadata-widget">
                    <h4>Technical Info</h4>
                    <div class="meta-list">
                        <div class="meta-item">
                            <span class="meta-label"><i class="fas fa-tag"></i> Category</span>
                            <span class="meta-val"><a href="{{ route('category.show', $link->category->value) }}">{{ $link->category->label() }}</a></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fas fa-clock"></i> First Seen</span>
                            <span class="meta-val">{{ $link->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fas fa-history"></i> Last Check</span>
                            <span class="meta-val">{{ $link->last_check ? $link->last_check->diffForHumans() : 'Never' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fas fa-check-double"></i> Total checks</span>
                            <span class="meta-val">{{ number_format($link->check_count) }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label"><i class="fas fa-user-edit"></i> Provider</span>
                            <span class="meta-val">{{ $link->user->username ?? 'Anonymous' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Safety Widget --}}
                <div class="sidebar-widget safety-widget">
                    <h4><i class="fas fa-user-shield"></i> Security Tip</h4>
                    <p>Always use a fresh Tor circuit when visiting financial services. Never provide your master password over `.onion` connections unless you've verified the PGP signature.</p>
                </div>

                {{-- Admin Ad placeholder --}}
                <div class="sidebar-ad">
                    <div class="ad-label">ADVERTISEMENT</div>
                    <a href="{{ route('advertise.create') }}" class="ad-placeholder-link">
                        <span>Advertise Your Service Here</span>
                        <small>Verified Tor Audience</small>
                    </a>
                </div>
            </aside>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.querySelector('.copy-btn');
                const original = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.classList.add('success');
                setTimeout(() => {
                    btn.innerHTML = original;
                    btn.classList.remove('success');
                }, 2000);
            });
        }
    </script>

    <style>
        :root {
            --detail-bg: #0d1117;
            --detail-card: #161b22;
            --detail-border: #30363d;
            --detail-accent: #58a6ff;
            --detail-text: #c9d1d9;
            --detail-text-dim: #8b949e;
        }

        .detail-page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* Breadcrumbs */
        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            font-size: 0.85rem;
            color: var(--detail-text-dim);
        }

        .breadcrumb-nav a { color: var(--detail-text-dim); text-decoration: none; transition: color 0.2s; }
        .breadcrumb-nav a:hover { color: var(--detail-accent); }
        .breadcrumb-nav .sep { opacity: 0.4; }
        .breadcrumb-nav .active { color: #fff; font-weight: 500; }

        /* Grid Layout */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2.5rem;
        }

        /* Main Card */
        .detail-hero-card {
            background: var(--detail-card);
            border: 1px solid var(--detail-border);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }

        .card-top { margin-bottom: 1.5rem; }

        .badge-status-wrap {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .geo-badge {
            font-size: 0.7rem;
            background: rgba(88, 166, 255, 0.1);
            color: var(--detail-accent);
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .premium-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            margin: 0;
            letter-spacing: -1px;
        }

        .url-copy-box {
            background: #0d1117;
            border: 1px solid var(--detail-border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 0.5rem 0.5rem 0.5rem 1.25rem;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }

        .url-text {
            font-family: 'JetBrains Mono', 'Monaco', monospace;
            font-size: 0.95rem;
            color: var(--detail-accent);
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .copy-btn {
            background: #21262d;
            border: 1px solid var(--detail-border);
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .copy-btn:hover { background: #30363d; border-color: #8b949e; }
        .copy-btn.success { background: #238636; border-color: #2ea043; }

        .hero-desc {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #bdc1c6;
            margin-bottom: 2.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-main-action {
            background: #238636;
            color: #fff;
            text-decoration: none;
            padding: 0.85rem 1.75rem;
            border-radius: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }

        .btn-main-action:hover { background: #2ea043; transform: translateY(-2px); }

        .btn-secondary-action {
            background: #21262d;
            color: var(--detail-text);
            border: 1px solid var(--detail-border);
            padding: 0.85rem 1.75rem;
            border-radius: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary-action:hover { background: #30363d; border-color: #8b949e; }

        .service-note {
            font-size: 0.8rem;
            color: var(--detail-text-dim);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Comments */
        .comments-section { margin-bottom: 3rem; }
        .section-header h2 { font-size: 1.35rem; color: #fff; margin-bottom: 2rem; }

        .comments-list { display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 3rem; }
        .comment-card {
            background: rgba(22, 27, 34, 0.5);
            border: 1px solid var(--detail-border);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .author-meta { display: flex; gap: 0.75rem; align-items: center; }
        .author-avatar {
            width: 40px;
            height: 40px;
            background: var(--detail-accent);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .author-info { display: flex; flex-direction: column; }
        .author-name { font-weight: 700; color: #fff; font-size: 0.95rem; }
        .comment-date { font-size: 0.75rem; color: var(--detail-text-dim); }

        .comment-id { font-size: 0.75rem; color: var(--detail-text-dim); opacity: 0.5; }

        .comment-body { line-height: 1.6; color: #bdc1c6; }

        .empty-comments {
            padding: 4rem 2rem;
            text-align: center;
            color: var(--detail-text-dim);
            background: rgba(0,0,0,0.1);
            border-radius: 12px;
            border: 1px dashed var(--detail-border);
        }

        .empty-comments i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.3; }

        /* Add Comment Form */
        .add-comment-box {
            background: var(--detail-card);
            border: 1px solid var(--detail-border);
            border-radius: 16px;
            padding: 2rem;
        }

        .add-comment-box h3 { font-size: 1.25rem; color: #fff; margin-bottom: 1.5rem; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }

        .add-comment-box input, .add-comment-box textarea {
            width: 100%;
            background: #0d1117;
            border: 1px solid var(--detail-border);
            color: #fff;
            padding: 0.85rem 1.25rem;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.2s;
        }

        .add-comment-box input:focus, .add-comment-box textarea:focus { border-color: var(--detail-accent); }
        .add-comment-box textarea { min-height: 120px; resize: vertical; }

        .submit-comment-btn {
            background: var(--detail-accent);
            color: #0d1117;
            border: none;
            padding: 0.85rem 2rem;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .submit-comment-btn:hover { background: #79c0ff; transform: translateY(-1px); }

        /* Sidebar */
        .sidebar-widget {
            background: var(--detail-card);
            border: 1px solid var(--detail-border);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .sidebar-widget h4 { font-size: 1rem; color: #fff; margin-bottom: 1.25rem; }

        .meta-list { display: flex; flex-direction: column; gap: 1rem; }
        .meta-item { display: flex; flex-direction: column; gap: 0.25rem; }
        .meta-label { font-size: 0.75rem; color: var(--detail-text-dim); text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-label i { width: 1.25rem; }
        .meta-val { font-size: 0.95rem; color: #fff; font-weight: 500; }
        .meta-val a { color: var(--detail-accent); text-decoration: none; }

        .safety-widget {
            background: linear-gradient(135deg, #161b22 0%, #1c2128 100%);
            border-left: 4px solid #f85149;
        }

        .safety-widget p { font-size: 0.85rem; color: var(--detail-text-dim); line-height: 1.5; margin: 0; }

        .sidebar-ad {
            border: 1px dashed var(--detail-border);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .ad-label { font-size: 0.6rem; color: var(--detail-text-dim); margin-bottom: 1rem; font-weight: 700; }
        .ad-placeholder-link { text-decoration: none; display: flex; flex-direction: column; gap: 0.25rem; transition: transform 0.2s; }
        .ad-placeholder-link:hover { transform: scale(1.05); }
        .ad-placeholder-link span { color: var(--detail-accent); font-weight: 700; font-size: 0.95rem; }
        .ad-placeholder-link small { color: var(--detail-text-dim); font-size: 0.75rem; }

        /* Pulse Animation */
        .pulse { animation: shadow-pulse 2s infinite; }
        @keyframes shadow-pulse {
            0% { box-shadow: 0 0 0 0 rgba(35, 134, 54, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(35, 134, 54, 0); }
            100% { box-shadow: 0 0 0 0 rgba(35, 134, 54, 0); }
        }

        @media (max-width: 900px) {
            .detail-grid { grid-template-columns: 1fr; }
            .detail-hero-card { padding: 1.5rem; }
            .premium-title { font-size: 1.8rem; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</x-app.layouts>