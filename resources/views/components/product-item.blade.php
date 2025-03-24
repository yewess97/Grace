<form role="form" action="{{route(CREATE_UPDATE_CART, ADD)}}" method="post" class="add-cart-form product-form">
    @csrf
    <input type="hidden" name="add_cart_product_id" value="{{$product->id}}">
    <a href="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" role="link">
        {{-- Product Image & Actions --}}
        <article class="product-img-actions position-relative rounded-2 overflow-hidden">
            {{-- Product Image --}}
            <img src="{{imageSource($product, MAIN_IMAGE)}}" alt="{{ $product->{NAME} }}">
            @oldprice ($product->old_price, $product->new_price)
                <span class="discount-percent position-absolute end-0 fw-500 lh-1">@discount($product->new_price, $product->old_price)</span>
            @endoldprice

            {{-- Product Actions --}}
            <article class="product-action position-absolute start-0 end-0 d-flex justify-content-center align-items-center">
                @if ($product->{STATUS} === 1)
                    {{-- Add To Cart --}}
                    <button type="submit" role="button" title="{{capitalizeAll(ADD.' to '.CART_MODEL)}}" class="add-cart-btn d-grid place-items-center fs-6 text-white border-0 rounded-1" data-mdb-toggle="tooltip" data-mdb-placement="top" aria-label="{{capitalizeAll(ADD.' to '.CART_MODEL)}}">
                        <i class="ti ti-shopping-cart"></i>
                    </button>
                @endif
                {{-- Quick View --}}
                <button type="button" role="button" title="{{capitalizeAll(QUICK_VIEW)}}" class="quick-view-btn d-grid place-items-center fs-6 text-white border-0 rounded-1" data-tooltip="tooltip" data-mdb-placement="top" data-mdb-toggle="modal" data-mdb-target="#product_quick_view_modal" data-route="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" data-main_image="{{imageSource($product, MAIN_IMAGE)}}" aria-label="{{capitalizeAll(QUICK_VIEW)}}">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </article>
        </article>
        {{-- Product Info --}}
        <article class="product-info">
            <h3 class="product-info-name text-capitalize">{{ $product->{NAME} }}</h3>
            <div class="product-info-price d-flex flex-wrap align-items-center w-100 overflow-hidden lh-1">
                <span class="new-price fs-6 fw-600">@priceFormat($product->new_price)</span>
                @oldprice ($product->old_price, $product->new_price)
                    <s class="old-price fs-7">@priceFormat($product->old_price)</s>
                @endoldprice
            </div>
        </article>
    </a>
</form>
