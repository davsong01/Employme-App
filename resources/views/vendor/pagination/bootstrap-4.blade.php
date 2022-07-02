                    {{-- <a href="#"><i class="fa fa-long-arrow-left"></i></a>

                    <a href="#">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#"><i class="fa fa-long-arrow-right"></i></a>
                </div> --}}

@if ($paginator->hasPages())
    {{-- <ul class="pagination"> --}}
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <a class="disabled"><i class="fa fa-long-arrow-left"></i></a>
        @else
           <a class="" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fa fa-long-arrow-left"></i></a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <a class="disabled"><span class="page-link">{{ $element }}</span></a>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        < class="page-item active"><span class="page-link">{{ $page }}</span></>
                    @else
                       <a class="" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
        @endif
    {{-- </ul> --}}
@endif
