<x-app.layouts title="Admin - {{ isset($ad) ? 'Refine' : 'Deploy' }} Ad Campaign">

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">{{ isset($ad) ? 'Refine Campaign' : 'Deploy New Strategic Ad' }}</h1>
            <p class="text-gh-dim text-sm italic">Define execution parameters and creative assets for priority network placement.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ads') }}" class="text-gh-dim text-xs font-bold hover:text-white flex items-center gap-2 no-underline">
                <i class="fas fa-arrow-left text-[0.6rem]"></i> Return to Queue
            </a>
        </div>
    </div>

    {{-- Admin Navigation --}}
    <nav class="flex items-center gap-2 overflow-x-auto pb-4 mb-10 border-b border-white/5 no-scrollbar">
        @foreach([
            ['Insights', route('admin.dashboard'), false],
            ['Directory Inventory', route('admin.links'), false],
            ['Ad Queue', route('admin.ads'), true],
            ['Uptime History', route('admin.uptime-logs'), false],
            ['Access Control', route('admin.blacklist'), false],
            ['Crawler Engine', route('admin.crawler.index'), false],
            ['Email Harvesting', route('admin.email-crawler.index'), false]
        ] as $item)
            <a href="{{ $item[1] }}" class="px-4 py-2.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ ($item[2] ?? false) ? 'bg-gh-accent text-gh-bg shadow-[0_0_15px_rgba(88,166,255,0.3)]' : 'text-gh-dim bg-white/5 border border-white/5 hover:text-white hover:border-gh-dim' }}">
                {{ $item[0] }}
            </a>
        @endforeach
    </nav>

    <div class="max-w-4xl mx-auto mb-24">
        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
            <div class="px-8 py-5 border-b border-gh-border bg-white/5 flex items-center justify-between">
                <h2 class="text-[0.65rem] font-black text-white uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-sliders-h text-gh-accent"></i> Campaign Configuration Index
                </h2>
                @if(isset($ad))
                    <span class="text-[0.6rem] bg-gh-accent/10 text-gh-accent px-2 py-0.5 rounded-lg border border-gh-accent/20 font-black uppercase">Modifying Record #{{ $ad->id }}</span>
                @endif
            </div>

            <div class="p-8 lg:p-12">
                <form action="{{ isset($ad) ? route('admin.ads.update', $ad->id) : route('admin.ads.store') }}"
                    method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div class="group">
                                <label for="ad-title" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1 group-focus-within:text-gh-accent transition-colors">Campaign Headline *</label>
                                <input type="text" name="title" id="ad-title" value="{{ old('title', $ad->title ?? '') }}" required
                                    class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-sm text-white focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/20"
                                    placeholder="e.g., Tactical Portal Upgrade">
                            </div>

                            <div class="group">
                                <label for="ad-url" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1 group-focus-within:text-gh-accent transition-colors">Destination Endpoint *</label>
                                <input type="text" name="url" id="ad-url" value="{{ old('url', $ad->url ?? '') }}" required
                                    class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-[0.8rem] text-white focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/20 font-mono"
                                    placeholder="https://node-id.onion">
                            </div>

                            <div class="group">
                                <label for="ad-description" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1 group-focus-within:text-gh-accent transition-colors">Brief Overview</label>
                                <textarea name="description" id="ad-description" rows="4"
                                    class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-sm text-white focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/20 resize-none leading-relaxed"
                                    placeholder="Explain the service value proposition...">{{ old('description', $ad->description ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label for="ad-type" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1">Tier *</label>
                                    <select name="ad_type" id="ad-type" required
                                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-[0.7rem] font-black uppercase text-white focus:ring-1 focus:ring-gh-accent outline-none cursor-pointer appearance-none">
                                        @foreach ($adTypes as $type)
                                            <option value="{{ $type->value }}" {{ old('ad_type', $ad->ad_type->value ?? '') === $type->value ? 'selected' : '' }}>
                                                {{ $type->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="group">
                                    <label for="ad-placement" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1">Zonal *</label>
                                    <select name="placement" id="ad-placement" required
                                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-[0.7rem] font-black uppercase text-white focus:ring-1 focus:ring-gh-accent outline-none cursor-pointer appearance-none">
                                        @foreach ($placements as $placement)
                                            <option value="{{ $placement->value }}" {{ old('placement', $ad->placement->value ?? '') === $placement->value ? 'selected' : '' }}>
                                                {{ $placement->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="group">
                                <label for="ad-status" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1">Operational State *</label>
                                <select name="status" id="ad-status" required
                                    class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-[0.7rem] font-black uppercase text-white focus:ring-1 focus:ring-gh-accent outline-none cursor-pointer appearance-none">
                                    @foreach(['pending', 'active', 'expired', 'rejected'] as $st)
                                        <option value="{{ $st }}" {{ old('status', $ad->status ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="group">
                                <label for="ad-contact" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1">Contact Reference</label>
                                <input type="text" name="contact_info" id="ad-contact"
                                    value="{{ old('contact_info', $ad->contact_info ?? '') }}"
                                    class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-xs text-white focus:ring-1 focus:ring-gh-accent outline-none transition-all placeholder:text-gh-dim/20 font-mono"
                                    placeholder="Identity Handle">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label for="starts_at" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1">Activation</label>
                                    <input type="datetime-local" name="starts_at" id="starts_at"
                                        value="{{ old('starts_at', isset($ad->starts_at) ? $ad->starts_at->format('Y-m-d\TH:i') : '') }}"
                                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-[0.65rem] text-white focus:ring-1 focus:ring-gh-accent outline-none transition-all uppercase">
                                </div>
                                <div class="group">
                                    <label for="expires_at" class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-3 ml-1">Termination</label>
                                    <input type="datetime-local" name="expires_at" id="expires_at"
                                        value="{{ old('expires_at', isset($ad->expires_at) ? $ad->expires_at->format('Y-m-d\TH:i') : '') }}"
                                        class="w-full bg-gh-bg border border-gh-border rounded-xl px-4 py-3.5 text-[0.65rem] text-white focus:ring-1 focus:ring-gh-accent outline-none transition-all uppercase">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Visual Asset --}}
                    <div class="pt-10 border-t border-white/5">
                        <label class="block text-[0.65rem] font-black text-gh-dim uppercase tracking-widest mb-6 ml-1">Graphical Creative Asset</label>
                        
                        <div class="flex flex-col md:flex-row items-start gap-8">
                            @if(isset($ad) && $ad->banner_path)
                                <div class="shrink-0">
                                    <div class="bg-gh-bg border border-gh-border p-2 rounded-2xl shadow-inner">
                                        <img src="{{ asset('storage/' . $ad->banner_path) }}" alt="Current Banner"
                                            class="w-32 h-auto rounded-xl object-cover grayscale hover:grayscale-0 transition-all duration-500">
                                    </div>
                                    <p class="text-[0.55rem] font-black text-gh-dim uppercase text-center mt-3 tracking-widest">Active Asset</p>
                                </div>
                            @endif

                            <div class="flex-grow w-full">
                                <label class="w-full flex flex-col items-center justify-center border-2 border-gh-border border-dashed rounded-2xl p-8 hover:bg-gh-accent/5 hover:border-gh-accent/30 transition-all cursor-pointer group">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-gh-dim group-hover:text-gh-accent mb-4 transition-colors"></i>
                                    <span class="text-[0.65rem] font-black text-gh-dim group-hover:text-white uppercase tracking-widest mb-2 transition-colors">Load Tactical Artifact</span>
                                    <span class="text-[0.6rem] text-gh-dim/40 font-bold">JPG, PNG or GIF (MAX 2MB)</span>
                                    <input type="file" name="banner" id="ad-banner" class="hidden">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 flex items-center gap-4">
                        <button type="submit" class="flex-grow bg-gh-accent text-gh-bg font-black py-4 rounded-xl text-xs uppercase tracking-widest hover:brightness-110 transition-all shadow-lg shadow-gh-accent/10">
                            {{ isset($ad) ? 'Synchronize Updates' : 'Authorize Deployment' }}
                        </button>
                        <a href="{{ route('admin.ads') }}" class="px-8 py-4 bg-white/5 border border-white/10 rounded-xl text-[0.65rem] font-black uppercase tracking-widest text-gh-dim hover:text-white hover:border-gh-dim no-underline transition-all">Abort</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app.layouts>