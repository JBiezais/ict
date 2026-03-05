<button type="button" x-data="{
    dark: document.documentElement.classList.contains('dark'),
    init() {
        this.$watch('dark', (value) => {
            document.documentElement.classList.toggle('dark', value);
            localStorage.setItem('theme', value ? 'dark' : 'light');
        });
    },
    toggle() {
        this.dark = !this.dark;
    }
}" @click="toggle(); $event.target.blur()"
    class="relative inline-flex items-center justify-center w-9 h-9 rounded-md text-neutral-500 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 dark:focus:ring-emerald-400 transition-colors duration-200"
    aria-label="Toggle dark mode">
    <span class="relative inline-block w-5 h-5">
        {{-- Sun icon (show when dark, click to go light) --}}
        <svg x-show="dark" x-transition class="absolute inset-0 w-5 h-5" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        {{-- Moon icon (show when light, click to go dark) --}}
        <svg x-show="!dark" x-transition class="absolute inset-0 w-5 h-5" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </span>
</button>
