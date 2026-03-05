<x-app-layout>
    <x-posts::filter-bar :categories="$categories" :baseUrl="route('home')" :currentFilters="$currentFilters ?? []" />

    @if ($posts->isEmpty())
        <p class="text-neutral-600 dark:text-zinc-400 text-center py-16">{{ __('No posts yet.') }}</p>
    @else
        <div class="space-y-12">
            @foreach ($posts as $post)
                <article
                    class="group py-6 border-b border-neutral-200 dark:border-zinc-700 last:border-b-0 last:pb-0 first:pt-0">
                    <a href="{{ route('posts.show', ['post' => $post->id]) }}" class="block cursor-pointer">
                        <x-posts::post-category-labels :categories="$post->categories" />
                        <div class="flex items-baseline gap-2 text-xs text-neutral-500 dark:text-zinc-400 mb-2">
                            @if ($post->userName)
                                <span>{{ $post->userName }}</span>
                                <span aria-hidden="true">·</span>
                            @endif
                            <time datetime="{{ $post->createdAt->toIso8601String() }}">
                                {{ $post->createdAt->format('M j, Y') }}
                            </time>
                            <span aria-hidden="true">·</span>
                            <span>{{ $post->commentsCount }} {{ Str::plural('comment', $post->commentsCount) }}</span>
                        </div>
                        <h2
                            class="text-xl font-semibold leading-tight text-neutral-900 dark:text-zinc-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-200">
                            {{ $post->title }}
                        </h2>
                        <p class="mt-2 text-neutral-600 dark:text-zinc-400 text-[15px] leading-relaxed line-clamp-3">
                            {{ Str::limit(strip_tags($post->content), 200) }}
                        </p>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-12 pt-8 border-t border-neutral-200 dark:border-zinc-700">
            {{ $posts->links() }}
        </div>
    @endif
</x-app-layout>
