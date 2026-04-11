<nav class="flex items-center gap-6 shrink-0">
    <a href="{{ route('home') }}"
        class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('home') ? 'text-gh-accent' : '' }}">Search</a>
    <a href="{{ route('directory') }}"
        class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('directory') ? 'text-gh-accent' : '' }}">Directory</a>
    <a href="{{ route('submit.create') }}"
        class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('submit.*') ? 'text-gh-accent' : '' }}">Submit</a>
    <a href="{{ route('advertise.create') }}"
        class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('advertise.*') ? 'text-gh-accent' : '' }}">Ads</a>
    <a href="{{ route('about') }}"
        class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('about') ? 'text-gh-accent' : '' }}">About</a>
    <a href="{{ route('support.index') }}"
        class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('support.*') ? 'text-gh-accent' : '' }}">Support</a>

    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
                class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('admin.*') ? 'text-gh-accent' : '' }}">Admin</a>
        @endif
        <a href="{{ route('dashboard') }}"
            class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent {{ request()->routeIs('dashboard') ? 'text-gh-accent' : '' }}">Dashboard</a>
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                class="bg-transparent border-none text-gh-dim cursor-pointer text-sm p-0 hover:text-accent-red">Logout</button>
        </form>
    @else
        <a href="{{ route('login.form') }}"
            class="text-gh-dim no-underline text-sm font-medium transition-colors hover:text-gh-accent">Login</a>
        <a href="{{ route('register.form') }}"
            class="bg-accent-green text-white !no-underline px-4 py-1.5 rounded-md text-sm font-medium transition-colors hover:bg-green-600">Join</a>
    @endauth
</nav>
