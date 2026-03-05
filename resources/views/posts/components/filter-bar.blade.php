@props(['categories', 'baseUrl', 'filterBarData', 'listSelector' => '[data-posts-list]'])

<div class="flex items-center gap-2 mb-6">
    <x-posts::filter-dropdown :categories="$categories" :baseUrl="$baseUrl" :filterBarData="$filterBarData" />
    <x-posts::sort-dropdown :baseUrl="$baseUrl" :filterBarData="$filterBarData" />
    <x-posts::search-input :baseUrl="$baseUrl" :filterBarData="$filterBarData" :listSelector="$listSelector" />
</div>
