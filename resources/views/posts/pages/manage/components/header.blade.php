<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-neutral-900 dark:text-zinc-100">{{ __('My Posts') }}</h2>
    <a href="{{ route('my-posts.posts.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 dark:bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-colors duration-200">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ __('New Post') }}
    </a>
</div>
