@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Home Main --}}
    <main role="main" class="home-product-main">
        <div class="container">
            <div class="main-sides row col-12">
                @if (session()->has('checkoutError'))
                    @customSession("checkoutError", "danger", "times")
                @endif
                <!----======= Left Side =======---->
                @include(HOME_PRODUCT_LEFT_SIDE_COMPONENT)

                <!----======= Home Right Side =======---->
                <section class="right-side home-right-side col-12 col-lg-9 d-flex flex-column gap-4 ps-lg-3">
                    {{-- Carousel --}}
                    <article class="home-carousel owl-carousel owl-theme owl-loaded owl-drag">
                        <div class="owl-stage-outer">
                            <div class="owl-stage">
                                @foreach (Storage::files('public/images/carousels') as $carousel_image)
                                    <div class="owl-item">
                                        <div class="home-carousel-img">
                                            <img src="{{asset(Storage::url($carousel_image))}}" alt="{{config('app.name')}} Slider">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>
                    {{-- Products --}}
                    <article class="home-products">
                        <div class="box-content rounded-start">
                            {{-- Products Title & Link --}}
                            <article class="box-title d-flex justify-content-between align-items-center mb-3 border-bottom">
                                {{-- Products Title --}}
                                <h2 class="fs-5 fw-600 text-uppercase">
                                    <span class="position-relative">Most Selling</span>
                                </h2>
                                {{-- Products Link --}}
                                <a href="{{route(PRODUCTS_LIST)}}" role="link" class="text-decoration-underline">All {{ucfirst(PRODUCTS_TABLE)}}</a>
                            </article>
                            {{-- Products Container --}}
                            <article class="products-container pagination-container">
                                @include(USER_PRODUCTS_PAGINATION, [PRODUCTS_TABLE => $products])
                            </article>
                        </div>
                    </article>
                    {{-- Categories Banners --}}
                    <article class="home-banners">
                        <div class="box-content row row-cols-1 row-cols-md-3 m-0 rounded-start">
                            @foreach ($common_collections[CATEGORIES_TABLE] as $category)
                                <a href="{{route(CATEGORY_MODEL, $category->slug)}}" role="link" class="col position-relative img-hover-effect">
                                    <img src="{{imageSource($category, BANNER_IMAGE)}}" alt="Category Banner" class="rounded-2">
                                </a>
                            @endforeach
                        </div>
                    </article>
                    {{-- Our Services --}}
                    <article class="home-services">
                        <div class="box-content rounded-start">
                             {{-- Our Services Title --}}
                            <article class="box-title mb-3 border-bottom">
                                <h2 class="fs-5 fw-600 text-uppercase">
                                    <span class="position-relative">Our services</span>
                                </h2>
                            </article>
                             {{-- Our Services Content --}}
                            <article class="main-services d-flex justify-content-between overflow-auto">
                                @foreach($services as $service)
                                    <div class="service d-flex align-items-center w-25">
                                        <article class="service-icon">
                                            <img src="{{imageSource("services/service-icon{$service->{MAIN_IMAGE} }.png")}}" alt="{{config('app.name')}} Service">
                                        </article>
                                        <article class="service-info overflow-hidden">
                                            <h6 class="fs-6 fw-600 lh-1 text-capitalize">{{ $service->{TITLE} }}</h6>
                                            <p class="text-capitalize overflow-hidden">{{ $service->{SHORT_DESCRIPTION} }}</p>
                                        </article>
                                    </div>
                                @endforeach
                            </article>
                        </div>
                    </article>
                    {{-- Our Partners --}}
                    <article class="home-partners">
                        <div class="box-content rounded-start">
                             {{-- Our Partners Title --}}
                            <article class="box-title mb-3 border-bottom">
                                <h2 class="fs-5 fw-600 text-uppercase">
                                    <span class="position-relative">Our partners</span>
                                </h2>
                            </article>
                             {{-- Our Partners Content --}}
                            <article class="partners-carousel owl-carousel owl-theme owl-loaded owl-drag">
                                <div class="owl-stage-outer">
                                    <div class="owl-stage">
                                        @foreach (Storage::files('public/images/partners') as $partner)
                                            <div class="owl-item partner">
                                                <div class="partner-img mt-2 cursor-pointer">
                                                    <img src="{{asset(Storage::url($partner))}}" alt="Our Partner">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        </div>
                    </article>
                </section>
            </div>
        </div>
    </main>

@endsection
