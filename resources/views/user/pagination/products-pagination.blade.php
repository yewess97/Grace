<ul role="list" class="@if (!str(Route::currentRouteName())->exactly('home')) box-content px-0 rounded-start @endif products-content row row-cols-1 row-cols-md-4">
    @foreach ($products as $product)
        <li role="listitem" class="product-item col position-relative">
            @include(PRODUCT_ITEM_COMPONENT, [PRODUCT_MODEL => $product])
        </li>
    @endforeach
</ul>

{{-- User Products Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($products, $products_pagination_route)</div>


{{-- Quick View Product Modal --}}
@include(USER_QUICK_VIEW_PRODUCT_MODAL)
