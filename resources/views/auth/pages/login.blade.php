<x-authentication-layout>
    <x-slot name="content">
        <!-- Session Status -->
        <x-ui.auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-form.input-label for="email" :value="__('Email')" />
                <x-form.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
                <x-form.input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-form.input-label for="password" :value="__('Password')" />

                <x-form.text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />

                <x-form.input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded dark:bg-stone-900 dark:border-stone-600 border-gray-300 text-stone-600 dark:text-stone-400 shadow-sm focus:ring-stone-500 dark:focus:ring-stone-400 dark:focus:ring-offset-stone-950"
                        name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-stone-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-stone-400 hover:text-gray-900 dark:hover:text-stone-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500 dark:focus:ring-stone-400 dark:focus:ring-offset-stone-950"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button.primary-button class="ms-3">
                    {{ __('Log in') }}
                </x-button.primary-button>
            </div>
        </form>
    </x-slot>
</x-authentication-layout>
