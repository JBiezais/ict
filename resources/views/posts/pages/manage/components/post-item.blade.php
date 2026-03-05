@props(['post'])

<article class="px-4 py-3">
    <div class="flex items-start justify-between gap-3">
        <a href="{{ route('posts.show', ['post' => $post->id, 'from' => 'my-posts']) }}"
            class="min-w-0 flex-1 font-medium text-neutral-900 dark:text-zinc-100 hover:text-emerald-600 dark:hover:text-emerald-400">
            {{ $post->title }}
        </a>
        <div class="shrink-0">
            <x-nav.dropdown align="right" alignMobile="left" width="w-36"
                contentClasses="py-1 bg-white dark:bg-zinc-800 overflow-visible">
                <x-slot name="trigger">
                    <button type="button"
                        class="p-1 rounded-md text-neutral-500 dark:text-zinc-400 hover:bg-neutral-100 dark:hover:bg-zinc-700 hover:text-neutral-700 dark:hover:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        aria-label="{{ __('Actions') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-nav.dropdown-link :href="route('my-posts.posts.edit', ['post' => $post->id])"
                        class="flex items-center gap-2 px-4 py-2">
                        <span class="inline-flex items-center gap-3 whitespace-nowrap">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                            </svg>
                            {{ __('Edit') }}
                        </span>
                    </x-nav.dropdown-link>
                    <form method="POST"
                        action="{{ route('my-posts.posts.destroy', ['post' => $post->id]) }}"
                        class="[&>button]:block [&>button]:w-full [&>button]:text-start"
                        onsubmit="return confirm('{{ __('Are you sure you want to delete this post?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-neutral-100 dark:hover:bg-zinc-700 focus:outline-none w-full min-w-max">
                            <span class="inline-flex items-center gap-3 whitespace-nowrap">
                                <svg class="w-4 h-4 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                {{ __('Delete') }}
                            </span>
                        </button>
                    </form>
                </x-slot>
            </x-nav.dropdown>
        </div>
    </div>
    <time datetime="{{ $post->createdAt->toIso8601String() }}"
        class="mt-1 block text-sm text-neutral-500 dark:text-zinc-400">
        {{ $post->createdAt->format('M j, Y') }}
    </time>
</article>
