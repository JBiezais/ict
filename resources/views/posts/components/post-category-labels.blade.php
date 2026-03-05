@props(['categories', 'showUncategorized' => true])

@php
    $items = $categories->isNotEmpty()
        ? $categories
        : ($showUncategorized
            ? [
                new class {
                    public $name = 'Uncategorized';
                },
            ]
            : []);
@endphp

@if (count($items) > 0)
    <div class="flex flex-wrap gap-1.5 mb-2">
        @foreach ($items as $cat)
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $cat->name === 'Uncategorized'
                    ? 'bg-neutral-100 dark:bg-zinc-700/60 text-neutral-500 dark:text-zinc-400'
                    : 'bg-emerald-50 dark:bg-zinc-700/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-zinc-600' }}">
                {{ $cat->name }}
            </span>
        @endforeach
    </div>
@endif
