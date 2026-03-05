@props([
    'categories',
    'baseUrl',
    'currentFilters' => [],
    'listSelector' => '[data-posts-list]',
])

@php
    $categoryIds = collect($currentFilters['category_ids'] ?? [])->map(fn ($v) => (int) $v)->filter()->values()->all();
    $allCategoryIds = $categories->pluck('id')->all();
    $selectedCategoryIds = empty($categoryIds) ? $allCategoryIds : $categoryIds;
    $includeUncategorized = filter_var($currentFilters['include_uncategorized'] ?? '1', FILTER_VALIDATE_BOOLEAN);
    $dateFrom = $currentFilters['date_from'] ?? '';
    $dateTo = $currentFilters['date_to'] ?? '';
    $sort = $currentFilters['sort'] ?? 'date';
    $search = $currentFilters['search'] ?? '';
    $dateRangeValue = '';
    if ($dateFrom && $dateTo) {
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom);
        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo);
        $dateRangeValue = ($from && $to) ? $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y') : "{$dateFrom} - {$dateTo}";
    } elseif ($dateFrom) {
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom);
        $dateRangeValue = $from ? $from->format('d/m/Y') : $dateFrom;
    }
    $hasActiveFilters =
        count($selectedCategoryIds) < count($allCategoryIds)
        || ! $includeUncategorized
        || $dateFrom !== ''
        || $dateTo !== ''
        || $search !== '';
    $hasActiveSort = $sort !== 'date';
@endphp

<div class="flex items-center gap-2 mb-6">
    {{-- Filter dropdown --}}
    <x-nav.dropdown
        align="left"
        width="min-w-[280px]"
        :closeOnContentClick="false"
        contentClasses="p-4 bg-white dark:bg-zinc-800"
    >
        <x-slot name="trigger">
            <button
                type="button"
                class="relative inline-flex items-center justify-center w-9 h-9 rounded-md border border-neutral-300 dark:border-zinc-600 text-neutral-500 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 hover:border-emerald-500 dark:hover:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 dark:focus:ring-emerald-400 bg-white dark:bg-zinc-800 transition-colors"
                aria-label="{{ __('Filter posts') }}"
                :aria-expanded="open"
            >
                {{-- Filter icon (adjustments-horizontal / funnel) --}}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                @if ($hasActiveFilters)
                    <span class="absolute top-0 right-0 w-2 h-2 rounded-full bg-emerald-500 translate-x-1/2 -translate-y-1/2" aria-hidden="true"></span>
                @endif
            </button>
        </x-slot>
        <x-slot name="content">
            <form method="GET" action="{{ $baseUrl }}" id="filter-form">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="search" value="{{ $search }}">
                @if ($categories->isNotEmpty())
                    <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-zinc-400 mb-2">{{ __('Categories') }}</p>
                    <div class="space-y-2 mb-4">
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-neutral-700 dark:text-zinc-300 hover:bg-neutral-50 dark:hover:bg-zinc-700/50 -mx-2 px-2 py-1.5 rounded">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                    {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                    class="rounded border-neutral-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-400 dark:bg-zinc-800">
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-neutral-700 dark:text-zinc-300 hover:bg-neutral-50 dark:hover:bg-zinc-700/50 -mx-2 px-2 py-1.5 rounded">
                            <input type="hidden" name="include_uncategorized" value="0">
                            <input type="checkbox" name="include_uncategorized" value="1"
                                {{ $includeUncategorized ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded border-neutral-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500 dark:focus:ring-emerald-400 dark:bg-zinc-800">
                            <span>{{ __('Uncategorized') }}</span>
                        </label>
                    </div>
                @endif
                <hr class="border-neutral-200 dark:border-zinc-600 my-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-zinc-400 mb-2">{{ __('Creation date') }}</p>
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">
                <input type="text" id="filter-date-range" data-date-range readonly placeholder="{{ __('Select date range') }}"
                    value="{{ $dateRangeValue }}"
                    class="w-full border border-neutral-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-emerald-500 dark:focus:ring-emerald-400 rounded-md shadow-sm text-sm py-2 px-3 cursor-pointer bg-white dark:bg-zinc-800">
            </form>
        </x-slot>
    </x-nav.dropdown>

    {{-- Sort dropdown --}}
    <x-nav.dropdown
        align="left"
        width="w-48"
        contentClasses="py-1 bg-white dark:bg-zinc-800"
    >
        <x-slot name="trigger">
            <button
                type="button"
                class="relative inline-flex items-center justify-center w-9 h-9 rounded-md border border-neutral-300 dark:border-zinc-600 text-neutral-500 hover:text-emerald-600 dark:text-zinc-400 dark:hover:text-emerald-400 hover:border-emerald-500 dark:hover:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 dark:focus:ring-emerald-400 bg-white dark:bg-zinc-800 transition-colors"
                aria-label="{{ __('Sort posts') }}"
                :aria-expanded="open"
            >
                {{-- Sort icon (arrows-up-down) --}}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                </svg>
                @if ($hasActiveSort)
                    <span class="absolute top-0 right-0 w-2 h-2 rounded-full bg-emerald-500 translate-x-1/2 -translate-y-1/2" aria-hidden="true"></span>
                @endif
            </button>
        </x-slot>
        <x-slot name="content">
            <form method="GET" action="{{ $baseUrl }}" id="sort-form">
                @foreach ($selectedCategoryIds as $id)
                    <input type="hidden" name="category_ids[]" value="{{ $id }}">
                @endforeach
                <input type="hidden" name="include_uncategorized" value="{{ $includeUncategorized ? '1' : '0' }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">
                <input type="hidden" name="search" value="{{ $search }}">
                <button type="submit" name="sort" value="date"
                    class="block w-full px-4 py-2 text-start text-sm {{ $sort === 'date' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                    {{ __('Newest first') }}
                </button>
                <button type="submit" name="sort" value="date_asc"
                    class="block w-full px-4 py-2 text-start text-sm {{ $sort === 'date_asc' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                    {{ __('Oldest first') }}
                </button>
                <button type="submit" name="sort" value="comments"
                    class="block w-full px-4 py-2 text-start text-sm {{ $sort === 'comments' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                    {{ __('Most comments') }}
                </button>
                <button type="submit" name="sort" value="comments_asc"
                    class="block w-full px-4 py-2 text-start text-sm {{ $sort === 'comments_asc' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 font-medium' : 'text-neutral-700 dark:text-zinc-300 hover:bg-neutral-100 dark:hover:bg-zinc-700' }}">
                    {{ __('Fewest comments') }}
                </button>
            </form>
        </x-slot>
    </x-nav.dropdown>

    {{-- Search --}}
    <div
        x-data="liveSearch({ baseUrl: {{ Js::from($baseUrl) }}, listSelector: {{ Js::from($listSelector) }}, minLength: 2, debounceMs: 300 })"
    >
        <form x-ref="searchForm" method="GET" action="{{ $baseUrl }}" class="flex-1 min-w-0 max-w-xl relative" @submit.prevent="handleSubmit($event)">
            @foreach ($selectedCategoryIds as $id)
                <input type="hidden" name="category_ids[]" value="{{ $id }}">
            @endforeach
            <input type="hidden" name="include_uncategorized" value="{{ $includeUncategorized ? '1' : '0' }}">
            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $dateTo }}">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 dark:text-zinc-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Search posts…') }}"
                aria-label="{{ __('Search posts') }}"
                class="w-full border border-neutral-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-emerald-500 dark:focus:ring-emerald-400 rounded-md shadow-sm text-sm py-2 pl-9 pr-3 bg-white dark:bg-zinc-800"
                @input="handleInput()">
        </form>
    </div>
</div>
