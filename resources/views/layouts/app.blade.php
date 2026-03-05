<x-base-layout>
    <div class="min-h-screen bg-neutral-50 dark:bg-zinc-900 text-neutral-900 dark:text-zinc-100">
        <header class="sticky top-0 z-10 border-b border-neutral-200 dark:border-zinc-800 bg-white/70 dark:bg-zinc-800/70 backdrop-blur-md">
            <div class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
                <x-nav.app-nav />
            </div>
        </header>

        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
</x-base-layout>
