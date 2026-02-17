@props(['paginator'])

@if ($paginator->hasPages())
<div class="flex items-center justify-between px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
    {{-- Results count --}}
    <div class="text-xs text-zinc-600 dark:text-zinc-400">
        Showing <span class="font-xs text-zinc-900 dark:text-zinc-100">{{ $paginator->firstItem() }}</span>
        to <span class="font-xs text-zinc-900 dark:text-zinc-100">{{ $paginator->lastItem() }}</span>
        of <span class="font-xs text-zinc-900 dark:text-zinc-100">{{ $paginator->total() }}</span> results
    </div>

    {{-- Pagination controls --}}
    <div class="flex items-center">
        {{-- Previous button --}}
        @if($paginator->onFirstPage())
        <button
            disabled
            style="border-radius: 5px 0 0 5px;"
            class="inline-flex items-center justify-center h-6 w-6 border border-zinc-200 dark:border-zinc-700 text-zinc-400 dark:text-zinc-600 cursor-not-allowed">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        @else
        <a
            href="{{ $paginator->previousPageUrl() }}"
            style="border-radius: 5px 0 0 5px;"
            class="inline-flex items-center justify-center h-6 w-6 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        @endif

        {{-- Page numbers --}}
        @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $start = max(1, $currentPage - 2);
        $end = min($lastPage, $currentPage + 2);
        @endphp

        {{-- First page --}}
        @if($start > 1)
        <a
            href="{{ $paginator->url(1) }}"
            class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-zinc-300 dark:border-zinc-600 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-500 dark:hover:bg-zinc-800 transition-colors">
            1
        </a>
        @if($start > 2)
        <span class="px-2 text-zinc-500 dark:text-zinc-500">...</span>
        @endif
        @endif

        {{-- Page range --}}
        @for($page = $start; $page <= $end; $page++)
            @if($page==$currentPage)
            <span
            class="inline-flex items-center justify-center h-8 w-6 border border-zinc-900 dark:border-zinc-200 bg-zinc-900 dark:bg-zinc-100 text-sm font-medium text-zinc-400 dark:text-zinc-900 transition-colors">
            {{ $page }}
            </span>
            @else
            <a
                href="{{ $paginator->url($page) }}"
                class="inline-flex items-center justify-center h-6 w-6 border border-zinc-300 dark:border-zinc-600 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                {{ $page }}
            </a>
            @endif
            @endfor

            {{-- Last page --}}
            @if($end < $lastPage)
                @if($end < $lastPage - 1)
                <span class="px-2 text-zinc-500 dark:text-zinc-500">...</span>
                @endif
                <a
                    href="{{ $paginator->url($lastPage) }}"
                    class="inline-flex items-center justify-center h-6 w-6 border border-zinc-300 dark:border-zinc-600 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                    {{ $lastPage }}
                </a>
                @endif

                {{-- Next button --}}
                @if(!$paginator->hasMorePages())
                <button
                    disabled
                    style="border-radius: 0 5px 5px 0;"
                    class="inline-flex items-center justify-center h-6 w-6 border border-zinc-200 dark:border-zinc-700 text-zinc-400 dark:text-zinc-600 cursor-not-allowed">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                @else
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    style="border-radius: 0 5px 5px 0;"
                    class="inline-flex items-center justify-center h-6 w-6 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @endif
    </div>
</div>
@endif
