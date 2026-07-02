<form role="form" action="{{route(CREATE_UPDATE_CART, ADD)}}" method="post" class="add-cart-form product-form">
    @csrf
    <input type="hidden" name="add_cart_product_id" value="{{$product->id}}">
    {{-- Multiple Items Grid View --}}
    <section class="grid-view-multiple-items">
        {{-- Product Image & Discount & Actions --}}
        <div class="product-img position-relative d-flex align-items-center rounded-2 overflow-hidden">
            {{-- Product Image & Discount --}}
            <a href="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" role="link">
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => MAIN_IMAGE])
            </a>
            {{-- Product Actions --}}
            <div class="product-actions position-absolute start-0 end-0 d-flex justify-content-center align-items-center">
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'actions'])
            </div>
        </div>

        {{-- Product Info (Name & Price) --}}
        <div class="product-info">
            <a href="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" role="link">
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'info'])
            </a>
        </div>
    </section>

    {{-- Single Item Grid View --}}
    <section class="grid-view-single-item border d-none">
        {{-- Product Image & Discount --}}
        <div class="product-img position-relative col-12 col-md-3 rounded-2 overflow-hidden">
            <a href="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" role="link">
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => MAIN_IMAGE])
            </a>
        </div>

        {{-- Product Info --}}
        <div class="product-info d-flex flex-column justify-content-center gap-2 px-3 px-md-0 py-3">
            <a href="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" role="link">
                {{-- Product Name & Price --}}
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'info', 'is_single_view' => true])

                {{-- Product Short Description --}}
                <p class="product-info-short-desc">{!! $product->{SHORT_DESCRIPTION} !!}</p>
            </a>
            {{-- Product Actions --}}
            <div class="product-actions d-flex align-items-center mt-2">
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'actions', 'is_single_view' => true])
            </div>
        </div>
    </section>
</form>

{{-- Add or Remove Wishlist Form --}}
<x-wishlist-cart.add-remove-wishlist-form product_id="{{$product->id}}" />
