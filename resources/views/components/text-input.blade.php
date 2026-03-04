@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-stone-600 dark:bg-stone-900 dark:text-stone-100 focus:border-stone-500 dark:focus:border-stone-400 focus:ring-stone-500 dark:focus:ring-stone-400 rounded-md shadow-sm']) }}>
