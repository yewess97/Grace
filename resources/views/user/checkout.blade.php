@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Checkout Main --}}
    <section role="main" class="checkout-main">
        <div class="main-sides">
            <form action="{{route(CREATE_ORDER)}}" method="post" role="form" id="add_order_form" class="grace-form row col-12">
                @csrf
                <input type="hidden" name="add_order_status" value="1">
                <input type="hidden" name="add_order_address_id" id="order_address_id">
                <!----======= Left Side =======---->
                <section class="left-side col-12 col-lg-7">
                    <div class="shipping-payment row gap-5 py-6">
                        {{-- Shipping Payment Header --}}
                        <header role="banner" class="shipping-payment-header">
                            {{-- Shipping Payment Logo --}}
                            <a href="{{route(PRODUCTS_LIST)}}" role="link" class="shipping-payment-logo">{{config('app.name')}} Store</a>

                            {{-- Shipping Payment Breadcrumb --}}
                            <nav role="navigation" class="nav-breadcrumb" aria-label="breadcrumb">
                                <div class="container">
                                    <div class="row">
                                        <ol role="list" class="breadcrumb">
                                            <li role="listitem" class="breadcrumb-item fw-500">
                                                <a href="{{route(CART_MODEL)}}" role="link">{{ucfirst(CART_MODEL)}}</a>
                                            </li>
                                            <li role="listitem" class="breadcrumb-item active fw-500" aria-current="page">
                                                {{ucfirst(CHECKOUT)}}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </nav>
                        </header>
                        {{-- Shipping Payment Main --}}
                        <main role="main" class="shipping-payment-main row col-12 gap-5 bg-white">
                            {{$add_order_error(STATUS)}}
                            {{$add_order_error(ADDRESS_ID)}}

                            {{-- Shipping Contact Information --}}
                            <article class="shipping-contact-info col-12">
                                {{-- Shipping Contact Information Title --}}
                                <h2 class="shipping-contact-info-title fs-5">Contact Information</h2>
                                {{-- Shipping Contact Information Info --}}
                                <ul role="list" class="shipping-contact-info-details d-grid gap-2 mt-3">
                                    <li role="listitem">
                                        <span>{{ucfirst(NAME)}}:</span>
                                        <span class="ms-3 fw-500">@userFullName</span>
                                    </li>
                                    <li role="listitem">
                                        <span>{{ucfirst(EMAIL)}}:</span>
                                        <span class="ms-3 fw-500">{{ auth()->user()->{EMAIL} }}</span>
                                    </li>
                                </ul>
                            </article>
                            {{-- Shipping Details --}}
                            <article class="shipping-details row col-12 gap-4">
                                {{-- Shipping Details Title & Add New Address --}}
                                <article class="shipping-details-header col-12 d-flex justify-content-between align-items-center">
                                    <h2 class="shipping-details-title fs-5">Shipping {{ucfirst(ADDRESS_MODEL)}}</h2>
                                    <a href="{{route(USER_ADDRESSES, [ID => encrypt(auth()->id())])}}" role="link" class="shipping-details-new-address d-flex align-items-center gap-2" target="_blank">
                                        <i class="ti ti-plus p-1 border rounded-circle"></i>
                                        <span class="text-decoration-underline">{{ucfirst(ADD)}} New {{ucfirst(ADDRESS_MODEL)}}</span>
                                    </a>
                                </article>
                                {{-- Shipping Details Addresses --}}
                                <article class="shipping-details-addresses pagination-container">
                                    @include(CHECKOUT_USER_ADDRESSES_PAGINATION, [USER_ADDRESSES => $user_addresses])
                                </article>

                                {{-- Shipping Payment --}}
                                <div class="shipping-payment-method mt-5 col-12">
                                    {{-- Shipping Payment Title --}}
                                    <h2 class="shipping-payment-title fs-5">Payment Method</h2>
                                    {{-- Shipping Payment Selection --}}
                                    <div class="select-payment mt-3 ps-4 pe-5 py-5 rounded">
                                        <label for="add_order_payment_method" class="payment-method-label position-relative d-flex align-items-center">
                                            <span class="fw-500">Cash on Delivery</span>
                                            <input type="radio" role="radio" name="add_order_payment_method" id="add_order_payment_method" class="position-absolute" checked="checked" value="cash on delivery" aria-required="true">
                                            <span class="custom-check position-absolute top-0 start-0" aria-labelledby="add_order_payment_method"></span>
                                        </label>
                                    </div>
                                </div>
                            </article>
                        </main>
                    </div>
                </section>

                <!----======= Right Side =======---->
                <aside role="region" class="right-side col-12 col-lg-5">
                    <div class="total-order-products py-6">
                        {{-- Order Products --}}
                        <ul role="list" class="order-products box-content row col-12 gap-4 pe-4 border-top border-bottom">
                            @foreach ($user_cart_items as $cart_item)
                                <li role="listitem" class="order-product-item row col-12 justify-content-between align-items-center">
                                    {{-- Order Product Image & Info --}}
                                    <article class="order-product-img-info col-9 d-flex align-items-center gap-3">
                                        {{-- Order Product Image --}}
                                        <div class="order-product-image position-relative rounded">
                                            <img src="{{imageSource($cart_item->{PRODUCT_MODEL}, MAIN_IMAGE)}}" alt="{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}">
                                            <span class="order-product-quantity position-absolute badge fw-500 rounded-pill">
                                                {{ $cart_item->{PRODUCT_QUANTITY} }}
                                            </span>
                                        </div>
                                        {{-- Order Product Info --}}
                                        <div class="order-product-info">
                                            <h2 class="order-product-name fw-500">{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}</h2>
                                            <p class="order-product-size">
                                                {{ucfirst(SIZE)}}: {{strtoupper($cart_item->selected_product_size)}}
                                            </p>
                                        </div>
                                    </article>
                                    {{-- Order Product Total Amount --}}
                                    <span class="order-product-total-amount col-3 d-flex justify-content-end align-items-center fw-500">
                                        @price(($cart_item->{PRODUCT_QUANTITY}) * ($cart_item->{PRODUCT_MODEL}->{NEW_PRICE}))
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        {{-- Order Total Summary --}}
                        <article class="order-total-summary">
                            <ul role="list" class="d-grid gap-2 pt-4 pb-3 border-bottom">
                                <li role="listitem" class="d-flex justify-content-between align-items-center">
                                    <span>Subtotal</span>
                                    <span class="fw-600">@price($total_cost)</span>
                                </li>
                                <li role="listitem" class="d-flex justify-content-between align-items-center">
                                    <span>Shipping</span>
                                    <span class="fw-600">Free</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center py-4">
                                <div>
                                    <h2 class="fs-6 fw-500">Total</h2>
                                    <p>(Including VAT)</p>
                                </div>
                                <span class="fs-5 fw-600">@price($total_cost)</span>
                            </div>
                        </article>
                        {{-- Place Order Button --}}
                        <article class="d-grid place-items-center">
                            <button type="submit" role="button" title="Place {{ucfirst(ORDER_MODEL)}}" id="place_order_btn" class="btn w-75 mt-3">Place {{ucfirst(ORDER_MODEL)}}</button>
                        </article>
                    </div>
                </aside>
            </form>
        </div>
    </section>

@endsection
