@props([
    'align' => 'right',
    'alignMobile' => null,
    'width' => '48',
    'contentClasses' => 'py-1 bg-white dark:bg-zinc-800',
    'closeOnContentClick' => true,
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'top' => 'origin-top',
        default => 'ltr:origin-top-right rtl:origin-top-left end-0',
    };

    if ($alignMobile === 'left' && $align === 'right') {
        $alignmentClasses =
            'ltr:origin-top-left rtl:origin-top-right start-0 sm:ltr:origin-top-right sm:rtl:origin-top-left sm:end-0';
    } elseif ($alignMobile === 'right' && $align === 'left') {
        $alignmentClasses =
            'ltr:origin-top-right rtl:origin-top-left end-0 sm:ltr:origin-top-left sm:rtl:origin-top-right sm:start-0';
    }

    $width = match ($width) {
        '48' => 'w-48',
        default => $width,
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
        style="display: none;" {{ $closeOnContentClick ? 'x-on:click="open = false"' : '' }}>
        <div class="rounded-md ring-1 ring-neutral-900/5 dark:ring-neutral-100/5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
