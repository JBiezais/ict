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
