<article id="cart_content">
    {{-- Cart Title --}}
    <div class="title d-flex justify-content-between align-items-center border-top border-bottom">
        <h2 class="fs-6 fw-600">My {{ucfirst(CART_MODEL)}}:</h2>
        <p class="count">
            <span class="counter">{{$cart_total_items}} Items</span>
        </p>
    </div>

    {{-- Cart Products --}}
    <ul role="list" class="products pagination-container">
        @foreach ($user_cart_items as $cart_item)
            <li role="listitem" id="item_{{$cart_item->id}}" class="product position-relative row justify-content-between align-items-center border-bottom">
                <input type="hidden" name="update_cart_product_id" value="{{ $cart_item->{PRODUCT_ID} }}">
                @if ($cart_item->{PRODUCT_MODEL}->{STATUS} === 0)
                    <div role="dialog" class="product-not-available-overlay position-absolute" aria-label="{{PRODUCT_MODEL}} Not Available Overlay"></div>
                @endif
                {{-- Cart Product Image & Info--}}
                <article class="product-img-info row col-12">
                    {{-- Cart Product Image --}}
                    <a href="{{route(PRODUCT_DETAILS, $cart_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="product-img col-4">
                        <img src="{{imageSource($cart_item->{PRODUCT_MODEL}, MAIN_IMAGE)}}" alt="{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}">
                    </a>

                    {{-- Cart Product Info --}}
                    <div class="product-info col-7 px-2">
                        <a href="{{route(PRODUCT_DETAILS, $cart_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="fs-sm-6 fw-600">{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}</a>
                        <p>
                            <span class="fw-600">{{ucfirst(SIZE)}}:</span>
                            <span>{{strtoupper($cart_item->selected_product_size)}}</span>
                            <input type="hidden" name="update_cart_product_size" class="cart-product-size" value="{{ $cart_item->{PRODUCT_SIZE} }}">
                        </p>
                        <p>{{config('app.name')}} Store</p>
                        <p>@priceFormat($cart_item->product->new_price)</p>
                    </div>
                </article>

                {{-- Cart Product Quantity --}}
                <article class="product-actions d-grid place-items-center">
                    @if ($cart_item->{PRODUCT_MODEL}->{STATUS} === 1)
                        <div class="cart-product-quantity d-flex align-items-center">
                            <x-product-quantity :cart_item="$cart_item" id="update_cart_product_quantity_{{  $cart_item->{PRODUCT_ID} }}" class="update-cart-product-quantity"/>
                        </div>
                    @else
                        <p>The {{ucfirst(PRODUCT_MODEL)}} is Out Of Stock</p>
                        <input type="hidden" name="update_cart_product_quantity">
                    @endif
                    {{-- Remove Cart Product --}}
                    <button type="button" role="button" title="Remove {{PRODUCT_MODEL}} from the {{CART_MODEL}}" class="product-remove text-decoration-underline bg-transparent border-0" data-route="{{route(DELETE_CART, $cart_item->id)}}" data-id="{{$cart_item->id}}">Remove</button>
                </article>

                {{-- Cart Item Total Price --}}
                <article class="product-total-price d-flex justify-content-end">
                    <span class="fw-600">@priceFormat(($cart_item->product_quantity) * ($cart_item->product->new_price))</span>
                </article>
            </li>
        @endforeach
    </ul>
</article>
