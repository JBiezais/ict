<x-app-layout>
    <x-posts::filter-bar :categories="$categories" :baseUrl="$baseUrl" :filterBarData="$filterBarData" />

    <div data-posts-list>
        <x-posts::browse-list :posts="$posts" />
    </div>
</x-app-layout>
