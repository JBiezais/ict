<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-zinc-800 border border-neutral-300 dark:border-zinc-600 rounded-md font-semibold text-xs text-neutral-700 dark:text-zinc-300 uppercase tracking-widest shadow-sm hover:bg-neutral-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 disabled:opacity-25 transition-colors duration-200 ease-in-out']) }}>
    {{ $slot }}
</button>
