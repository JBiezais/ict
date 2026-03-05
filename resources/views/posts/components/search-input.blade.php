@props(['baseUrl', 'filterBarData', 'listSelector' => '[data-posts-list]'])

<div x-data="liveSearch({ baseUrl: {{ Js::from($baseUrl) }}, listSelector: {{ Js::from($listSelector) }}, minLength: 2, debounceMs: 300 })">
    <form x-ref="searchForm" method="GET" action="{{ $baseUrl }}" class="flex-1 min-w-0 max-w-xl relative"
        @submit.prevent="handleSubmit($event)">
        @foreach ($filterBarData->selectedCategoryIds as $id)
            <input type="hidden" name="category_ids[]" value="{{ $id }}">
        @endforeach
        <input type="hidden" name="include_uncategorized"
            value="{{ $filterBarData->includeUncategorized ? '1' : '0' }}">
        <input type="hidden" name="date_from" value="{{ $filterBarData->dateFrom }}">
        <input type="hidden" name="date_to" value="{{ $filterBarData->dateTo }}">
        <input type="hidden" name="sort" value="{{ $filterBarData->sort }}">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 dark:text-zinc-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </span>
        <input type="text" name="search" value="{{ $filterBarData->search }}"
            placeholder="{{ __('Search posts…') }}" aria-label="{{ __('Search posts') }}"
            class="w-full border border-neutral-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-emerald-500 dark:focus:ring-emerald-400 rounded-md shadow-sm text-sm py-2 pl-9 pr-3 bg-white dark:bg-zinc-800"
            @input="handleInput()">
    </form>
</div>
