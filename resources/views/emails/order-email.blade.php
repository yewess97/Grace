@component('mail::message')

    {{-- Order User Name --}}
    <h2 class="mb-3 fs-5 fw-700 text-center">✨🎉 Congratulations {{$order_user_name}} 🎉✨</h2>

    {{-- Order Title --}}
    <ins class="text-center">
        <h3 class="fs-6 fw-600">Your {{$order_number_title}} Details:</h3>
    </ins>

    {{-- Order Details --}}
    <div class="col-12 mt-4 clearfix">
        {{-- Order Info --}}
        <div class="order-info">
            <div class="border rounded">
                <div class="px-5 py-4">
                    @foreach ($order_details as $title => $value)
                        <div class="order-details-info col-12 d-table py-3 border-top border-bottom">
                            <h2 class="d-table-cell vertical-center fs-6 fw-600">{{$title}}:</h2>
                            <div class="d-table-cell vertical-center text-right">{!! $value !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Order Products & Total Summary --}}
        <div class="order-details-summary">
            {{-- Order Products --}}
            <div class="border rounded">
                <div class="px-5 py-4">
                    @foreach ($order->{ORDER_ITEMS} as $order_item)
                        <div class="order-product-item col-12 d-table py-3">
                            {{-- Order Product Image & Info --}}
                            <div class="order-product-img-info d-table-cell vertical-center">
                                {{-- Order Product Image --}}
                                <div class="order-product-image me-2 rounded">
                                    <img src="{{imageSource($order_item, PRODUCT_MAIN_IMAGE)}}}}" alt="{{ $order_item->{PRODUCT_NAME} }}">
                                    <span class="order-product-quantity fw-500 text-center rounded-circle">
                                        {{ $order_item->{PRODUCT_QUANTITY} }}
                                    </span>
                                </div>
                                {{-- Order Product Info --}}
                                <div class="order-product-info">
                                    <h2 class="order-product-name fw-500">{{ $order_item->{PRODUCT_NAME} }}</h2>
                                    <p class="order-product-size">{{ucfirst(SIZE)}}: {{strtoupper($order_product_size($order_item))}}</p>
                                </div>
                            </div>
                            {{-- Order Product Total Amount --}}
                            <div class="order-product-total-amount d-table-cell vertical-center text-right">
                                <span class="fw-500">@priceFormat($order_item->{PRODUCT_TOTAL_PRICE})</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Order Total Summary --}}
            <div class="order-total-summary">
                <ul class="pt-4 pb-3 border-bottom">
                    <li class="col-12 d-table mb-2">
                        <span class="d-table-cell vertical-center">Subtotal</span>
                        <span class="d-table-cell vertical-center fw-600 text-right">@priceFormat($order->{ORDER_ITEMS}->sum(PRODUCT_TOTAL_PRICE))</span>
                    </li>
                    <li class="col-12 d-table">
                        <span class="d-table-cell vertical-center">Shipping</span>
                        <span class="d-table-cell vertical-center fw-600 text-right">Free</span>
                    </li>
                </ul>
                <div class="col-12 d-table mt-3">
                    <span class="d-table-cell vertical-center fs-6 fw-500">{{ucfirst(ORDER_MODEL)}} {{capitalizeAll(TOTAL_COST)}}</span>
                    <span class="d-table-cell vertical-center fs-7 fw-600 text-right">
                        @priceFormat($order->{ORDER_ITEMS}->sum(PRODUCT_TOTAL_PRICE))
                    </span>
                </div>
            </div>
        </div>
    </div>

@endcomponent
