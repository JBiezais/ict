@props(['baseUrl', 'filterBarData'])

<x-nav.dropdown align="left" width="w-48" contentClasses="py-1 bg-white dark:bg-zinc-800">
    <x-slot name="trigger">
        <button type="button"
            class="relative inline-flex items-center justify-center w-9 h-9 rounded-md border border-neutral-300 dark:border-zinc-600 text-neutral-500 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 hover:border-emerald-500 dark:hover:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 dark:focus:ring-emerald-400 bg-white dark:bg-zinc-800 transition-colors"
            aria-label="{{ __('Sort posts') }}" :aria-expanded="open">
            {{-- Sort icon (arrows-up-down) --}}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
            </svg>
            @if ($filterBarData->hasActiveSort)
                <span
                    class="absolute top-0 right-0 w-2 h-2 rounded-full bg-emerald-500 translate-x-1/2 -translate-y-1/2"
                    aria-hidden="true"></span>
            @endif
        </button>
    </x-slot>
    <x-slot name="content">
        <form method="GET" action="{{ $baseUrl }}" id="sort-form">
            @foreach ($filterBarData->selectedCategoryIds as $id)
                <input type="hidden" name="category_ids[]" value="{{ $id }}">
            @endforeach
            <input type="hidden" name="include_uncategorized"
                value="{{ $filterBarData->includeUncategorized ? '1' : '0' }}">
            <input type="hidden" name="date_from" value="{{ $filterBarData->dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $filterBarData->dateTo }}">
            <input type="hidden" name="search" value="{{ $filterBarData->search }}">
            <button type="submit" name="sort" value="date"
                class="block w-full px-4 py-2 text-start text-sm {{ $filterBarData->sort === 'date' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                {{ __('Newest first') }}
            </button>
            <button type="submit" name="sort" value="date_asc"
                class="block w-full px-4 py-2 text-start text-sm {{ $filterBarData->sort === 'date_asc' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                {{ __('Oldest first') }}
            </button>
            <button type="submit" name="sort" value="comments"
                class="block w-full px-4 py-2 text-start text-sm {{ $filterBarData->sort === 'comments' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                {{ __('Most comments') }}
            </button>
            <button type="submit" name="sort" value="comments_asc"
                class="block w-full px-4 py-2 text-start text-sm {{ $filterBarData->sort === 'comments_asc' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                {{ __('Fewest comments') }}
            </button>
        </form>
    </x-slot>
</x-nav.dropdown>
