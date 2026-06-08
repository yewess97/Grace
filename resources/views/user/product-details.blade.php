@extends(key(viewLayoutTitle(USER_MODEL)), [TITLE => ucfirst($product->{NAME})])

@section('content')

    {{-- Product Details Main --}}
    <main role="main" class="home-product-main">
        <div class="container">
            <div class="main-sides row flex-lg-nowrap col-12">
                <!----======= Left Side =======---->
                @include(HOME_PRODUCT_LEFT_SIDE_COMPONENT)

                <!----======= Home Right Side =======---->
                <section class="right-side product-right-side col-lg-9 d-flex flex-column gap-4 ps-lg-3">
                    {{-- Product Images and Info --}}
                    <form action="{{route(CREATE_UPDATE_CART, ADD)}}" method="post" role="form" class="add-cart-form grace-form" data-loading_spinner="{{imageSource('loading.png')}}">
                        @csrf
                        <div class="grace-form-body box-content product-imgs-info row row-cols-1 row-cols-lg-2 gap-0 px-0 rounded-start">
                            <input type="hidden" name="add_cart_product_id" value="{{$product->id}}">
                            {{-- Product Images --}}
                            <article class="product-images row col">
                                {{-- Product Main Image --}}
                                <article class="product-main-image d-grid place-items-center bg-image hover-zoom">
                                    <img src="{{imageSource($product, MAIN_IMAGE)}}" alt="{{ $product->{NAME} }}">
                                </article>
                                {{-- Product Thumb Images --}}
                                @if ($product->{THUMB_IMAGES}->count())
                                    <article class="product-thumb-images-carousel position-relative owl-carousel owl-theme owl-loaded owl-drag">
                                        <div class="owl-stage-outer">
                                            <ul role="list" class="owl-stage">
                                                @foreach ($product->{THUMB_IMAGES} as $thumb_image)
                                                    <li role="listitem" class="product-thumb-image owl-item cursor-pointer">
                                                        <img src="{{imageSource($thumb_image, THUMB_IMAGE)}}" alt="{{ $product->{NAME} }}">
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </article>
                                @endif
                            </article>
                            {{-- Product Info --}}
                            <article class="product-info col d-flex flex-column pt-2 px-3">
                                {{-- Product Title --}}
                                <h2 class="product-title fs-8 fw-600 text-capitalize">{{ $product->{NAME} }}</h2>
                                {{-- Product Availability --}}
                                <h2 class="product-availability fw-600">
                                    <span>Availability:</span>
                                    <span class="ms-3 text-main">{{$product->{STATUS} === 0 ? 'Empty From Stock' : 'In Stock'}}</span>
                                </h2>
                                {{-- Product Price --}}
                                <div class="product-price d-flex align-items-center">
                                    <span class="new-price fs-6 fw-600 lh-1">@priceFormat($product->new_price)</span>
                                    @oldprice ($product->old_price, $product->new_price)
                                    <span class="old-price fs-7 text-decoration-line-through lh-1">@priceFormat($product->old_price)</span>
                                    <span class="discount-percent fw-500 lh-1 text-white">
                                        @discount($product->new_price, $product->old_price)
                                    </span>
                                    @endoldprice
                                </div>
                                {{-- Product Short-Description --}}
                                <p class="product-short-desc">{!! $product->{SHORT_DESCRIPTION} !!}</p>
                                {{-- Product Add To Cart Form --}}
                                <div class="product-info-btns d-grid gap-4">
                                    {{-- Select Size --}}
                                    <div class="add-cart-product-sizes col-lg-8 col-md-5 col-sm-6">
                                        <div class="form-group position-relative">
                                            <label for="add_cart_product_size" class="label-select position-absolute user-select-none pe-none">
                                                <sup class="me-1">*</sup>{{ucfirst(SIZES)}}
                                            </label>
                                            <select name="add_cart_product_size[]" id="add_cart_product_size" class="product-sizes" multiple="multiple" aria-required="true">
                                                @foreach (productSizes($product, true) as $size => $value)
                                                    <option value="{{$value}}">{{$size}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="add_cart_product_size[]">
                                        </div>
                                        {{$add_cart_product_error(PRODUCT_SIZE)}}
                                    </div>
                                    {{-- Select Quantity --}}
                                    <div class="cart-product-quantity">
                                        <div class="form-group d-flex align-items-center">
                                            <x-products.quantity id="add_cart_product_quantity"/>
                                        </div>
                                        {{$add_cart_product_error(PRODUCT_QUANTITY)}}
                                    </div>
                                    @if ($product->{STATUS} === 1)
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- Add To/Remove From Wishlist Button --}}
                                            @submitButton(WISHLIST_MODEL, $product->id)
                                            {{-- Add To Cart Button --}}
                                            @submitButton(ADD_TO_CART)
                                        </div>
                                    @endif
                                </div>
                            </article>
                        </div>
                    </form>
                    {{-- Add or Remove Wishlist Form --}}
                    <x-wishlist-cart.add-remove-wishlist-form product_id="{{$product->id}}" />
                    {{-- Product Long-Description & Reviews --}}
                    <section class="box-content product-details-reviews">
                        <ul role="tablist" class="nav nav-tabs d-flex justify-content-center align-items-center mb-1">
                            {{-- Product Long-Description --}}
                            <li role="presentation" class="nav-item">
                                <a href="#product_desc_panel" role="tab" id="product_desc" class="nav-link product-details-reviews-title fw-500 text-capitalize text-center active" data-mdb-toggle="tab" aria-controls="product_desc_panel" aria-selected="true">Description</a>
                            </li>
                            {{-- Product Reviews --}}
                            <li role="presentation" class="nav-item">
                                <a href="#product_reviews_panel" role="tab" id="product_reviews" class="nav-link product-details-reviews-title fw-500 text-capitalize text-center" data-mdb-toggle="tab" aria-controls="product_reviews_panel" aria-selected="false">{{ucfirst(REVIEWS_TABLE)}}</a>
                            </li>
                        </ul>
                        {{-- Product Long-Description & Reviews Content --}}
                        <div class="tab-content product-details-reviews-content border rounded">
                            {{-- Product Long-Description Content --}}
                            <div role="tabpanel" id="product_desc_panel" class="tab-pane description fade show active" aria-labelledby="product_desc">
                                <h2 class="fs-6 fw-600 text-capitalize">More details</h2>
                                <div class="more-details-desc text-break">
                                    {!! $product->{LONG_DESCRIPTION} !!}
                                </div>
                            </div>
                            {{-- Product Reviews Content --}}
                            <div role="tabpanel" id="product_reviews_panel" class="tab-pane reviews fade" aria-labelledby="product_reviews">
                                @include(REVIEWS_COMPONENT)
                            </div>
                        </div>
                    </section>
                    {{-- Related Products --}}
                    @if ($product->{RELATED_PRODUCTS}->count())
                        <section class="related-products box-content">
                            {{-- Related Products Title --}}
                            <article class="box-title mb-3 border-bottom">
                                <h2 class="fs-5 fw-600 text-uppercase">
                                    <span class="position-relative">{{capitalizeAll(RELATED_PRODUCTS)}}</span>
                                </h2>
                            </article>
                            {{-- Related Products Content --}}
                            <article class="related-products-carousel owl-carousel owl-theme owl-loaded owl-drag">
                                <div class="owl-stage-outer">
                                    <ul role="list" class="owl-stage products-content">
                                        @foreach ($product->{RELATED_PRODUCTS} as $related_product)
                                            <li role="listitem" class="owl-item product-item position-relative p-0">
                                                <x-products.item :product="$related_product"/>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </article>
                        </section>
                    @endif
                </section>
            </div>
        </div>
    </main>


    {{-- Quick View Product Modal --}}
    @include(USER_QUICK_VIEW_PRODUCT_MODAL)

    {{-- Product Edit Review Modal --}}
    @include(USER_EDIT_REVIEW_MODAL)

@endsection
