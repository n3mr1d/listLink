<x-app.layouts title="Edit Node Details">

    @include('admin._nav')

    <div class="admin-header">
        <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.3rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.12em;">Registry Modulation</span>
        </div>
        <h1>Modifying Linked Node</h1>
        <p>Updating core metadata and status for <code style="color:var(--color-gh-accent);">${{ $link->url }}</code></p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">
        
        {{-- Main Form --}}
        <div class="panel">
            <div class="panel-head">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                Identity Fields
            </div>
            <div style="padding:1.5rem;">
                <form action="{{ route('admin.links.update', $link->id) }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;">
                    @csrf
                    <div>
                        <label style="display:block;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem;">Node Title</label>
                        <input type="text" name="title" value="{{ old('title', $link->title) }}" required
                            style="width:100%;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;color:#fff;padding:.65rem;font-size:.85rem;outline:none;">
                    </div>

                    <div>
                        <label style="display:block;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem;">Destination URL</label>
                        <input type="text" name="url" value="{{ old('url', $link->url) }}" required
                            style="width:100%;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;color:#fff;padding:.65rem;font-size:.85rem;outline:none;font-family:monospace;">
                    </div>

                    <div>
                        <label style="display:block;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem;">Deep Description</label>
                        <textarea name="description" rows="5"
                            style="width:100%;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;color:#fff;padding:.65rem;font-size:.85rem;outline:none;line-height:1.5;">{{ old('description', $link->description) }}</textarea>
                    </div>

                    <div>
                        <label style="display:block;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem;">Search Clusters (Tags)</label>
                        <input type="text" name="tags" value="{{ old('tags', $link->tags) }}" placeholder="wiki, index, directory..."
                            style="width:100%;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;color:#fff;padding:.65rem;font-size:.85rem;outline:none;">
                        <p style="font-size:.6rem;color:var(--color-gh-dim);margin-top:.3rem;">Weighted keywords for intelligent search engine. Comma separated.</p>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label style="display:block;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem;">Classification</label>
                            <select name="category" 
                                style="width:100%;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;color:#fff;padding:.65rem;font-size:.85rem;outline:none;cursor:pointer;">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->value }}" {{ (old('category', $link->category->value) == $cat->value) ? 'selected' : '' }}>{{ $cat->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.5rem;">Network Presence</label>
                            <select name="uptime_status" 
                                style="width:100%;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.4rem;color:#fff;padding:.65rem;font-size:.85rem;outline:none;cursor:pointer;">
                                @foreach($uptimeStatuses as $status)
                                    <option value="{{ $status->value }}" {{ (old('uptime_status', $link->uptime_status->value) == $status->value) ? 'selected' : '' }}>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:.5rem;padding-top:1.25rem;border-top:1px solid var(--color-gh-border);display:flex;justify-content:space-between;align-items:center;">
                        <a href="{{ route('admin.links') }}" style="font-size:.72rem;font-weight:700;color:var(--color-gh-dim);text-decoration:none;">Discard Changes</a>
                        <button type="submit" 
                            style="background:var(--color-gh-accent);color:#0d1117;padding:.6rem 1.25rem;border:none;border-radius:.4rem;font-weight:900;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;cursor:pointer;">Update Identity</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            {{-- Crawler Panel --}}
            <div class="panel">
                <div class="panel-head">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    Crawler Cluster
                </div>
                <div style="padding:1.25rem;">
                    <div style="margin-bottom:1.25rem;">
                        <span style="display:block;font-size:.6rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;margin-bottom:.4rem;">Crawler Status</span>
                        <span class="status-badge sb-{{ $link->crawl_status }}">{{ $link->crawl_status }}</span>
                    </div>
                    
                    <form action="{{ route('admin.links.enrich', $link->id) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;background:rgba(88,166,255,.1);border:1px solid var(--color-gh-accent);color:var(--color-gh-accent);padding:.65rem;border-radius:.4rem;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;transition:all .15s;">
                            Trigger Deep Extraction
                        </button>
                    </form>
                    <p style="margin:.75rem 0 0;font-size:.62rem;color:var(--color-gh-dim);line-height:1.4;">Forces the crawler to bypass cache and scrape fresh metadata (title, description, icons) from the destination node.</p>
                </div>
            </div>

            {{-- Extraction Logs --}}
            <div class="panel">
                <div class="panel-head">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Recent Syncs
                </div>
                <div style="padding:.5rem 0;">
                    @foreach($link->crawlLogs()->latest()->limit(5)->get() as $log)
                        <div style="padding:.5rem 1rem;{{ !$loop->last ? 'border-bottom:1px solid rgba(48,54,61,0.3);' : '' }}">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span class="status-badge sb-{{ $log->status }}" style="font-size:.5rem;padding:.1rem .35rem;">{{ $log->status }}</span>
                                <span style="font-size:.55rem;color:var(--color-gh-dim);font-family:monospace;">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </div>
                    @endforeach
                    <a href="{{ route('admin.crawler.link-logs', $link->id) }}" style="display:block;padding:.75rem 1rem;text-align:center;font-size:.62rem;font-weight:800;color:var(--color-gh-accent);text-decoration:none;text-transform:uppercase;letter-spacing:.08em;">Full Signal Log</a>
                </div>
            </div>
        </div>

    </div>

</x-app.layouts>
