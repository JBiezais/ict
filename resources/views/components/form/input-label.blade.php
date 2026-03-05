@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-neutral-700 dark:text-zinc-300']) }}>
    {{ $value ?? $slot }}
</label>
