@if ($paginator->hasPages())
    <div>
        @if ($paginator->onFirstPage())
            <span>&laquo; ก่อนหน้า</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">&laquo; ก่อนหน้า</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">ถัดไป &raquo;</a>
        @else
            <span>ถัดไป &raquo;</span>
        @endif

        <br><br>
        @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if ($page == $paginator->currentPage())
                <strong>[{{ $page }}]</strong>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach
    </div>
@endif
