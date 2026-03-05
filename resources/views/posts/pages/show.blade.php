<x-app-layout>
    <article
        class="rounded-lg border border-neutral-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 p-8 shadow-sm">
        <h2 class="text-2xl font-bold text-neutral-900 dark:text-zinc-100">{{ $post->title }}</h2>
        <div class="mt-2 flex items-center gap-4 text-sm text-neutral-500 dark:text-zinc-400">
            <span>{{ $post->user->name }}</span>
            <span>{{ $post->created_at->format('M j, Y') }}</span>
        </div>
        <div
            class="mt-6 prose prose-neutral dark:prose-invert max-w-none text-neutral-700 dark:text-zinc-300 whitespace-pre-wrap">
            {{ $post->content }}</div>
    </article>

    <div class="mt-6">
        <a href="{{ request('from') === 'my-posts' ? route('my-posts.posts.index') : route('home') }}"
            class="text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
            &larr; {{ __('Back to posts') }}
        </a>
    </div>
</x-app-layout>
