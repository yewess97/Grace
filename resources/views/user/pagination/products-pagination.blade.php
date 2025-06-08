<ul role="list" class="@if (!str(Route::currentRouteName())->exactly('home')) box-content px-0 rounded-start @endif products-content row {{session()->has('no_results') ? 'justify-content-center' : 'row-cols-1 row-cols-md-4'}}">
    @if (session()->has('no_results'))
        <li role="listitem" class="col">
            <img src="{{imageSource('no-results.png')}}" alt="No Results Found">
        </li>
    @else
        @foreach ($products as $product)
            <li role="listitem" class="product-item col position-relative">
                <x-product-item :product="$product"/>
            </li>
        @endforeach
    @endif
</ul>

{{-- User Products Pagination --}}
<div class="table-pagination col-12">@pagination($products, $products_pagination_route)</div>


{{-- Quick View Product Modal --}}
@include(USER_QUICK_VIEW_PRODUCT_MODAL)
