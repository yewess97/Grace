<form role="form" action="{{route(CREATE_UPDATE_CART, ADD)}}" method="post" class="add-cart-form product-form">
    @csrf
    <input type="hidden" name="add_cart_product_id" value="{{$product->id}}">
    <a href="{{route(PRODUCT_DETAILS, $product->{SLUG})}}" role="link">
        {{-- Multiple Items Grid View --}}
        <section class="grid-view-multiple-items">
            {{-- Product Image & Discount & Actions --}}
            <article class="product-img position-relative d-flex align-items-center rounded-2 overflow-hidden">
                {{-- Product Image & Discount --}}
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => MAIN_IMAGE])

                {{-- Product Actions --}}
                <div class="product-actions position-absolute start-0 end-0 d-flex justify-content-center align-items-center">
                    @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'actions'])
                </div>
            </article>

            {{-- Product Info (Name & Price) --}}
            <article class="product-info">
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'info'])
            </article>
        </section>


        {{-- Single Item Grid View --}}
        <section class="grid-view-single-item border d-none">
            {{-- Product Image & Discount --}}
            <article class="product-img position-relative col-12 col-md-3 rounded-2 overflow-hidden">
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => MAIN_IMAGE])
            </article>

            {{-- Product Info --}}
            <article class="product-info d-flex flex-column justify-content-center gap-2 px-3 px-md-0 py-3">
                {{-- Product Name & Price --}}
                @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'info', 'is_single_view' => true])

                {{-- Product Short Description --}}
                <p class="product-info-short-desc">{!! $product->{SHORT_DESCRIPTION} !!}</p>

                {{-- Product Actions --}}
                <article class="product-actions d-flex align-items-center mt-2">
                    @include(PRODUCT_ITEM_COMMON_PARTIAL, [PRODUCT_MODEL => $product, 'container' => 'actions', 'is_single_view' => true])
                </article>
            </article>
        </section>
    </a>
</form>

{{-- Add or Remove Wishlist Form --}}
<x-wishlist-cart.add-remove-wishlist-form product_id="{{$product->id}}" />
