@props(['comment', 'post', 'depth' => 0, 'maxDepth' => 5])

@php
    $isOwner = auth()->check() && auth()->id() === $comment->user_id;
    $isReply = $depth > 0;
    $canReply = $depth < $maxDepth;
@endphp

<article id="comment-{{ $comment->id }}" x-data="{ editing: false, replyOpen: false, repliesOpen: false }"
    {{ $attributes->merge([
        'class' => $isReply ? 'mt-2' : 'py-3 border-b border-neutral-200 dark:border-zinc-700 last:border-b-0',
    ]) }}>
    <div class="flex items-start justify-between gap-3 group">
        <div class="min-w-0 flex-1">
            <div class="flex items-baseline gap-2 text-xs text-neutral-500 dark:text-zinc-400">
                <span class="font-medium text-neutral-900 dark:text-zinc-100">{{ $comment->user->name }}</span>
                <span aria-hidden="true">·</span>
                <time datetime="{{ $comment->created_at->toIso8601String() }}"
                    title="{{ $comment->created_at->format('M j, Y g:i A') }}">
                    {{ $comment->created_at->diffForHumans() }}
                </time>
            </div>
            <div class="mt-0.5">
                <div x-show="!editing"
                    class="content-block text-sm leading-snug text-neutral-700 dark:text-zinc-300 whitespace-pre-line">
                    {{ Str::trim($comment->content) }}</div>
                <form method="POST" action="{{ route('posts.comments.update', [$post, $comment]) }}"
                    @submit="editing = false" x-show="editing" x-cloak class="mt-1">
                    @csrf
                    @method('PUT')
                    <textarea name="content" rows="3" required x-init="$el.value = {{ Js::from($comment->content) }}"
                        class="block w-full rounded-md border border-neutral-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-neutral-900 dark:text-zinc-100 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 resize-none"></textarea>
                    <div class="mt-2 flex gap-2">
                        <x-button.primary-button type="submit"
                            class="!py-1 !px-2 !text-sm !normal-case !tracking-normal">{{ __('Save') }}</x-button.primary-button>
                        <button type="button" @click="editing = false"
                            class="rounded-md px-3 py-1.5 text-sm font-medium text-neutral-600 dark:text-zinc-400 hover:text-neutral-900 dark:hover:text-zinc-100 hover:bg-neutral-100 dark:hover:bg-zinc-700">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($comment->children_count > 0 || ($canReply && auth()->check()) || $isOwner)
        <div class="mt-1.5 flex items-center gap-4">
            @if ($comment->children_count > 0)
                <button type="button" @click="repliesOpen = !repliesOpen"
                    class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        x-show="!repliesOpen">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        x-show="repliesOpen" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                    <span
                        x-show="!repliesOpen">{{ __('View :count replies', ['count' => $comment->children_count]) }}</span>
                    <span x-show="repliesOpen" x-cloak>{{ __('Hide replies') }}</span>
                </button>
            @endif
            @auth
                @if ($canReply)
                    <button type="button" @click="replyOpen = !replyOpen"
                        class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                        {{ __('Reply') }}
                    </button>
                @endif
            @endauth
            @if ($isOwner)
                <button type="button" @click="editing = true"
                    class="inline-flex items-center gap-1 text-xs text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-zinc-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                    {{ __('Edit') }}
                </button>
                <form method="POST" action="{{ route('posts.comments.destroy', [$post, $comment]) }}"
                    class="inline-block"
                    onsubmit="return confirm('{{ __('Are you sure you want to delete this comment?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1 text-xs text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        {{ __('Delete') }}
                    </button>
                </form>
            @endif
        </div>
    @endif

    @auth
        @if ($canReply)
            <div class="mt-2" x-show="replyOpen" x-cloak x-transition>
                <x-comments::comment-form :post="$post" :parent-id="$comment->id" :rows="2" />
            </div>
        @endif
    @endauth

    @if ($comment->children_count > 0)
        <div x-show="repliesOpen" x-cloak x-transition
            class="mt-2 pl-6 md:pl-8 border-l-2 border-neutral-200 dark:border-zinc-600 space-y-5">
            @foreach ($comment->children as $child)
                <x-comments::comment :comment="$child" :post="$post" :depth="$depth + 1" :max-depth="$maxDepth" />
            @endforeach
        </div>
    @endif
</article>
