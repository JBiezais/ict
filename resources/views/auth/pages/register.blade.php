<x-authentication-layout>
    <x-slot name="content">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-form.input-label for="name" :value="__('Name')" />
                <x-form.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" />
                <x-form.input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-form.input-label for="email" :value="__('Email')" />
                <x-form.text-input id="email" class="block mt-1 w-full" type="email" name="email"
                    :value="old('email')" required autocomplete="username" />
                <x-form.input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-form.input-label for="password" :value="__('Password')" />

                <x-form.text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />

                <x-form.input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-form.input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-form.text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />

                <x-form.input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button.primary-button>
                    {{ __('Register') }}
                </x-button.primary-button>
            </div>
        </form>

        @if (Route::has('login'))
            <div class="mt-6 pt-6 border-t border-neutral-200 dark:border-zinc-700">
                <a href="{{ route('login') }}"
                    class="block w-full text-center px-4 py-3 text-sm font-semibold text-neutral-700 dark:text-zinc-300 bg-neutral-100 dark:bg-zinc-800 border border-neutral-200 dark:border-zinc-700 rounded-md hover:bg-neutral-200 dark:hover:bg-zinc-700 hover:text-neutral-900 dark:hover:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-colors duration-200">
                    {{ __('Log in') }}
                </a>
            </div>
        @endif
    </x-slot>
</x-authentication-layout>
