<ul role="list" @class([
        'products-content row',
        'box-content px-0 rounded-start' => !str(Route::currentRouteName())->exactly(HOME),
        session()->has('no_results') ? 'justify-content-center' : 'row-cols-1 row-cols-md-4',
    ])>
    @if (session()->has('no_results'))
        <li role="listitem" class="col">
            <img src="{{imageSource('no-results.webp')}}" alt="No Results Found">
        </li>
    @else
        @foreach ($products as $product)
            <li role="listitem" class="product-item col position-relative">
                <x-products.item :product="$product"/>
            </li>
        @endforeach
    @endif
</ul>

{{-- User Products Pagination --}}
<div class="table-pagination col-12">@pagination($products, $products_pagination_route)</div>


{{-- Quick View Product Modal --}}
@include(USER_QUICK_VIEW_PRODUCT_MODAL)
