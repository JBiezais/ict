<x-app-layout>
    <h2 class="text-2xl font-bold text-neutral-900 dark:text-zinc-100 mb-6">{{ __('Edit Post') }}</h2>

    <form method="POST" action="{{ route('my-posts.posts.update', $post) }}"
        class="rounded-lg border border-neutral-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <x-form.input-label for="title" :value="__('Title')" />
            <x-form.text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $post->title)"
                required autofocus />
            <x-form.input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-form.input-label for="content" :value="__('Content')" />
            <x-form.textarea name="content" id="content" rows="10"
                class="block mt-1 w-full">{{ old('content', $post->content) }}</x-form.textarea>
            <x-form.input-error :messages="$errors->get('content')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center gap-4">
            <x-button.primary-button>{{ __('Update Post') }}</x-button.primary-button>
            <a href="{{ route('my-posts.posts.index') }}"
                class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
</x-app-layout>
