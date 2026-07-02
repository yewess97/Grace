<aside class="left-side row col-12 col-lg-3 gap-4 pe-lg-3">
    {{-- Categories Menu --}}
    <ul role="list" class="categories-menu position-relative bg-white rounded-start">
        <li role="listitem" class="category-item main-dropdown">
            @include(TOP_BOTTOM_WEARS_PARTIAL, ['wear_type' => 'top_wear', 'badge' => 'New'])

            {{-- Main Top Wear List Content --}}
            <ul role="menu" class="dropdown-content box-content position-absolute start-100 d-flex justify-content-around align-items-center gap-2 border rounded-end">
                {{-- Top Wear List --}}
                @include(TOP_BOTTOM_WEARS_PARTIAL, ['wear_type' => 'top_wear'])

                <li role="menuitem" class="dropdown-item">
                    <div class="top-wear-dropdown-image position-relative img-hover-effect cursor-pointer">
                        <img src="{{imageSource('site_banners/banner1.png')}}" alt="Banner 1">
                    </div>
                </li>
            </ul>
        </li>

        {{-- Accessories --}}
        <li role="listitem" class="category-item row">
            <a href="{{route(SUBCATEGORY_MODEL, 'accessories')}}" role="link" class="category-title">Accessories</a>
        </li>

        {{-- Bottom Wear List --}}
        <li role="listitem" class="category-item main-dropdown">
            @include(TOP_BOTTOM_WEARS_PARTIAL, ['wear_type' => 'bottom_wear', 'badge' => 'Sale'])

            <ul role="list" class="dropdown-content box-content position-absolute start-100 row justify-content-around align-items-center gap-2 border rounded-end">
                <li role="listitem">
                    <ul role="menu" class="d-flex align-items-center">
                        @include(TOP_BOTTOM_WEARS_PARTIAL, ['wear_type' => 'bottom_wear'])
                    </ul>
                    <ul role="list" class="d-flex align-items-center">
                        <li role="listitem" class="dropdown-item dropdown-item-image pe-2">
                            <div class="bottom-wear-dropdown-image position-relative img-hover-effect cursor-pointer">
                                <img src="{{imageSource('site_banners/banner2.png')}}" alt="Banner 2">
                            </div>
                        </li>
                        <li role="listitem" class="dropdown-item dropdown-item-image ps-2">
                            <div class="bottom-wear-dropdown-image position-relative img-hover-effect">
                                <img src="{{imageSource('site_banners/banner3.png')}}" alt="Banner 3">
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        {{-- Bags --}}
        <li role="listitem" class="category-item row">
            <a href="{{route(SUBCATEGORY_MODEL, 'bags')}}" role="link" class="category-title">Bags</a>
        </li>

        {{-- Categories --}}
        @foreach ($common_collections[CATEGORIES_TABLE] as $category)
            <li role="listitem" class="category-item row">
                <a href="{{route(CATEGORY_MODEL, $category->{SLUG})}}" role="link" class="main-category-title">
                    {{ucfirst($category->{NAME})}}
                </a>
            </li>
        @endforeach
    </ul>
    {{-- Banner --}}
    <article class="banner">
        <div class="box-content rounded-start">
            <a href="{{route(FILTER_PRODUCTS, [CATEGORIES_TABLE => 'women', SUBCATEGORIES_TABLE => 'sweaters-shirts'])}}" role="link" class="position-relative d-block img-hover-effect">
                <img src="{{imageSource('site_banners/banner4.png')}}" alt="Banner 4">
            </a>
        </div>
    </article>
    {{-- New Products --}}
    <article class="new-products">
        <ul role="list" class="new-products-content box-content rounded-start">
            {{-- New Products Title --}}
            <li role="listitem" class="box-title mb-3 border-bottom">
                <h2 class="fs-6 fw-500">
                    <span class="position-relative">{{capitalizeAll(NEW_PRODUCTS)}}</span>
                </h2>
            </li>
            {{-- New Products Content --}}
            @foreach ($common_collections[NEW_PRODUCTS] as $new_product)
                <li role="listitem" class="new-product-item">
                    <a href="{{route(PRODUCT_DETAILS, $new_product->{SLUG})}}" role="link" class="new-product row align-items-center">
                        <article class="new-product-img col-2 rounded-2">
                            <img src="{{imageSource($new_product, MAIN_IMAGE)}}" alt="{{ $new_product->{NAME} }}" class="w-100">
                        </article>
                        <article class="new-product-info row col gap-2">
                            <h3>{{capitalizeFirst($new_product->{NAME})}}</h3>
                            <div class="price-info row gap-1">
                                <div class="new-price-discount d-flex align-items-center">
                                    <span class="new-price">@priceFormat($new_product->new_price)</span>
                                    @oldprice ($new_product->old_price, $new_product->new_price)
                                        <span class="discount-percent text-white">@discount($new_product->new_price, $new_product->old_price)</span>
                                    @endoldprice
                                </div>
                                @oldprice ($new_product->old_price, $new_product->new_price)
                                    <s class="old-price fs-7">@priceFormat($new_product->old_price)</s>
                                @endoldprice
                            </div>
                        </article>
                    </a>
                </li>
            @endforeach
        </ul>
    </article>
    {{-- Customers Reviews --}}
    <article class="customers-reviews">
        <div class="customers-reviews-content box-content rounded-start">
             {{-- Customers Reviews Title --}}
            <article class="box-title mb-3 border-bottom">
                <h2 class="fs-6 fw-500">
                    <span class="position-relative">{{capitalizeAll(CUSTOMERS_REVIEWS)}}</span>
                </h2>
            </article>
            {{-- Customers Reviews Content --}}
            <article class="customers-reviews-carousel owl-carousel owl-theme owl-loaded owl-drag">
                <div class="owl-stage-outer">
                    <div class="owl-stage">
                        @if ($common_left_side[CUSTOMERS_REVIEWS]->isNotEmpty())
                            @foreach ($common_left_side[CUSTOMERS_REVIEWS] as $customer_review)
                                <div class="owl-item">
                                    <div class="row gap-2 text-center">
                                        <div class="customer-review border overflow-hidden">
                                            <i class="fa-solid fa-quote-left d-block fs-9"></i>
                                            <p>{{ $customer_review->{BODY_TEXT} }}</p>
                                        </div>
                                        <div class="customer-name">
                                            <h2 class="mt-2 fw-500 lh-base text-main">{{ $customer_review->{USER_MODEL}->{FULL_NAME} }}</h2>
                                            <p class="mt-2">
                                                @include(REVIEW_RATING_PARTIAL, [RATING => $customer_review->{RATING}])
                                            </p>
                                            <p class="mt-2 fs-7 lh-base">
                                                Reviewed on the "<b>{{ $customer_review->{PRODUCT_MODEL}->{NAME} }}</b>" {{ucfirst(PRODUCT_MODEL)}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="owl-item">
                                <div class="customer-review border text-center overflow-hidden">
                                    <i class="fa-solid fa-quote-left d-block fs-9"></i>
                                    <p>No {{ucfirst(pluralize(REVIEWS_TABLE))}} Yet!</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        </div>
    </article>
    {{-- Banner --}}
    <article class="banner">
        <div class="box-content rounded-start">
            <a href="{{route(FILTER_PRODUCTS, [SUBCATEGORIES_TABLE => 'shoes'])}}" role="link" class="position-relative d-block img-hover-effect">
                <img src="{{imageSource('site_banners/banner5.png')}}" alt="Banner 5">
            </a>
        </div>
    </article>
    {{-- NewsLetter --}}
    <article class="newsletter">
        <div class="newsletter-container d-grid place-items-center text-center gap-3 bg-white rounded-start">
            <img src="{{imageSource('email-icon.png')}}" alt="Newsletter">
            <h2 class="fs-6 fw-600 text-capitalize lh-1">Our newsletter</h2>
            <p>Subscribe with us to receive the latest offers and updates</p>
            <form method="post" role="form" id="subscribe-form">
                <label for="subscribe_email" class="w-100">
                    <input type="email" name="subscribe_email" id="subscribe_email" class="w-100 text-center" placeholder="Enter Your Email" required="required" aria-required="true">
                </label>
                <button type="submit" role="button" title="Subscribe" class="btn mt-3">Subscribe</button>
            </form>
        </div>
    </article>
</aside>
