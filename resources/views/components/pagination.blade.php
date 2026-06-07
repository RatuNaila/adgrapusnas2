@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
        {{-- Mobile --}}
        <div class="flex-1 flex items-center justify-between sm:hidden">
            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center px-4 py-2 text-sm text-slate-400 bg-white border border-slate-200 rounded-md">
                    &laquo; Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="inline-flex items-center px-4 py-2 text-sm text-slate-700 bg-white border border-slate-200 rounded-md hover:bg-slate-50">
                    &laquo; Sebelumnya
                </a>
            @endif

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="ml-3 inline-flex items-center px-4 py-2 text-sm text-slate-700 bg-white border border-slate-200 rounded-md hover:bg-slate-50">
                    Selanjutnya &raquo;
                </a>
            @else
                <span
                    class="ml-3 inline-flex items-center px-4 py-2 text-sm text-slate-400 bg-white border border-slate-200 rounded-md">
                    Selanjutnya &raquo;
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-600">
                    Menampilkan <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    – <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    dari <span class="font-medium">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-md shadow-sm isolate">
                    {{-- Prev --}}
                    @if ($paginator->onFirstPage())
                        <span
                            class="inline-flex items-center px-2 py-2 text-slate-400 ring-1 ring-slate-200 bg-white rounded-l-md">&#171;</span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                            class="inline-flex items-center px-2 py-2 text-slate-700 ring-1 ring-slate-200 bg-white hover:bg-slate-50 rounded-l-md">&#171;</a>
                    @endif

                    {{-- Numbers --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span
                                class="inline-flex items-center px-4 py-2 text-sm text-slate-500 ring-1 ring-slate-200 bg-white">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 ring-1 ring-blue-600">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex items-center px-4 py-2 text-sm text-slate-700 ring-1 ring-slate-200 bg-white hover:bg-slate-50">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                            class="inline-flex items-center px-2 py-2 text-slate-700 ring-1 ring-slate-200 bg-white hover:bg-slate-50 rounded-r-md">&#187;</a>
                    @else
                        <span
                            class="inline-flex items-center px-2 py-2 text-slate-400 ring-1 ring-slate-200 bg-white rounded-r-md">&#187;</span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
