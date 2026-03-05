<x-app-layout>
    <x-posts.pages.manage::header />

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($posts->isEmpty())
        <p class="text-neutral-600 dark:text-zinc-400">{{ __('You have not created any posts yet.') }}</p>
    @else
        <div
            class="rounded-lg border border-neutral-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 overflow-visible shadow-sm divide-y divide-neutral-200 dark:divide-zinc-700">
            @foreach ($posts as $post)
                <x-posts.pages.manage::post-item :post="$post" />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    @endif
</x-app-layout>
