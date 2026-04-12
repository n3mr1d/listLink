<x-app.layouts title="Live Global Search">

    @include('admin._nav')

    <div class="admin-header">
        <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.3rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <span style="font-size:.6rem;font-weight:800;color:var(--color-gh-accent);text-transform:uppercase;letter-spacing:.12em;">Master Console</span>
        </div>
        <h1>Unified Signal Search</h1>
        <p>Live intercept of directory nodes and crawling sequences.</p>
    </div>

    {{-- Live Search Input --}}
    <div style="margin-bottom:2rem;">
        <div style="display:flex;align-items:center;background:var(--color-gh-btn-bg);border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;padding:0 1rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-dim)" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="master-search-input" placeholder="Enter link ID, title, URL, or error pattern..." 
                style="flex:1;background:transparent;border:none;color:#fff;padding:1rem;outline:none;font-size:.95rem;font-weight:500;" autocomplete="off">
            <div id="search-spinner" style="display:none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-gh-accent)" stroke-width="3" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            </div>
        </div>
        <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
    </div>

    {{-- Tabs --}}
    <div class="filter-bar" id="search-tabs">
        <a href="#" class="active" data-type="links">Nodes Registry</a>
        <a href="#" data-type="crawls">Crawl Intelligance</a>
    </div>

    {{-- Results Panel --}}
    <div class="panel">
        <div class="panel-head">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Parsed Results: <span id="results-count" style="margin-left:.3rem;color:var(--color-gh-accent);">0</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table" id="results-table">
                <thead>
                    <tr id="table-head">
                        {{-- Dynamic Head --}}
                    </tr>
                </thead>
                <tbody id="results-body">
                    <tr>
                        <td colspan="100%">
                            <div class="empty-state">
                                <p>Begin typing to intercept signals...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const input = document.getElementById('master-search-input');
        const spinner = document.getElementById('search-spinner');
        const countSpan = document.getElementById('results-count');
        const tableBody = document.getElementById('results-body');
        const tableHead = document.getElementById('table-head');
        const tabs = document.querySelectorAll('#search-tabs a');

        let currentType = 'links';
        let debounceTimer;

        // Tab Switching
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                currentType = tab.dataset.type;
                performSearch();
            });
        });

        // Live Search Input
        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(performSearch, 300);
        });

        async function performSearch() {
            const q = input.value.trim();
            if (q.length < 2) {
                countSpan.innerText = '0';
                tableBody.innerHTML = '<tr><td colspan="100%"><div class="empty-state"><p>Query too short (min 2 chars)</p></div></td></tr>';
                renderHeader();
                return;
            }

            spinner.style.display = 'block';
            renderHeader();

            try {
                const response = await fetch(`{{ route('admin.search.live') }}?q=${encodeURIComponent(q)}&type=${currentType}`);
                const data = await response.json();

                countSpan.innerText = data.count;
                renderResults(data.results);
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                spinner.style.display = 'none';
            }
        }

        function renderHeader() {
            if (currentType === 'links') {
                tableHead.innerHTML = `
                    <th>Identity</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Crawl</th>
                    <th style="text-align:right">Ops</th>
                `;
            } else {
                tableHead.innerHTML = `
                    <th>Target Node</th>
                    <th>Status</th>
                    <th>Response</th>
                    <th>Intercepted</th>
                    <th>Diagnostics</th>
                `;
            }
        }

        function renderResults(results) {
            if (results.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="100%"><div class="empty-state"><p>Zero signals detected for this query.</p></div></td></tr>';
                return;
            }

            let html = '';
            results.forEach(item => {
                if (currentType === 'links') {
                    html += `
                        <tr>
                            <td>
                                <div style="font-size:.8rem;font-weight:700;color:#fff;">${item.title}</div>
                                <div style="font-size:.6rem;color:var(--color-gh-dim);font-family:monospace;opacity:.7;">${item.url}</div>
                            </td>
                            <td><span class="status-badge ${item.status.toLowerCase().includes('online') ? 'sb-online' : 'sb-offline'}">${item.status}</span></td>
                            <td style="font-size:.65rem;color:var(--color-gh-dim);">${item.last_checked}</td>
                            <td><span class="status-badge sb-${item.crawl_status}">${item.crawl_status}</span></td>
                            <td style="text-align:right;">
                                <div style="display:flex;justify-content:flex-end;gap:.35rem;">
                                    <a href="${item.edit_url}" class="btn-sm" style="color:var(--color-gh-accent);"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                                    <form action="${item.delete_url}" method="POST" onsubmit="return confirm('Terminate this node from registry?')">
                                        @csrf
                                        <button type="submit" class="btn-sm" style="color:var(--color-accent-red);"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    html += `
                        <tr>
                            <td><div style="font-size:.65rem;color:var(--color-gh-dim);font-family:monospace;">${item.url}</div></td>
                            <td><span class="status-badge sb-${item.status}">${item.status}</span></td>
                            <td style="font-size:.7rem;font-weight:700;color:#fff;">${item.response_time}</td>
                            <td style="font-size:.65rem;color:var(--color-gh-dim);">${item.checked_at}</td>
                            <td style="font-size:.65rem;color:var(--color-accent-red);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${item.error || ''}">${item.error || '<span style="color:var(--color-accent-green)">Success Receipt</span>'}</td>
                        </tr>
                    `;
                }
            });
            tableBody.innerHTML = html;
        }

        // Init header
        renderHeader();
    </script>

</x-app.layouts>
