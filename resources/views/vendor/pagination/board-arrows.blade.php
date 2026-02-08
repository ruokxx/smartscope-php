@if ($paginator->hasPages())
    <div class="pagination" style="display:flex; gap:8px; justify-content:center; align-items:center;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="muted" style="opacity:0.5; cursor:not-allowed;">&larr;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="text-decoration:none; font-size:18px;">&larr;</a>
        @endif

        {{-- Pagination Elements --}}
        {{-- Pagination Elements --}}
        <span class="muted" style="font-size:14px;">{{ __('messages.page') }} {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="text-decoration:none; font-size:18px;">&rarr;</a>
        @else
            <span class="muted" style="opacity:0.5; cursor:not-allowed;">&rarr;</span>
        @endif
    </div>
@endif
