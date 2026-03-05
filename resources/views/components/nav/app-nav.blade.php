<div class="flex items-center justify-between w-full">
    {{-- Left: Logo + Nav links --}}
    <div class="flex items-center gap-6">
        <a href="{{ route('home') }}" class="flex items-center shrink-0" aria-label="{{ config('app.name') }}">
            <x-ui.application-logo class="w-8 h-8" />
        </a>
        <nav class="flex items-center gap-4">
            <x-nav.nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-nav.nav-link>

            @auth
                <x-nav.nav-link :href="route('my-posts.posts.index')" :active="request()->routeIs('my-posts.*')">
                    {{ __('My posts') }}
                </x-nav.nav-link>
            @endauth
        </nav>
    </div>

    {{-- Right: Theme toggle + Log in/Register (guests) or User dropdown (auth) --}}
    <div class="flex items-center gap-4">
        <x-ui.theme-toggle />
        @guest
            <x-nav.nav-link :href="route('login')">
                {{ __('Log in') }}
            </x-nav.nav-link>
            @if (Route::has('register'))
                <x-nav.nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-nav.nav-link>
            @endif
        @endguest
        @auth
            <x-nav.dropdown align="right" width="48" contentClasses="py-1 bg-white dark:bg-zinc-800">
                <x-slot name="trigger">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-neutral-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 dark:focus:ring-offset-zinc-900">
                        {{ auth()->user()->name }}
                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-nav.dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Log out') }}
                        </x-nav.dropdown-link>
                    </form>
                </x-slot>
            </x-nav.dropdown>
        @endauth
    </div>
</div>
