@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 bg-gh-bar-bg border border-gh-border text-gh-dim rounded-lg text-xs font-black uppercase tracking-tighter cursor-not-allowed opacity-50">
                &laquo;
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 bg-gh-bar-bg border border-gh-border text-gh-dim hover:text-gh-accent hover:border-gh-accent rounded-lg text-xs font-black uppercase tracking-tighter transition-all">
                &laquo;
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-2 py-2 text-gh-dim text-xs font-black tracking-tighter">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-4 py-2 bg-gh-accent text-gh-bg border border-gh-accent rounded-lg text-xs font-black tracking-tighter shadow-[0_0_15px_rgba(56,139,253,0.3)]">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-4 py-2 bg-gh-bar-bg border border-gh-border text-gh-dim hover:text-gh-accent hover:border-gh-accent rounded-lg text-xs font-black tracking-tighter transition-all">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 bg-gh-bar-bg border border-gh-border text-gh-dim hover:text-gh-accent hover:border-gh-accent rounded-lg text-xs font-black uppercase tracking-tighter transition-all">
                &raquo;
            </a>
        @else
            <span class="px-4 py-2 bg-gh-bar-bg border border-gh-border text-gh-dim rounded-lg text-xs font-black uppercase tracking-tighter cursor-not-allowed opacity-50">
                &raquo;
            </span>
        @endif
    </nav>
@endif