@extends(key(viewLayoutTitle(isAdminRoute(true))), [TITLE => $order_number_title])

@section('content')

    {{-- Order Details Main --}}
    <main role="main" class="order-details-main {{isAdminRoute() ? 'main-body' : 'py-6'}}">
        <div class="container">
            <div class="main-sides row col-12">
                @unless(isAdminRoute())
                    <div class="d-flex align-items-baseline gap-3 mb-lg-3">
                        @backTo(PROFILE)
                        <h2 class="title mb-3 fs-5 fw-600">{{$order_number_title}} Details</h2>
                    </div>
                @endunless

                <!----======= Left Side =======---->
                <section class="left-side col-12 col-lg-7">
                    <div class="box-content border rounded">
                        @if(isAdminRoute())
                            @backTo(ORDERS_TABLE, ADMIN_ORDERS_ROUTE, [STATUS => $order->{STATUS}])
                        @endif
                        {{-- Order Details Info --}}
                        <div class="order-details-info-wrapper px-4 px-lg-5 py-4">
                            @foreach($order_details as $title => $slot)
                                <article class="order-details-info d-flex justify-content-between align-items-center border-top border-bottom">
                                    <h2 class="fs-6 fw-600">{{$title}}:</h2>
                                    {!! $slot !!}
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>

                <!----======= Right Side =======---->
                <aside role="region" class="right-side col-12 col-lg-4">
                    <div class="total-order-products p-0">
                        {{-- Order Products --}}
                        <ul role="list" class="order-products box-content row col-12 gap-3 pe-4 border-top border-bottom rounded">
                            @foreach ($order->{ORDER_ITEMS} as $order_item)
                                <li role="listitem" class="order-product-item row col-12 justify-content-between align-items-center">
                                    {{-- Order Product Image & Info --}}
                                    <article class="order-product-img-info col-9 d-flex align-items-center gap-3">
                                        {{-- Order Product Image --}}
                                        <div class="order-product-image position-relative me-2 rounded">
                                            <img src="{{imageSource($order_item, PRODUCT_MAIN_IMAGE)}}" alt="{{ $order_item->{PRODUCT_NAME} }}">
                                            <span class="order-product-quantity position-absolute badge fw-500 rounded-pill">
                                                {{ $order_item->{PRODUCT_QUANTITY} }}
                                            </span>
                                        </div>
                                        {{-- Order Product Info --}}
                                        <div class="order-product-info">
                                            <h2 class="order-product-name fw-500">{{ $order_item->{PRODUCT_NAME} }}</h2>
                                            <p class="order-product-size">{{ucfirst(SIZE)}}: {{strtoupper($order_product_size($order_item))}}</p>
                                        </div>
                                    </article>
                                    {{-- Order Product Total Amount --}}
                                    <span class="order-product-total-amount col-3 d-flex justify-content-end fw-500">
                                        @priceFormat($order_item->{PRODUCT_TOTAL_PRICE})
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        {{-- Order Total Summary --}}
                        <article class="order-total-summary">
                            <ul role="list" class="d-grid gap-2 pt-4 pb-3 border-bottom">
                                <li role="listitem" class="d-flex justify-content-between align-items-center">
                                    <span>Subtotal</span>
                                    <span class="fw-600">@priceFormat($order->{ORDER_ITEMS}->sum(PRODUCT_TOTAL_PRICE))</span>
                                </li>
                                <li role="listitem" class="d-flex justify-content-between align-items-center">
                                    <span>Shipping</span>
                                    <span class="fw-600">Free</span>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center py-4">
                                <span class="fs-6 fw-500">{{ucfirst(ORDER_MODEL)}} {{capitalizeAll(TOTAL_COST)}}</span>
                                <span class="fs-5 fw-600">@priceFormat($order->{ORDER_ITEMS}->sum(PRODUCT_TOTAL_PRICE))</span>
                            </div>
                        </article>
                    </div>
                </aside>
            </div>
        </div>
    </main>

@endsection
