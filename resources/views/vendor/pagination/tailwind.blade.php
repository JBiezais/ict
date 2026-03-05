@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex gap-2 items-center justify-between sm:hidden">

            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-500 dark:text-zinc-400 bg-transparent border border-neutral-200 dark:border-zinc-700 cursor-not-allowed rounded-md dark:bg-transparent">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-700 dark:text-zinc-200 border border-neutral-200 dark:border-zinc-700 rounded-md hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 active:bg-neutral-50 dark:active:bg-zinc-800 transition ease-in-out duration-150 dark:hover:border-zinc-600">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-700 dark:text-zinc-200 border border-neutral-200 dark:border-zinc-700 rounded-md hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 active:bg-neutral-50 dark:active:bg-zinc-800 transition ease-in-out duration-150 dark:hover:border-zinc-600">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-neutral-500 dark:text-zinc-400 bg-transparent border border-neutral-200 dark:border-zinc-700 cursor-not-allowed rounded-md dark:bg-transparent">
                    {!! __('pagination.next') !!}
                </span>
            @endif

        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-4 sm:items-center sm:justify-between">

            <div>
                <p class="text-xs text-neutral-500 dark:text-zinc-400 leading-5">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span
                            class="font-medium text-emerald-600 dark:text-emerald-400">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span
                            class="font-medium text-emerald-600 dark:text-emerald-400">{{ $paginator->lastItem() }}</span>
                    @else
                        <span
                            class="font-medium text-emerald-600 dark:text-emerald-400">{{ $paginator->count() }}</span>
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span
                    class="inline-flex rtl:flex-row-reverse rounded-md border border-neutral-200 dark:border-zinc-700">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="inline-flex items-center px-2 py-1.5 text-sm font-medium text-neutral-400 dark:text-zinc-500 bg-transparent cursor-not-allowed rounded-l-md dark:bg-transparent"
                                aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="inline-flex items-center px-2 py-1.5 text-sm font-medium text-neutral-600 dark:text-zinc-400 rounded-l-md hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 active:bg-neutral-50 dark:active:bg-zinc-700 transition ease-in-out duration-150"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="inline-flex items-center px-3 py-1.5 -ml-px text-sm font-medium text-neutral-500 dark:text-zinc-400 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 -ml-px text-sm font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-zinc-700/80 cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex items-center px-3 py-1.5 -ml-px text-sm font-medium text-neutral-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 active:bg-neutral-50 dark:active:bg-zinc-700 transition ease-in-out duration-150"
                                        aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="inline-flex items-center px-2 py-1.5 -ml-px text-sm font-medium text-neutral-600 dark:text-zinc-400 rounded-r-md hover:text-emerald-600 dark:hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 active:bg-neutral-50 dark:active:bg-zinc-700 transition ease-in-out duration-150"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="inline-flex items-center px-2 py-1.5 -ml-px text-sm font-medium text-neutral-400 dark:text-zinc-500 rounded-r-md cursor-not-allowed"
                                aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
