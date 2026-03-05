@props(['disabled' => false])

<input type="date" @disabled($disabled)
    {{ $attributes->merge(['class' => 'border border-neutral-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-emerald-500 dark:focus:ring-emerald-400 rounded-md shadow-sm text-sm py-2']) }}>
