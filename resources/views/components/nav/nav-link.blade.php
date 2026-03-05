@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-emerald-600 dark:border-emerald-400 text-sm font-medium leading-5 text-emerald-600 dark:text-emerald-400 focus:outline-none focus:border-emerald-700 dark:focus:border-emerald-300 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-neutral-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-neutral-300 dark:hover:border-zinc-600 focus:outline-none focus:text-emerald-600 dark:focus:text-emerald-400 focus:border-neutral-300 dark:focus:border-zinc-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
