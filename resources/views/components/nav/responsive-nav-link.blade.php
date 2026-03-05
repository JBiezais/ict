@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-emerald-600 dark:border-emerald-400 text-start text-base font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-900/20 focus:outline-none focus:text-emerald-800 dark:focus:text-emerald-300 focus:bg-emerald-100 dark:focus:bg-emerald-900/30 focus:border-emerald-700 dark:focus:border-emerald-300 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-neutral-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-neutral-50 dark:hover:bg-zinc-800 hover:border-neutral-300 dark:hover:border-zinc-600 focus:outline-none focus:text-emerald-600 dark:focus:text-emerald-400 focus:bg-neutral-50 dark:focus:bg-zinc-800 focus:border-neutral-300 dark:focus:border-zinc-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
