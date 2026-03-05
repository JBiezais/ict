<x-app-layout>
    <article
        class="rounded-lg border border-neutral-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 p-8 shadow-sm">
        <h2 class="text-2xl font-bold text-neutral-900 dark:text-zinc-100">{{ $post->title }}</h2>
        <div class="mt-2 flex items-center gap-4 text-sm text-neutral-500 dark:text-zinc-400">
            <span>{{ $post->user->name }}</span>
            <span>{{ $post->created_at->format('M j, Y') }}</span>
        </div>
        <div
            class="content-block mt-6 prose prose-neutral dark:prose-invert max-w-none indent-0 text-neutral-700 dark:text-zinc-300 whitespace-pre-line">
            {{ Str::trim($post->content) }}</div>
    </article>

    <section id="comments" class="mt-12 pt-8 border-t border-neutral-200 dark:border-zinc-700"
        aria-labelledby="comments-heading">
        <h2 id="comments-heading" class="text-lg font-semibold text-neutral-900 dark:text-zinc-100 mb-6">
            {{ __('Comments') }} ({{ $post->comments_count }})
        </h2>

        @auth
            <x-comments::comment-form :post="$post" />
        @else
            <p class="text-sm text-neutral-500 dark:text-zinc-400 mb-6">
                <a href="{{ route('login') }}"
                    class="text-emerald-600 dark:text-emerald-400 hover:underline">{{ __('Log in') }}</a>
                {{ __('to add a comment.') }}
            </p>
        @endauth

        @if ($post->comments_count === 0)
            <p class="text-neutral-500 dark:text-zinc-400 text-sm">{{ __('No comments yet.') }}</p>
        @else
            <div class="space-y-6">
                @foreach ($post->comments as $comment)
                    <x-comments::comment :comment="$comment" :post="$post" :max-depth="$maxCommentNestingDepth" />
                @endforeach
            </div>
        @endif
    </section>

    <div class="mt-6">
        <a href="{{ request('from') === 'my-posts' ? route('my-posts.posts.index') : route('home') }}"
            class="text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
            &larr; {{ __('Back to posts') }}
        </a>
    </div>
</x-app-layout>
