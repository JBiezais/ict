<x-base-layout>
    <div class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
            <!-- Left panel -->
            <div
                class="bg-stone-50 dark:bg-stone-900 flex flex-col justify-between p-8 md:p-12 min-h-[280px] md:min-h-0">
                <div>
                    <a href="{{ route('home') }}" class="inline-block">
                        <x-ui.application-logo />
                    </a>
                </div>
                <blockquote class="text-stone-600 dark:text-stone-400 text-lg italic">
                    "The only way to do great work is to love what you do."
                </blockquote>
            </div>

            <!-- Right panel -->
            <div class="bg-white dark:bg-stone-950 flex items-center justify-center p-8">
                <div class="w-full max-w-md">
                    {{ $content }}
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
