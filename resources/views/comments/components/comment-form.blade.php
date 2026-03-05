@props(['post', 'parentId' => null, 'rows' => 3])

<form method="POST" action="{{ route('posts.comments.store', $post) }}" class="mt-3">
    @csrf
    @if ($parentId)
        <input type="hidden" name="parent_id" value="{{ $parentId }}" />
    @endif
    <div>
        <label for="comment-content-{{ $parentId ?? 'new' }}" class="sr-only">{{ __('Comment') }}</label>
        <textarea name="content" id="comment-content-{{ $parentId ?? 'new' }}" rows="{{ $rows }}" required
            placeholder="{{ __('Write a comment...') }}"
            class="block w-full rounded-md border border-neutral-200 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-neutral-900 dark:text-zinc-100 placeholder:text-neutral-400 dark:placeholder:text-zinc-500 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 resize-none">{{ old('content') }}</textarea>
        <x-form.input-error :messages="$errors->get('content')" class="mt-2" />
    </div>
    <div class="mt-2 flex justify-end">
        <x-button.primary-button type="submit" class="!py-1.5 !px-3 !text-sm !normal-case !tracking-normal">
            {{ $parentId ? __('Reply') : __('Comment') }}
        </x-button.primary-button>
    </div>
</form>
