<x-app.layouts title="{{ $category->label() }}">

    <div class="page-header">
        <h1>{{ $category->label() }}</h1>
        <p>Browse all verified .onion links in the {{ $category->label() }} category.</p>
    </div>

    @if($links->count() > 0)
        <table class="links-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="hide-mobile">URL</th>
                    <th class="hide-mobile">Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($links as $link)
                    <tr>
                        <td class="link-title">
                            <a href="{{ route('link.show', $link->slug) }}">{{ $link->title }}</a>
                        </td>
                        <td class="link-url hide-mobile">{{ $link->url }}</td>
                        <td class="hide-mobile" style="font-size:0.8rem;color:var(--text-secondary);">
                            {{ Str::limit($link->description, 60) }}
                        </td>
                        <td>
                            <span class="uptime-badge {{ $link->uptime_status->cssClass() }}">
                                {{ $link->uptime_status->icon() }} {{ $link->uptime_status->label() }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $links->links('pagination.simple') }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted">
                No links in this category yet. <a href="{{ route('submit.create') }}">Submit one!</a>
            </div>
        </div>
    @endif

    {{-- Category Navigation --}}
    <div class="mt-3">
        <div class="card">
            <div class="card-header">All Categories</div>
            <div class="card-body">
                @foreach($categories as $cat)
                    <a href="{{ route('category.show', $cat->value) }}"
                        class="btn btn-sm {{ $cat->value === $category->value ? 'btn-primary' : 'btn-secondary' }}"
                        style="margin:0.15rem;">
                        {{ $cat->label() }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</x-app.layouts>