@props(['categories', 'baseUrl', 'filterBarData'])

<x-nav.dropdown align="left" width="min-w-[280px]" :closeOnContentClick="false" contentClasses="p-4 bg-white dark:bg-zinc-800">
    <x-slot name="trigger">
        <button type="button"
            class="relative inline-flex items-center justify-center w-9 h-9 rounded-md border border-neutral-300 dark:border-zinc-600 text-neutral-500 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 hover:border-emerald-500 dark:hover:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 dark:focus:ring-emerald-400 bg-white dark:bg-zinc-800 transition-colors"
            aria-label="{{ __('Filter posts') }}" :aria-expanded="open">
            {{-- Filter icon (adjustments-horizontal / funnel) --}}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            @if ($filterBarData->hasActiveFilters)
                <span
                    class="absolute top-0 right-0 w-2 h-2 rounded-full bg-emerald-500 translate-x-1/2 -translate-y-1/2"
                    aria-hidden="true"></span>
            @endif
        </button>
    </x-slot>
    <x-slot name="content">
        <form method="GET" action="{{ $baseUrl }}" id="filter-form">
            <input type="hidden" name="sort" value="{{ $filterBarData->sort }}">
            <input type="hidden" name="search" value="{{ $filterBarData->search }}">
            @if ($categories->isNotEmpty())
                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-zinc-400 mb-2">
                    {{ __('Categories') }}</p>
                <div class="space-y-2 mb-4">
                    @foreach ($categories as $category)
                        <label
                            class="flex items-center gap-2 cursor-pointer text-sm text-neutral-700 dark:text-zinc-300 hover:bg-neutral-50 dark:hover:bg-zinc-700/50 -mx-2 px-2 py-1.5 rounded">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                {{ in_array($category->id, $filterBarData->selectedCategoryIds) ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded border-neutral-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-400 dark:bg-zinc-800">
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                    <label
                        class="flex items-center gap-2 cursor-pointer text-sm text-neutral-700 dark:text-zinc-300 hover:bg-neutral-50 dark:hover:bg-zinc-700/50 -mx-2 px-2 py-1.5 rounded">
                        <input type="hidden" name="include_uncategorized" value="0">
                        <input type="checkbox" name="include_uncategorized" value="1"
                            {{ $filterBarData->includeUncategorized ? 'checked' : '' }} onchange="this.form.submit()"
                            class="rounded border-neutral-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-400 dark:bg-zinc-800">
                        <span>{{ __('Uncategorized') }}</span>
                    </label>
                </div>
            @endif
            <hr class="border-neutral-200 dark:border-zinc-600 my-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-zinc-400 mb-2">
                {{ __('Creation date') }}</p>
            <input type="hidden" name="date_from" value="{{ $filterBarData->dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $filterBarData->dateTo }}">
            <input type="text" id="filter-date-range" data-date-range readonly
                placeholder="{{ __('Select date range') }}" value="{{ $filterBarData->dateRangeValue }}"
                class="w-full border border-neutral-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-emerald-500 dark:focus:ring-emerald-400 rounded-md shadow-sm text-sm py-2 px-3 cursor-pointer bg-white dark:bg-zinc-800">
        </form>
    </x-slot>
</x-nav.dropdown>
