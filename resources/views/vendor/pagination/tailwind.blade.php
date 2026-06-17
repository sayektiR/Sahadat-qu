@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center">
        <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-11 w-11 items-center justify-center border-r border-slate-200 text-slate-300" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    &lsaquo;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-11 w-11 items-center justify-center border-r border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-blue-950" aria-label="@lang('pagination.previous')">
                    &lsaquo;
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-11 min-w-11 items-center justify-center border-r border-slate-200 px-3 text-sm text-slate-400" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-11 min-w-11 items-center justify-center border-r border-blue-950 bg-blue-950 px-4 text-sm font-semibold text-white" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-11 min-w-11 items-center justify-center border-r border-slate-200 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-blue-950">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-11 w-11 items-center justify-center text-slate-600 hover:bg-slate-100 hover:text-blue-950" aria-label="@lang('pagination.next')">
                    &rsaquo;
                </a>
            @else
                <span class="inline-flex h-11 w-11 items-center justify-center text-slate-300" aria-disabled="true" aria-label="@lang('pagination.next')">
                    &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@endif
