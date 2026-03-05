<x-app-layout>
    <x-posts::filter-bar :categories="$categories" :baseUrl="route('home')" :currentFilters="$currentFilters ?? []" />

    <div data-posts-list>
        @include('posts.partials.browse-list', ['posts' => $posts])
    </div>
</x-app-layout>
