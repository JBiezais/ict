<x-base-layout>
    <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
        <!-- Left panel -->
        <div class="bg-neutral-50 dark:bg-zinc-800 flex flex-col justify-between p-8 md:p-12 min-h-[280px] md:min-h-0">
            <div class="flex items-center justify-between w-full">
                <a href="{{ route('home') }}" class="inline-block">
                    <x-ui.application-logo />
                </a>
            </div>
            <blockquote class="text-neutral-600 dark:text-zinc-400 text-lg italic">
                "The only way to do great work is to love what you do."
            </blockquote>
        </div>

        <!-- Right panel -->
        <div class="bg-white dark:bg-zinc-900 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                {{ $content }}
            </div>
        </div>
    </div>
</x-base-layout>
