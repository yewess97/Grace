<div id="user_cart_dropdown" @class([
        'cart-menu dropdown-menu border rounded-3',
        'd-none' => Route::currentRouteName() === CART_MODEL,
    ]) aria-labelledby="user_cart_menu">
    @if (session()->has(EMPTY_CART))
        {{-- Cart Empty --}}
        <div class="cart-empty d-grid place-items-center gap-1">
            <i class="ti ti-bag opacity-25"></i>
            <p>No {{PRODUCTS_TABLE}} in your {{CART_MODEL}}</p>
        </div>
    @else
        {{-- Cart Product Title --}}
        <p class="cart-title px-3 py-2">
            <span class="cart-total-items">There are {{$total_items}} {{pluralize(PRODUCT_MODEL, $total_items)}}</span>
        </p>
        {{-- Cart Details --}}
        <ul role="list" class="cart-details border-top">
            @foreach ($user_cart_items as $cart_item)
                <li role="listitem" class="cart-product position-relative d-flex justify-content-between align-items-center w-100">
                    @if ($cart_item->{PRODUCT_MODEL}->{STATUS} === 0)
                        <div class="product-not-available-overlay position-absolute"></div>
                    @endif
                    {{-- Cart Product Info --}}
                    <a href="{{route(PRODUCT_DETAILS, $cart_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="row align-items-center">
                        <article class="cart-product-img col-2 me-2 rounded-2">
                            <img src="{{imageSource($cart_item->{PRODUCT_MODEL}, MAIN_IMAGE)}}" alt="{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}">
                        </article>
                        <article class="cart-product-details row col gap-1">
                            <h5 class="fw-500">{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}</h5>
                            <p>Size: {{$cart_item->selected_product_size}}</p>
                            <p>
                                <span class="quantity">{{ $cart_item->{PRODUCT_QUANTITY} }} &times;</span>
                                <span class="price fs-6 fw-bold lh-sm">@priceFormat($cart_item->product->new_price)</span>
                            </p>
                        </article>
                    </a>
                    {{-- Remove Cart Product --}}
                    <form action="{{route(DELETE_CART, $cart_item->id)}}" method="post" role="form" class="delete-cart-form">
                        @csrf
                        @method(strtoupper(DELETE))
                        <button type="submit" role="button" title="Remove {{ucfirst(PRODUCT_MODEL)}}" data-tooltip="tooltip" data-mdb-placement="top" class="fs-6 bg-transparent text-danger border-0">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
        {{-- Cart Subtotal & Buttons --}}
        <ul role="list" class="cart-subtotal-btns border-top">
            <li role="listitem" class="cart-subtotal d-flex justify-content-between">
                <h3>Sub Total:</h3>
                <span>@priceFormat($total_cost)</span>
            </li>
            <li role="listitem" class="cart-btns d-flex gap-3 mt-3">
                <a href="{{route(CART_MODEL)}}" type="button" role="link" id="view_cart" class="btn w-100">
                    View {{ucfirst(CART_MODEL)}}
                </a>
                <a href="{{route(CHECKOUT)}}" type="button" role="link" class="btn w-100">
                    {{ucfirst(CHECKOUT)}}
                </a>
            </li>
        </ul>
    @endif
</div>
