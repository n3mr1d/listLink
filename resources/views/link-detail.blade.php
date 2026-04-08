<x-app.layouts title="{{ $link->title }} - Tor .Onion Directory"
    description="Details for {{ $link->title }}: {{ Str::limit($link->description, 150) }} - Verified .onion link on Hidden Line.">

    <div class="max-w-[1000px] mx-auto py-8">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center gap-2 mb-8 text-[0.7rem] font-bold uppercase tracking-wider text-gh-dim">
            <a href="{{ route('home') }}" class="hover:text-gh-accent no-underline transition-colors">Home</a>
            <span class="opacity-30">/</span>
            <a href="{{ route('category.show', $link->category->value) }}" class="hover:text-gh-accent no-underline transition-colors">{{ $link->category->label() }}</a>
            <span class="opacity-30">/</span>
            <span class="text-white">{{ $link->title }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-[1fr_350px] gap-8">
            {{-- Main Content Column --}}
            <div class="flex flex-col gap-8">
                {{-- Link Detail Card --}}
                <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                        <div>
                            <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">{{ $link->title }}</h1>
                            <div class="flex items-center gap-2 text-sm text-gh-dim">
                                <span class="bg-white/5 px-2 py-0.5 rounded border border-white/5 flex items-center gap-1.5"><i class="fas fa-globe-americas opacity-60"></i> Global</span>
                                <span class="opacity-30">•</span>
                                <span class="text-gh-accent font-medium">{{ $link->category->label() }}</span>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-black uppercase tracking-widest {{ $link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 'bg-green-500/10 text-green-500 border border-green-500/20 shadow-[0_0_15px_rgba(34,197,94,0.1)]' : ($link->uptime_status === \App\Enum\UptimeStatus::OFFLINE ? 'bg-red-500/10 text-red-500 border border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.1)]' : 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 shadow-[0_0_15px_rgba(234,179,8,0.1)]') }}">
                                {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-black/40 border border-gh-border rounded-xl px-5 py-4 mb-8">
                        <div class="flex-grow font-mono text-[0.95rem] text-gh-text truncate select-all">{{ $link->url }}</div>
                        <button onclick="copyToClipboard('{{ $link->url }}')" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-gh-btn-bg border border-gh-border text-gh-dim hover:text-white hover:border-gh-dim transition-all active:scale-95" title="Copy Onion Address">
                            <i class="far fa-copy text-sm"></i>
                        </button>
                    </div>

                    @if($link->description)
                        <div class="mb-10">
                            <h3 class="text-xs font-bold text-gh-dim uppercase tracking-widest mb-3">Service Description</h3>
                            <p class="text-[1.05rem] text-gh-text leading-relaxed whitespace-pre-wrap">{{ $link->description }}</p>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-3 pt-8 border-t border-white/5">
                        <form action="{{ route('link.check', $link->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-gh-btn-bg text-white border border-gh-border px-5 py-2.5 rounded-xl font-bold text-sm tracking-tight hover:bg-gh-btn-hover transition-all flex items-center gap-2 group">
                                <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-500"></i> Check Status Now
                            </button>
                        </form>
                        <a href="{{ $link->url }}" target="_blank" rel="noreferrer noopener" class="bg-gh-accent text-gh-bg px-5 py-2.5 rounded-xl font-extrabold text-sm tracking-tight hover:bg-blue-400 transition-all flex items-center gap-2 no-underline">
                            <i class="fas fa-external-link-alt text-xs"></i> Visit Now
                        </a>
                        <span class="text-[0.65rem] text-gh-dim/60 italic ml-2">Verification via Tor Proxy (15s timeout)</span>
                    </div>
                </div>

                {{-- Comments Section --}}
                <div class="flex flex-col gap-6">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="text-xl font-black text-white uppercase tracking-tight">Community Reviews <span class="text-gh-dim ml-1">({{ $link->comments->count() }})</span></h2>
                    </div>

                    @forelse($link->comments as $comment)
                        <div class="bg-gh-bar-bg border border-gh-border rounded-2xl p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gh-accent text-xs font-bold">
                                        {{ strtoupper(substr($comment->username, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-bold text-white">{{ $comment->username }}</span>
                                </div>
                                <span class="text-xs text-gh-dim">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-[0.95rem] text-gh-text leading-relaxed">{{ $comment->content }}</div>
                        </div>
                    @empty
                        <div class="bg-gh-bar-bg/50 border border-dashed border-gh-border rounded-2xl p-10 text-center">
                            <p class="text-gh-dim text-sm italic">No comments yet. Be the first to share your experience with this service.</p>
                        </div>
                    @endforelse

                    {{-- Add Comment Form --}}
                    <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-lg mt-4">
                        <div class="bg-white/5 px-6 py-4 border-b border-gh-border">
                            <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-comment-dots text-gh-accent"></i> Post a Comment
                            </h3>
                        </div>
                        <div class="p-8">
                            <form action="{{ route('link.comment', $link->id) }}" method="POST" class="flex flex-col gap-6">
                                @csrf
                                {{-- Honeypot --}}
                                <div class="hidden">
                                    <label for="website_url_hp">Website</label>
                                    <input type="text" name="website_url_hp" id="website_url_hp" tabindex="-1" autocomplete="off">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex flex-col gap-2">
                                        <label for="username" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Your Name</label>
                                        @auth
                                            <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                                            <div class="bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm opacity-60 font-medium">{{ auth()->user()->username }}</div>
                                        @else
                                            <input type="text" name="username" id="username" placeholder="Anonymous" value="{{ old('username') }}" class="bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/40">
                                        @endauth
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label for="challenge" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Anti-Spam Verification: {{ $challenge }}</label>
                                        <input type="number" name="challenge" id="challenge" required placeholder="Result" class="bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/40">
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="content" class="text-xs font-bold text-gh-dim uppercase tracking-wider ml-1">Your Remark</label>
                                    <textarea name="content" id="content" placeholder="Share your verdict on this .onion service..." required rows="4" class="bg-gh-bg border border-gh-border rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-1 focus:ring-gh-accent transition-all placeholder:text-gh-dim/40 resize-none">{{ old('content') }}</textarea>
                                </div>

                                <div class="pt-2">
                                    <button type="submit" class="bg-gh-accent text-gh-bg px-8 py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-400 active:scale-95 transition-all shadow-lg shadow-blue-500/10">Publish Comment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="flex flex-col gap-8">
                {{-- Metadata Card --}}
                <div class="bg-gh-bar-bg border border-gh-border rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-white/5 px-6 py-4 border-b border-gh-border">
                        <h3 class="text-xs font-black text-white uppercase tracking-widest">Metadata</h3>
                    </div>
                    <div class="p-6 flex flex-col gap-6">
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest">Submitted By</span>
                            <span class="text-white font-bold flex items-center gap-2"><i class="fas fa-user-circle text-xs opacity-50"></i> {{ $link->user->username ?? 'Anonymous' }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest">Date Added</span>
                            <span class="text-white font-bold flex items-center gap-2"><i class="fas fa-calendar-alt text-xs opacity-50"></i> {{ $link->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest">Last Uptime Check</span>
                            <span class="text-white font-bold flex items-center gap-2"><i class="fas fa-clock text-xs opacity-50"></i> {{ $link->last_check ? $link->last_check->diffForHumans() : 'Pending first check' }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span class="text-[0.65rem] font-black text-gh-dim uppercase tracking-widest">Reliability Score</span>
                            <div class="flex items-center gap-3">
                                <div class="flex-grow h-1.5 bg-gh-bg rounded-full overflow-hidden">
                                    @php
                                        // Simple score calculation: online links checked multiple times
                                        $score = min(100, max(10, ($link->uptime_status === \App\Enum\UptimeStatus::ONLINE ? 60 : 20) + min(40, $link->check_count * 2)));
                                    @endphp
                                    <div class="h-full {{ $score > 70 ? 'bg-green-500' : ($score > 40 ? 'bg-yellow-500' : 'bg-red-500') }} rounded-full shadow-[0_0_8px_currentColor]" style="width: {{ $score }}%"></div>
                                </div>
                                <span class="text-xs font-black text-white">{{ $score }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Safety Notice --}}
                <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-6">
                    <h4 class="text-xs font-black text-red-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Security Warning</h4>
                    <p class="text-[0.8rem] text-gh-dim leading-relaxed mb-0">Never shared PII (Personally Identifiable Information) on hidden services. Ensure you are using the official Tor Browser with JavaScript disabled if possible.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Flash animation for the button could be added here
                alert('Onion address copied to clipboard!');
            });
        }
    </script>
</x-app.layouts>