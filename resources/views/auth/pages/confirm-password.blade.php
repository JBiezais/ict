<x-authentication-layout>
    <x-slot name="content">
        <div class="mb-4 text-sm text-neutral-600 dark:text-zinc-400">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div>
                <x-form.input-label for="password" :value="__('Password')" />

                <x-form.text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />

                <x-form.input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-4">
                <x-button.primary-button>
                    {{ __('Confirm') }}
                </x-button.primary-button>
            </div>
        </form>
    </x-slot>
</x-authentication-layout>
