@if ($paginator->hasPages())
    <nav role="navigation" class="nav justify-content-end" aria-label="Page navigation">
        <ul role="list" class="pagination flex-wrap gap-1">
            @if ($paginator->onFirstPage())
                <li role="listitem" class="page-item disabled" aria-disabled="true" aria-label="First page">
                    <a href="javascript:;" role="link" rel="prev" class="page-link fs-7 fw-500" tabindex="-1" aria-disabled="true" aria-label="First page">
                        <i class="fa-solid fa-angles-left"></i>
                    </a>
                </li>
                <li role="listitem" class="page-item disabled" aria-disabled="true" aria-label="Previous page">
                    <a href="javascript:;" role="link" rel="prev" class="page-link fs-7 fw-500" tabindex="-1" aria-disabled="true" aria-label="Previous page">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                </li>
            @else
                <li role="listitem" class="page-item" aria-label="First page">
                    <a href="{{ $paginator->url(1) }}" role="link" rel="prev" class="page-link fs-7 fw-500" data-route="{{$route}}" aria-label="First page">
                        <i class="fa-solid fa-angles-left"></i>
                    </a>
                </li>
                <li role="listitem" class="page-item" aria-label="Previous page">
                    <a href="{{ $paginator->previousPageUrl() }}" role="link" rel="prev" class="page-link fs-7 fw-500" data-route="{{$route}}" aria-label="Previous page">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li role="listitem" class="page-item disabled" aria-disabled="true">{{$element}}</li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <li role="listitem" class="page-item active disabled" aria-disabled="true">
                                <a href="javascript:;" role="link" class="page-link fs-7 fw-500" tabindex="-1" aria-disabled="true">{{$page}}</a>
                            </li>
                        @else
                            <li role="listitem" class="page-item">
                                <a href="{{$url}}" role="link" class="page-link fs-7 fw-500" data-route="{{$route}}">{{$page}}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li role="listitem" class="page-item" aria-label="Next page">
                    <a href="{{ $paginator->nextPageUrl() }}" role="link" rel="next" class="page-link fs-7 fw-500" data-route="{{$route}}" aria-label="Next page">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                </li>
                <li role="listitem" class="page-item" aria-label="Last page">
                    <a href="{{ $paginator->url($paginator->lastPage()) }}" role="link" rel="next" class="page-link fs-7 fw-500" data-route="{{$route}}" aria-label="Last page">
                        <i class="fa-solid fa-angles-right"></i>
                    </a>
                </li>
            @else
                <li role="listitem" class="page-item disabled" aria-disabled="true" aria-label="Next page">
                    <a href="javascript:;" role="link" rel="next" class="page-link fs-7 fw-500" tabindex="-1" aria-disabled="true" aria-label="Next page">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                </li>
                <li role="listitem" class="page-item disabled" aria-disabled="true" aria-label="Last page">
                    <a href="javascript:;" role="link" rel="next" class="page-link fs-7 fw-500" tabindex="-1" aria-disabled="true" aria-label="Last page">
                        <i class="fa-solid fa-angles-right"></i>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif
