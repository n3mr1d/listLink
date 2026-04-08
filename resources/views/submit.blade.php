<x-app.layouts title="Submit link">
    <div class="max-w-[800px] mx-auto py-10">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2">Publish your Service</h1>
            <p class="text-gh-dim">Expand the dark web. Distribute your .onion link to the network.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">
            <div class="flex flex-col gap-6">
                {{-- Info Banner --}}
                <div class="bg-blue-500/5 border border-blue-500/10 rounded-2xl p-6 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="text-sm">
                        @auth
                            <p class="text-white font-bold mb-1">Authenticated Session</p>
                            <p class="text-gh-dim leading-relaxed m-0 text-xs">Logged in as <span class="text-gh-accent">{{ auth()->user()->username }}</span>. Your link will be featured in the <span class="text-white font-bold">Global Directory</span> and Search Engine.</p>
                        @else
                            <p class="text-orange-400 font-bold mb-1">Anonymous Mode</p>
                            <p class="text-gh-dim leading-relaxed m-0 text-xs">Your link will <span class="bg-orange-500/20 px-1 rounded">only</span> be indexed by the <span class="text-white font-bold">Search Engine</span>. <a href="{{ route('login.form') }}" class="text-gh-accent hover:underline">Log in</a> for directory listing.</p>
                        @endauth
                    </div>
                </div>

                {{-- Crawler Pre-fill Form --}}
                <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-white/5 px-6 py-4 border-b border-gh-border flex items-center justify-between">
                        <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-spider text-gh-accent"></i> Tor Assistant
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-xs text-gh-dim mb-4 leading-relaxed">Let our crawler fetch the title and metadata for you to save time.</p>
                        <form action="{{ route('submit.crawl') }}" method="POST" class="flex flex-col gap-3">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="crawl_url" value="{{ old('crawl_url', session('crawled_url', '')) }}" placeholder="http://v3-onion-address.onion" required
                                    class="flex-grow bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30">
                                <button type="submit" class="bg-gh-btn-bg text-white border border-gh-border px-6 rounded-xl font-bold text-sm tracking-tight hover:bg-gh-btn-hover transition-all shrink-0">
                                    Crawl
                                </button>
                            </div>
                            <p class="text-[0.6rem] text-gh-dim/60 italic px-1"><i class="fas fa-shield-alt mr-1"></i> Crawler uses multiple hops for anonymity. Max timeout 15s.</p>
                        </form>

                        @if (session('crawl_result'))
                        <div class="mt-6 bg-green-500/5 border border-green-500/20 rounded-xl p-4">
                            <div class="text-green-500 font-black text-[0.65rem] uppercase tracking-widest mb-3 flex items-center gap-2">
                                <i class="fas fa-check-circle"></i> Sync Successful
                            </div>
                            <div class="space-y-2">
                                @if (session('crawl_result.title'))
                                    <div class="text-xs"><span class="text-gh-dim font-bold uppercase tracking-tighter mr-2">Fetched Title:</span> <span class="text-white">{{ session('crawl_result.title') }}</span></div>
                                @endif
                                @if (session('crawl_result.description'))
                                    <div class="text-xs"><span class="text-gh-dim font-bold uppercase tracking-tighter mr-2">Fetched Info:</span> <span class="text-white">{{ Str::limit(session('crawl_result.description'), 100) }}</span></div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Submit Form --}}
                <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-lg">
                    <div class="p-8">
                        <form action="{{ route('submit.store') }}" method="POST" class="flex flex-col gap-6">
                            @csrf
                            <div class="hidden"><input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off"></div>

                            <div class="space-y-4">
                                <div class="flex flex-col gap-2">
                                    <label for="title" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Service Hub Name *</label>
                                    <input type="text" name="title" id="title" value="{{ old('title', session('crawl_result.title', '')) }}" placeholder="e.g., Hidden Wiki Clone" required minlength="3" maxlength="100"
                                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30">
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="url" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Onion Address *</label>
                                    <input type="text" name="url" id="url" value="{{ old('url', session('crawled_url', '')) }}" placeholder="http://...onion" required
                                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30 font-mono">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex flex-col gap-2">
                                        <label for="category" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Category *</label>
                                        <select name="category" id="category" required class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all appearance-none">
                                            <option value="">— Choose Node —</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->value }}" {{ old('category') === $category->value ? 'selected' : '' }}>
                                                    {{ $category->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="challenge" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Human Test: {{ $challenge }}</label>
                                        <input type="number" name="challenge" id="challenge" required placeholder="Result" class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30">
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="description" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Mission / Description</label>
                                    <textarea name="description" id="description" placeholder="What value does this site provide to the network?" rows="4" maxlength="500"
                                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/30 resize-none">{{ old('description', session('crawl_result.description', '')) }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-gh-accent text-gh-bg py-4 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-400 active:scale-95 transition-all shadow-xl shadow-blue-500/10 mt-2">
                                Publish Service Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="flex flex-col gap-6">
                <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-sm">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-4">Guidelines</h3>
                    <ul class="flex flex-col gap-4 p-0 m-0 list-none">
                        <li class="flex items-start gap-3">
                            <span class="text-gh-accent text-xs mt-0.5">•</span>
                            <span class="text-[0.7rem] text-gh-dim leading-relaxed">No illegal service that break global core ethics.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-gh-accent text-xs mt-0.5">•</span>
                            <span class="text-[0.7rem] text-gh-dim leading-relaxed">Instant publication. No delays or hidden queues.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-gh-accent text-xs mt-0.5">•</span>
                            <span class="text-[0.7rem] text-gh-dim leading-relaxed">Provide v3 onion addresses (56 characters) for better compatibility.</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gh-accent/5 border border-gh-accent/20 rounded-2xl p-6">
                    <h4 class="text-xs font-black text-gh-accent uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="fas fa-ad"></i> Need more reach?
                    </h4>
                    <p class="text-[0.7rem] text-gh-dim leading-relaxed mb-4">Promote your service on the top of the directory and search results.</p>
                    <a href="{{ route('advertise.create') }}" class="inline-block bg-gh-accent/10 border border-gh-accent/30 text-gh-accent px-4 py-2 rounded-lg text-[0.65rem] font-bold uppercase tracking-widest no-underline hover:bg-gh-accent hover:text-gh-bg transition-all">
                        Buy Sponsored Slot
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app.layouts>