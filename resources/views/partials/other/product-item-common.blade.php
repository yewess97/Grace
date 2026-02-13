@if ($container === MAIN_IMAGE)
    {{-- Product Image --}}
    <img src="{{imageSource($product, MAIN_IMAGE)}}" alt="{{ $product->{NAME} }}">

    {{-- Product Discount --}}
    @oldprice ($product->old_price, $product->new_price)
        <span class="discount-percent position-absolute end-0 fw-500 lh-1">@discount($product->new_price, $product->old_price)</span>
    @endoldprice
@endif


@if ($container === 'info')
    {{-- Product Name --}}
    <h3 class="product-info-name text-capitalize @isset($is_single_view) mt-0 @endisset">{{ $product->{NAME} }}</h3>

    {{-- Product Price --}}
    <div class="product-info-price d-flex flex-wrap align-items-center w-100 overflow-hidden lh-1 @isset($is_single_view) mt-0 @endisset">
        <span class="new-price fs-6 fw-600">@priceFormat($product->new_price)</span>
        @oldprice ($product->old_price, $product->new_price)
            <s class="old-price fs-7">@priceFormat($product->old_price)</s>
        @endoldprice
    </div>
@endif


@if ($container === 'actions')
    @if ($product->{STATUS} === 1)
        {{-- Add To Cart --}}
        <button type="submit" role="button" title="{{capitalizeAll(ADD_TO_CART)}}" class="add-cart-btn d-grid place-items-center fs-6 text-white border-0 rounded-1 @isset($is_single_view) opacity-100 visible @endisset" data-tooltip="tooltip" data-mdb-placement="top" aria-label="{{capitalizeAll(ADD_TO_CART)}}">
            <i class="ti ti-shopping-cart"></i>
        </button>

        {{-- Add To Wishlist --}}
        <button type="submit" role="button" title="{{capitalizeAll(ADD_TO_WISHLIST)}}" class="add-wishlist-btn d-grid place-items-center fs-6 text-white border-0 rounded-1 @isset($is_single_view) opacity-100 visible @endisset" data-tooltip="tooltip" data-mdb-placement="top" aria-label="{{capitalizeAll(ADD_TO_WISHLIST)}}">
            <i class="ti ti-heart"></i>
        </button>
    @endif

    {{-- Quick View --}}
    <button type="button" role="button" title="{{capitalizeAll(QUICK_VIEW)}}" class="quick-view-btn d-grid place-items-center fs-6 text-white border-0 rounded-1 @isset($is_single_view) opacity-100 visible @endisset" data-tooltip="tooltip" data-mdb-placement="top" data-mdb-toggle="modal" data-mdb-target="#product_quick_view_modal" data-route="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" data-main_image="{{imageSource($product, MAIN_IMAGE)}}" aria-label="{{capitalizeAll(QUICK_VIEW)}}">
        <x-action-icon action="view"/>
    </button>
@endif
