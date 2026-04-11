<x-app.layouts title="Submit link">

    <style>
        .submit-layout { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        @media (min-width: 768px) { .submit-layout { grid-template-columns: 1fr 280px; } }
    </style>

    <div style="max-width:900px;margin:0 auto;padding:1rem 0 3rem;">

        {{-- Page header --}}
        <div style="text-align:center;margin-bottom:1.75rem;">
            <h1 style="font-size:1.6rem;font-weight:900;color:#fff;letter-spacing:-.02em;margin:0 0 .3rem;">Publish your Service</h1>
            <p style="color:var(--color-gh-dim);font-size:.85rem;margin:0;">Expand the dark web. Distribute your .onion link to the network.</p>
        </div>

        <div class="submit-layout">

            {{-- Left: info + forms --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">

                {{-- Auth banner --}}
                <div style="border:1px solid {{ auth()->check() ? 'rgba(88,166,255,.25)' : 'rgba(251,146,60,.25)' }};background:{{ auth()->check() ? 'rgba(88,166,255,.05)' : 'rgba(251,146,60,.05)' }};border-radius:.5rem;padding:.85rem 1rem;display:flex;align-items:flex-start;gap:.65rem;">
                    <div style="width:1.8rem;height:1.8rem;border-radius:50%;background:{{ auth()->check() ? 'rgba(88,166,255,.15)' : 'rgba(251,146,60,.15)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.85rem;">
                        {{ auth()->check() ? '✓' : '⚠' }}
                    </div>
                    <div style="font-size:.8rem;">
                        @auth
                            <p style="color:#fff;font-weight:700;margin:0 0 .15rem;">Authenticated Session</p>
                            <p style="color:var(--color-gh-dim);margin:0;line-height:1.5;">Logged in as <span style="color:var(--color-gh-accent);">{{ auth()->user()->username }}</span>. Your link will be featured in the <strong style="color:#fff;">Global Directory</strong> and Search Engine.</p>
                        @else
                            <p style="color:#fb923c;font-weight:700;margin:0 0 .15rem;">Anonymous Mode</p>
                            <p style="color:var(--color-gh-dim);margin:0;line-height:1.5;">Your link will <span style="background:rgba(251,146,60,.2);padding:.05rem .3rem;border-radius:.2rem;">only</span> be indexed by the <strong style="color:#fff;">Search Engine</strong>. <a href="{{ route('login.form') }}" style="color:var(--color-gh-accent);">Log in</a> for directory listing.</p>
                        @endauth
                    </div>
                </div>

                {{-- Crawler --}}
                <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;overflow:hidden;">
                    <div style="padding:.7rem 1rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;gap:.4rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
                        <span style="font-size:.7rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.1em;">Tor Assistant</span>
                    </div>
                    <div style="padding:.9rem 1rem;">
                        <p style="font-size:.75rem;color:var(--color-gh-dim);margin:0 0 .65rem;line-height:1.5;">Let our crawler fetch the title and metadata for you to save time.</p>
                        <form action="{{ route('submit.crawl') }}" method="POST">
                            @csrf
                            <div style="display:flex;gap:.5rem;">
                                <input type="text" name="crawl_url" value="{{ old('crawl_url', session('crawled_url', '')) }}" placeholder="http://v3-onion-address.onion" required
                                    style="flex:1;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.55rem .75rem;color:#fff;font-size:.82rem;outline:none;min-width:0;">
                                <button type="submit" style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);color:#fff;padding:.55rem 1rem;border-radius:.4rem;font-size:.8rem;font-weight:700;cursor:pointer;white-space:nowrap;">Crawl</button>
                            </div>
                            <p style="font-size:.62rem;color:rgba(125,133,144,.5);font-style:italic;margin:.4rem 0 0;">🛡 Crawler uses multiple hops for anonymity. Max timeout 15s.</p>
                        </form>

                        @if (session('crawl_result'))
                            <div style="margin-top:.75rem;border:1px solid rgba(74,222,128,.25);background:rgba(74,222,128,.05);border-radius:.4rem;padding:.75rem;">
                                <div style="font-size:.62rem;font-weight:800;color:#4ade80;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem;">✓ Sync Successful</div>
                                @if (session('crawl_result.title'))
                                    <div style="font-size:.75rem;"><span style="color:var(--color-gh-dim);font-weight:700;margin-right:.4rem;">Title:</span><span style="color:#fff;">{{ session('crawl_result.title') }}</span></div>
                                @endif
                                @if (session('crawl_result.description'))
                                    <div style="font-size:.75rem;margin-top:.2rem;"><span style="color:var(--color-gh-dim);font-weight:700;margin-right:.4rem;">Info:</span><span style="color:#fff;">{{ Str::limit(session('crawl_result.description'), 100) }}</span></div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit Form --}}
                <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:1.25rem;">
                    <form action="{{ route('submit.store') }}" method="POST" style="display:flex;flex-direction:column;gap:1rem;">
                        @csrf
                        <div style="display:none;"><input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off"></div>

                        <div style="display:flex;flex-direction:column;gap:.3rem;">
                            <label for="title" style="font-size:.7rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;">Service Hub Name *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', session('crawl_result.title', '')) }}" placeholder="e.g., Hidden Wiki Clone" required minlength="3" maxlength="100"
                                style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.85rem;outline:none;width:100%;box-sizing:border-box;">
                        </div>

                        <div style="display:flex;flex-direction:column;gap:.3rem;">
                            <label for="url" style="font-size:.7rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;">Onion Address *</label>
                            <input type="text" name="url" id="url" value="{{ old('url', session('crawled_url', '')) }}" placeholder="http://...onion" required
                                style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.82rem;font-family:monospace;outline:none;width:100%;box-sizing:border-box;">
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                            <div style="display:flex;flex-direction:column;gap:.3rem;">
                                <label for="category" style="font-size:.7rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;">Category *</label>
                                <select name="category" id="category" required
                                    style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.82rem;outline:none;width:100%;appearance:none;">
                                    <option value="">— Choose —</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->value }}" {{ old('category') === $category->value ? 'selected' : '' }}>{{ $category->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:.3rem;">
                                <label for="challenge" style="font-size:.7rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;">Human Test: {{ $challenge }}</label>
                                <input type="number" name="challenge" id="challenge" required placeholder="Result"
                                    style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.85rem;outline:none;width:100%;box-sizing:border-box;">
                            </div>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:.3rem;">
                            <label for="description" style="font-size:.7rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;">Description</label>
                            <textarea name="description" id="description" placeholder="What value does this site provide?" rows="3" maxlength="500"
                                style="background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;padding:.6rem .75rem;color:#fff;font-size:.82rem;outline:none;width:100%;box-sizing:border-box;resize:vertical;">{{ old('description', session('crawl_result.description', '')) }}</textarea>
                        </div>

                        <button type="submit" style="width:100%;background:var(--color-gh-accent);color:#0d1117;padding:.75rem;border:none;border-radius:.4rem;font-weight:900;font-size:.82rem;text-transform:uppercase;letter-spacing:.1em;cursor:pointer;">
                            Publish Service Now
                        </button>
                    </form>
                </div>
            </div>

            {{-- Right sidebar --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div style="border:1px solid var(--color-gh-border);border-radius:.5rem;padding:1rem;">
                    <h3 style="font-size:.72rem;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.12em;margin:0 0 .75rem;">Guidelines</h3>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.6rem;">
                        @foreach([
                            'No illegal service that breaks global core ethics.',
                            'Instant publication. No delays or hidden queues.',
                            'Provide v3 onion addresses (56 characters) for better compatibility.',
                        ] as $rule)
                            <li style="display:flex;align-items:flex-start;gap:.5rem;">
                                <span style="color:var(--color-gh-accent);font-size:.75rem;margin-top:.1rem;flex-shrink:0;">•</span>
                                <span style="font-size:.75rem;color:var(--color-gh-dim);line-height:1.5;">{{ $rule }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div style="border:1px solid rgba(88,166,255,.2);background:rgba(88,166,255,.04);border-radius:.5rem;padding:1rem;">
                    <h4 style="font-size:.7rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.1em;margin:0 0 .4rem;">Need more reach?</h4>
                    <p style="font-size:.75rem;color:var(--color-gh-dim);line-height:1.5;margin:0 0 .75rem;">Promote your service on the top of the directory and search results.</p>
                    <a href="{{ route('advertise.create') }}" style="display:inline-block;background:rgba(88,166,255,.1);border:1px solid rgba(88,166,255,.3);color:var(--color-gh-accent);padding:.4rem .85rem;border-radius:.35rem;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;text-decoration:none;">
                        Buy Sponsored Slot
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-app.layouts>