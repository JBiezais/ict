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
                        class="rounded border-neutral-300 dark:border-zinc-600 dark:bg-zinc-800 text-neutral-600 dark:text-zinc-400 shadow-sm focus:ring-emerald-500 dark:focus:ring-emerald-400 dark:focus:ring-offset-zinc-900"
                        name="remember">
                    <span class="ms-2 text-sm text-neutral-600 dark:text-zinc-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 dark:focus:ring-offset-zinc-900"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button.primary-button class="ms-3">
                    {{ __('Log in') }}
                </x-button.primary-button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="mt-6 pt-6 border-t border-neutral-200 dark:border-zinc-700">
                <a href="{{ route('register') }}"
                    class="block w-full text-center px-4 py-3 text-sm font-semibold text-neutral-700 dark:text-zinc-300 bg-neutral-100 dark:bg-zinc-800 border border-neutral-200 dark:border-zinc-700 rounded-md hover:bg-neutral-200 dark:hover:bg-zinc-700 hover:text-neutral-900 dark:hover:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-colors duration-200">
                    {{ __('Register') }}
                </a>
            </div>
        @endif
    </x-slot>
</x-authentication-layout>
