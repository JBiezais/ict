<x-app-layout>
    <x-posts.pages.manage::header />

    <x-posts::filter-bar :categories="$categories" :baseUrl="$baseUrl" :filterBarData="$filterBarData" />

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
            {{ session('status') }}
        </div>
    @endif

    <div data-posts-list>
        <x-posts::manage-list :posts="$posts" />
    </div>
</x-app-layout>
