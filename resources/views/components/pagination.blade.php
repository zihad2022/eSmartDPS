<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center space-x-2">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded">Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
            class="px-3 py-1 text-sm bg-white text-gray-700 border rounded hover:bg-gray-100">
            Prev
        </a>
    @endif

    {{-- Pagination Elements --}}
    @php
        $elements = $paginator->links()->elements[0] ?? [$paginator->currentPage() => null];
    @endphp

    @foreach ($elements as $page => $url)
        @if ($page == $paginator->currentPage())
            <span class="px-3 py-1 text-sm font-semibold bg-accent-500 text-white rounded">
                {{ $page }}
            </span>
        @else
            <a href="{{ $url ?? '#' }}"
                class="px-3 py-1 text-sm bg-white text-gray-700 border rounded hover:bg-gray-100">
                {{ $page }}
            </a>
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
            class="px-3 py-1 text-sm bg-white text-gray-700 border rounded hover:bg-gray-100">
            Next
        </a>
    @else
        <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded">Next</span>
    @endif
</nav>
