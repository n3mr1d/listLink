<x-app.layouts title="Admin - Email Harvesting Operations">

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Email Harvesting</h1>
            <p class="text-gh-dim text-sm italic">Automated collection and validation of public-facing communication nodes.</p>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), false],
            ['Directory Inventory', route('admin.links'), false],
            ['Ad Queue', route('admin.ads'), false],
            ['Uptime History', route('admin.uptime-logs'), false],
            ['Access Control', route('admin.blacklist'), false],
            ['Crawler Engine', route('admin.crawler.index'), false],
            ['Email Harvesting', route('admin.email-crawler.index'), true]
        ] as $item)
            <a href="{{ $item[1] }}" class="px-4 py-2.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(88,166,255,0.3)]' : 'text-gh-dim bg-white/5 border border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-8 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-500 text-xs font-bold uppercase tracking-widest flex items-center gap-3">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-9 gap-4 mb-12">
        @foreach([
            ['Verified', $stats['total'], 'gh-accent'],
            ['Active', $stats['active'], 'green-400'],
            ['Invalid', $stats['invalid'], 'red-500'],
            ['Domains', $stats['domains'], 'purple-400'],
            ['New', $stats['not_exported'], 'orange-400'],
            ['Exported', $stats['exported'], 'blue-400'],
            ['Automated', $stats['auto_crawl'], 'cyan-400'],
            ['Manual', $stats['manual'], 'purple-500'],
            ['Today', $stats['today'], 'yellow-500']
        ] as $stat)
            <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-4 text-center group transition-all">
                <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-2 group-hover:text-{{ $stat[2] }}">{{ $stat[0] }}</div>
                <div class="text-2xl font-black text-white">{{ number_format($stat[1]) }}</div>
            </div>
        @endforeach
    </div>

    {{-- Operation Panels --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        {{-- Scan Single --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm flex flex-col">
            <div class="px-6 py-4 border-b border-gh-border bg-white/5">
                <h3 class="text-[0.65rem] font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-search-plus text-gh-accent"></i> Targeted Scan
                </h3>
            </div>
            <div class="p-6 flex-grow">
                <form method="POST" action="{{ route('admin.email-crawler.scan-url') }}" class="flex flex-col h-full">
                    @csrf
                    <input type="url" name="url" placeholder="https://target-node.onion/contact" required class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-sm text-white focus:ring-1 focus:ring-gh-accent outline-none mb-4 placeholder:text-gh-dim/30">
                    <label class="flex items-center gap-3 mb-6 cursor-pointer group">
                        <input type="checkbox" name="use_proxy" value="1" class="accent-gh-accent">
                        <span class="text-[0.65rem] font-bold text-gh-dim uppercase tracking-widest group-hover:text-white transition-colors">Route through Tor</span>
                    </label>
                    <button type="submit" class="mt-auto w-full bg-gh-accent text-gh-bg py-3 rounded-xl font-black text-[0.65rem] uppercase tracking-widest hover:bg-blue-400 transition-all">Initiate Scan</button>
                </form>
            </div>
        </div>

        {{-- Bulk Scan --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm flex flex-col">
            <div class="px-6 py-4 border-b border-gh-border bg-white/5">
                <h3 class="text-[0.65rem] font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-layer-group text-purple-400"></i> Batch Process
                </h3>
            </div>
            <div class="p-6 flex-grow">
                <form method="POST" action="{{ route('admin.email-crawler.scan-bulk') }}" class="flex flex-col h-full">
                    @csrf
                    <textarea name="urls" placeholder="Enter multiple nodes..." class="w-full h-24 bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-sm text-white focus:ring-1 focus:ring-purple-500 outline-none mb-4 resize-none placeholder:text-gh-dim/30 font-mono"></textarea>
                    <label class="flex items-center gap-3 mb-6 cursor-pointer group">
                        <input type="checkbox" name="use_proxy" value="1" class="accent-purple-500">
                        <span class="text-[0.65rem] font-bold text-gh-dim uppercase tracking-widest group-hover:text-white transition-colors">Force Proxy Hop</span>
                    </label>
                    <button type="submit" class="mt-auto w-full bg-purple-500/10 border border-purple-500/30 text-purple-400 py-3 rounded-xl font-black text-[0.65rem] uppercase tracking-widest hover:bg-purple-500 hover:text-white transition-all">Queue Batch</button>
                </form>
            </div>
        </div>

        {{-- Manual Ingestion --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm flex flex-col">
            <div class="px-6 py-4 border-b border-gh-border bg-white/5">
                <h3 class="text-[0.65rem] font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-plus-circle text-green-500"></i> Direct Ingestion
                </h3>
            </div>
            <div class="p-6 flex-grow">
                <form method="POST" action="{{ route('admin.email-crawler.manual-add') }}" class="flex flex-col h-full">
                    @csrf
                    <input type="email" name="email" placeholder="alias@domain.onion" required class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-sm text-white focus:ring-1 focus:ring-green-500 outline-none mb-3 placeholder:text-gh-dim/30">
                    <input type="url" name="source_url" placeholder="Source Reference (optional)" class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-sm text-white focus:ring-1 focus:ring-green-500 outline-none mb-6 placeholder:text-gh-dim/30">
                    <button type="submit" class="mt-auto w-full bg-green-500/10 border border-green-500/30 text-green-500 py-3 rounded-xl font-black text-[0.65rem] uppercase tracking-widest hover:bg-green-500 hover:text-white transition-all">Commit Record</button>
                </form>
            </div>
        </div>

        {{-- Tactical Export --}}
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm flex flex-col">
            <div class="px-6 py-4 border-b border-gh-border bg-white/5">
                <h3 class="text-[0.65rem] font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-file-export text-orange-400"></i> Intelligence Export
                </h3>
            </div>
            <div class="p-6 flex-grow flex flex-col justify-between gap-4">
                <div class="space-y-2">
                    <a href="{{ route('admin.email-crawler.export', ['status'=>'active','exported'=>'no','mark_exported'=>1]) }}" class="w-full block text-center bg-orange-500 text-gh-bg py-3 rounded-xl font-black text-[0.65rem] uppercase tracking-widest hover:brightness-110 transition-all no-underline shadow-lg shadow-orange-500/10">Extract New Nodes</a>
                    <a href="{{ route('admin.email-crawler.export', ['status'=>'all']) }}" class="w-full block text-center bg-white/5 border border-white/10 text-gh-dim py-3 rounded-xl font-black text-[0.65rem] uppercase tracking-widest hover:text-white transition-all no-underline">Dump Full Database</a>
                </div>
                <div class="pt-4 border-t border-white/5">
                    <form method="POST" action="{{ route('admin.email-crawler.bulk-delete') }}">
                        @csrf
                        <div class="flex gap-2">
                            <select name="status" class="flex-grow bg-gh-bg border border-white/10 rounded-lg px-2 text-[0.6rem] text-gh-dim uppercase font-black outline-none appearance-none cursor-pointer">
                                <option value="">All State</option>
                                <option value="invalid">Invalid</option>
                            </select>
                            <button type="submit" class="bg-red-500/10 border border-red-500/30 text-red-500 p-2 rounded-lg hover:bg-red-500 hover:text-white transition-all" onclick="return confirm('Purge tactical data?')">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DB Mining --}}
    <div class="bg-gradient-to-br from-orange-500/5 to-transparent border border-orange-500/20 rounded-2xl p-8 mb-12 shadow-sm relative overflow-hidden group">
        <i class="fas fa-database absolute -right-10 -bottom-10 text-[12rem] text-white/[0.02] -rotate-12 transition-transform group-hover:scale-110"></i>
        <div class="flex flex-col lg:flex-row lg:items-center gap-10">
            <div class="flex-grow">
                <h3 class="text-xl font-black text-white uppercase tracking-tight mb-2">Internal Data Mining</h3>
                <p class="text-gh-dim text-sm leading-relaxed max-w-[600px]">Systematically traverse your current link database to extract communication identifiers. This operation runs asynchronously via the background worker.</p>
            </div>
            <form method="POST" action="{{ route('admin.email-crawler.crawl-from-db') }}" class="flex flex-wrap items-end gap-4 min-w-[50%]">
                @csrf
                <div class="flex-grow min-w-[200px] flex flex-col gap-2">
                    <label class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest ml-1">Extraction Scope</label>
                    <select name="source" class="w-full bg-gh-bg border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:ring-1 focus:ring-orange-500 transition-all cursor-pointer appearance-none">
                        <option value="both">Global (Links + Discovery)</option>
                        <option value="links">Verified Directory only</option>
                        <option value="discovered">Scraped Inventory only</option>
                    </select>
                </div>
                <div class="w-24 flex flex-col gap-2">
                    <label class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest ml-1">Limit</label>
                    <input type="number" name="limit" value="500" class="w-full bg-gh-bg border border-white/10 rounded-xl px-4 py-3 text-xs text-white outline-none focus:ring-1 focus:ring-orange-500 transition-all font-mono">
                </div>
                <div class="flex flex-col gap-2 h-full justify-end">
                    <button type="submit" class="bg-orange-500 text-gh-bg px-8 py-3.5 rounded-xl font-black text-[0.65rem] uppercase tracking-widest hover:brightness-110 transition-all shadow-lg shadow-orange-500/10">Mine Database</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter & Record Stream --}}
    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-gh-border bg-white/5 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <form method="GET" action="{{ route('admin.email-crawler.index') }}" class="flex-grow flex flex-wrap items-center gap-4">
                <div class="relative flex-grow min-w-[300px]">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gh-dim/50"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search intelligence stream..." class="w-full bg-gh-bg border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/30">
                </div>
                <select name="status" onchange="this.form.submit()" class="bg-gh-bg border border-white/10 rounded-xl px-4 py-2 text-[0.65rem] font-black uppercase text-gh-dim outline-none cursor-pointer">
                    @foreach(['all' => 'Filter: All State', 'active' => 'Active Only', 'invalid' => 'Invalid Only'] as $k => $v)
                        <option value="{{ $k }}" {{ $status === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
            <div class="flex items-center gap-4 border-l border-white/5 pl-6 hidden lg:flex">
                <div class="text-right">
                    <div class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest mb-1">Stream Content</div>
                    <div class="text-sm font-black text-white">{{ number_format($emails->total()) }} Records</div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white/5 border-b border-white/5">
                    <tr class="text-[0.6rem] font-black text-gh-dim uppercase tracking-widest">
                        <th class="px-6 py-4">Node Profile</th>
                        <th class="px-6 py-4">Source Analytics</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($emails as $email)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1.5">
                                    <div class="text-sm font-black text-white tracking-tight group-hover:text-gh-accent transition-colors select-all">{{ $email->email }}</div>
                                    <div class="flex items-center gap-2">
                                        @if($email->exported)<span class="bg-green-500/10 text-green-500 text-[0.55rem] font-black uppercase tracking-widest px-1.5 py-0.5 rounded border border-green-500/10">Archived</span>@endif
                                        <span class="text-[0.65rem] text-gh-dim truncate max-w-[200px]">{{ $email->page_title ?: 'Untitled Endpoint' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-[0.7rem]">
                                <div class="flex flex-col gap-1">
                                    @if($email->source_domain)
                                        <a href="{{ route('admin.email-crawler.index', ['domain'=>$email->source_domain]) }}" class="text-gh-accent font-bold no-underline hover:underline">{{ $email->source_domain }}</a>
                                    @endif
                                    <span class="text-gh-dim/60 font-mono text-[0.6rem] uppercase tracking-tighter">{{ $email->source_type }} INGESTION</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <form method="POST" action="{{ route('admin.email-crawler.update-status', $email->id) }}" class="flex justify-center">
                                    @csrf
                                    @php
                                        $stMap = [
                                            'active' => 'bg-green-500/10 text-green-500 border-green-500/10',
                                            'invalid' => 'bg-red-500/10 text-red-500 border-red-500/10',
                                            'unsubscribed' => 'bg-orange-500/10 text-orange-500 border-orange-500/10'
                                        ];
                                        $cls = $stMap[$email->status] ?? 'bg-white/5 text-gh-dim border-white/5';
                                    @endphp
                                    <select name="status" onchange="this.form.submit()" class="bg-transparent border-none p-0 text-[0.6rem] font-black uppercase text-center cursor-pointer focus:ring-0 appearance-none {{ $cls }} px-2 py-0.5 rounded-lg border">
                                        <option value="active" {{ $email->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="invalid" {{ $email->status === 'invalid' ? 'selected' : '' }}>Invalid</option>
                                        <option value="unsubscribed" {{ $email->status === 'unsubscribed' ? 'selected' : '' }}>Unsub</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-end">
                                    <form method="POST" action="{{ route('admin.email-crawler.delete', $email->id) }}">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500/5 text-gh-dim hover:text-red-500 border border-white/5 hover:border-red-500/20 transition-all shadow-sm" onclick="return confirm('Purge node record?')">
                                            <i class="fas fa-trash-alt text-[0.7rem]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <i class="fas fa-satellite-dish text-4xl text-white/5"></i>
                                    <p class="text-gh-dim text-sm italic">Monitoring silent. No communication nodes detected under current parameters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($emails->hasPages())
            <div class="px-6 py-4 border-t border-white/5 bg-white/[0.01]">
                {{ $emails->links('pagination.simple') }}
            </div>
        @endif
    </div>

    <div class="mt-12 mb-24 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-8 shadow-sm">
            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-shield-alt text-gh-accent"></i> Operational Guidelines
            </h4>
            <ul class="flex flex-col gap-4 list-none p-0 m-0">
                <li class="flex items-start gap-3">
                    <span class="text-gh-accent mt-0.5">/</span>
                    <span class="text-[0.75rem] text-gh-dim leading-relaxed">Only extract publicly visible nodes. Never bypass authentication protocols.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-gh-accent mt-0.5">/</span>
                    <span class="text-[0.75rem] text-gh-dim leading-relaxed">Systematic deduplication is applied at the point of ingestion.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-gh-accent mt-0.5">/</span>
                    <span class="text-[0.75rem] text-gh-dim leading-relaxed">Tor proxy escalation is required for all .onion end-points.</span>
                </li>
            </ul>
        </div>
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-8 shadow-sm">
            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fas fa-terminal text-gh-accent"></i> Backend Controls
            </h4>
            <div class="space-y-4">
                <div class="bg-black/40 rounded-xl p-4 border border-white/5 group">
                    <div class="text-[0.55rem] font-black text-gh-dim uppercase tracking-widest mb-2 group-hover:text-gh-accent transition-colors">Queue Worker Command</div>
                    <code class="text-xs text-gh-accent select-all">php artisan queue:work --queue=email-crawler</code>
                </div>
                <p class="text-[0.65rem] text-gh-dim italic px-1 leading-relaxed">Recommended to run workers under Supervisor for 24/7 autonomous collection.</p>
            </div>
        </div>
    </div>

</x-app.layouts>
