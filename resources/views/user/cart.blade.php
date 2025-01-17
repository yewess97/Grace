@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Cart Main --}}
    <main role="main" class="cart-main py-6">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                @if (Session::has(EMPTY_CART))
                    <div class="col">
                        <div class="empty-cart d-grid place-items-center gap-5">
                            <h2 class="empty-cart-title d-grid place-items-center gap-3 fs-1 fw-600">
                                <span>Your</span>
                                <span>{{ucfirst(CART_MODEL)}}</span>
                                <span>is currently</span>
                                <span>Empty</span>
                            </h2>
                            <a href="{{route(PRODUCTS_LIST)}}" type="button" role="link" class="btn">
                                {{pluralize(ADD_PRODUCT_TITLE)}} Now
                            </a>
                        </div>
                    </div>
                @else
                    <div class="main-sides row col-12">
                        <!----======= Left Side =======---->
                        <section class="cart col-9 px-sm-3">
                            <div class="box-content border rounded">
                                <form action="{{route(CREATE_UPDATE_CART, UPDATE)}}" method="post" role="form" class="update-cart-form">
                                    {{-- Cart Title --}}
                                    <article class="cart-title d-flex justify-content-between align-items-center border-top border-bottom">
                                        <h2 class="fs-6 fw-600">My {{ucfirst(CART_MODEL)}}:</h2>
                                        <p class="cart-count">
                                            <span class="cart-counter">{{$user_cart_items->sum(PRODUCT_QUANTITY)}} Items</span>
                                        </p>
                                    </article>
                                    {{-- Cart Products --}}
                                    <ul role="list" class="cart-products">
                                        @foreach ($user_cart_items as $cart_item)
                                            <li role="listitem" class="cart-product position-relative row justify-content-between align-items-center border-bottom">
                                                <input type="hidden" name="update_cart_product_id" value="{{ $cart_item->{PRODUCT_ID} }}">
                                                @if ($cart_item->{PRODUCT_MODEL}->{STATUS} === 0)
                                                    <div role="dialog" class="product-not-available-overlay position-absolute" aria-label="{{PRODUCT_MODEL}} Not Available Overlay"></div>
                                                @endif
                                                {{-- Cart Product Image & Info --}}
                                                <article class="cart-product-img-info row col-12">
                                                    {{-- Cart Product Image --}}
                                                    <a href="{{route(PRODUCT_DETAILS, $cart_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="cart-product-img col-4 h-fit-content">
                                                        <img src="{{imageSource($cart_item->{PRODUCT_MODEL}, MAIN_IMAGE)}}" alt="{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}" class="h-auto">
                                                    </a>
                                                    {{-- Cart Product Info --}}
                                                    <div class="cart-product-info col-7 px-2">
                                                        <a href="{{route(PRODUCT_DETAILS, $cart_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="fs-sm-6 fw-600">{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}</a>
                                                        <p>
                                                            <span class="fw-600">{{ucfirst(SIZE)}}:</span>
                                                            <span>{{strtoupper($cart_item->selected_product_size)}}</span>
                                                            <input type="hidden" name="update_cart_product_size" class="cart-product-size" value="{{ $cart_item->{PRODUCT_SIZE} }}">
                                                        </p>
                                                        <p>Grace Store</p>
                                                        <p>@price($cart_item->product->new_price)</p>
                                                    </div>
                                                </article>
                                                {{-- Cart Product Quantity --}}
                                                <article class="cart-product-quantity-remove d-grid place-items-center">
                                                    @if ($cart_item->{PRODUCT_MODEL}->{STATUS} === 1)
                                                        <div class="cart-product-quantity d-flex align-items-center">
                                                            <x-product-quantity :cart_item="$cart_item" id="update_cart_product_quantity_{{  $cart_item->{ID} }}" class="update-cart-product-quantity"/>
                                                        </div>
                                                    @else
                                                        <p>The {{ucfirst(PRODUCT_MODEL)}} is Out Of Stock</p>
                                                        <input type="hidden" name="update_cart_product_quantity">
                                                    @endif
                                                    {{-- Remove Cart Product --}}
                                                    <button type="button" role="button" title="Remove {{PRODUCT_MODEL}} from the {{CART_MODEL}}" class="cart-product-remove text-decoration-underline bg-transparent border-0" data-route="{{route(DELETE_CART, $cart_item->id)}}">Remove</button>
                                                </article>
                                                {{-- Cart Item Total Price --}}
                                                <article class="cart-product-total-price d-flex justify-content-end">
                                                    <span class="fw-600">@price(($cart_item->product_quantity) * ($cart_item->product->new_price))</span>
                                                </article>
                                            </li>
                                        @endforeach
                                    </ul>
                                    {{-- Cart Buttons --}}
                                    <article class="cart-buttons d-flex justify-content-between align-items-center border-bottom">
                                        <a href="{{route(PRODUCTS_LIST)}}" role="link" class="text-decoration-underline">
                                            Continue Shopping
                                        </a>
                                        <input type="submit" class="p-0 text-decoration-underline bg-transparent border-0" value="{{capitalizeAll(UPDATE.' '.CART_MODEL)}}">
                                        <a href="{{route(pluralize(DELETE_CART))}}" role="link" id="clear_cart" class="text-decoration-underline">Clear {{ucfirst(CART_MODEL)}}</a>
                                    </article>
                                </form>
                                <form method="post" id="cart_product_remove_form" class="delete-cart-form d-none" aria-describedby="cart_product_remove">
                                    @csrf
                                    @method(strtoupper(DELETE))
                                </form>
                            </div>
                        </section>

                        <!----======= Right Side =======---->
                        <aside class="summary col-3 px-sm-3">
                            <div class="summary-content box-content position-sticky border rounded">
                                {{-- Summary Title --}}
                                <article class="summary-title border-top border-bottom">
                                    <h2 class="fs-6 fw-600">Summary</h2>
                                </article>
                                {{-- Summary Body --}}
                                <ul role="list" class="summary-body d-grid">
                                    <li role="listitem" class="d-flex justify-content-between align-items-center">
                                        <span>Subtotal</span>
                                        <span class="fw-600">@price($total_cost)</span>
                                    </li>
                                    <li role="listitem" class="d-flex justify-content-between align-items-center">
                                        <span>Shipping</span>
                                        <span class="fw-600">Free</span>
                                    </li>
                                </ul>
                                {{-- Summary Total --}}
                                <article class="summary-total d-flex justify-content-between align-items-center border-top">
                                    <div>
                                        <h2 class="fw-600">Total</h2>
                                        <p>(Including VAT)</p>
                                    </div>
                                    <span class="fw-600">@price($total_cost)</span>
                                </article>
                                {{-- Summary Proceed to Checkout Button --}}
                                <a href="{{route(CHECKOUT)}}" type="button" role="link" class="btn btn-block mt-2">
                                    Proceed to {{ucfirst(CHECKOUT)}}
                                </a>
                            </div>
                        </aside>
                    </div>
                @endif
            </div>
        </div>
    </main>

@endsection
