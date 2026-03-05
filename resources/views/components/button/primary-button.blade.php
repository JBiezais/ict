<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-emerald-600 dark:bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-emerald-400 focus:bg-emerald-700 dark:focus:bg-emerald-400 active:bg-emerald-800 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 dark:focus:ring-emerald-400 transition-colors duration-200 ease-in-out']) }}>
    {{ $slot }}
</button>
