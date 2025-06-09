@extends(key(viewLayoutTitle(USER_MODEL)), [TITLE => $products_list_title])

@section('content')
    {{-- Products Main --}}
    <main role="main" class="products-main py-6">
        <div class="container">
            {{-- Errors --}}
            <section class="filter-products-errors row mb-3">
                {{$filter_products_error(CATEGORIES_TABLE)}}
                {{$filter_products_error(SUBCATEGORIES_TABLE)}}
                {{$filter_products_error(SIZES)}}
                {{$filter_products_error(MIN_PRICE)}}
                {{$filter_products_error(MAX_PRICE)}}
                {{$filter_products_error(SORT)}}
            </section>
            {{-- Main Sides --}}
            <div class="main-sides row flex-lg-nowrap col-12">
                {{-- Products Filter Responsive Nav Menu --}}
                <article class="products-filter-nav-menu col d-none">
                    {{-- Products Filter Nav Menu Toggler --}}
                    <div role="button" class="nav-menu-toggler d-flex align-items-center gap-2 fs-5 text-black" tabindex="0" aria-label="Open filtration menu" aria-expanded="false" aria-controls="products_filter_menu">
                        <i class="fa-solid fa-list-ul"></i>
                        <h2 class="fs-5 fw-500">{{ucfirst(FILTER)}}</h2>
                    </div>

                    {{-- Products Filter Nav Menu Content --}}
                    <nav role="navigation" id="products_filter_menu" class="nav-menu position-fixed top-0 h-100 overflow-auto bg-white">
                        <div class="container">
                            <div class="nav-menu-content row">
                                {{-- Nav Menu Header --}}
                                <header role="banner" class="nav-menu-header nav-offer position-relative">
                                    {{-- Close Button --}}
                                    @menuCloseBtn("products_filter_menu")

                                    {{-- Offers Sales --}}
                                    <x-offers-sales :common_collections="$common_collections"/>
                                </header>

                                {{-- Nav Menu Main Body --}}
                                <main role="main" class="nav-menu-main">
                                    <div class="container">
                                        <div class="row left-side products-filter-form-menu">
                                            <x-products-filter-form class="px-2 py-3" :common_collections="$common_collections" :product_sizes="$product_sizes" :products_prices="$products_prices"/>
                                        </div>
                                    </div>
                                </main>
                            </div>
                        </div>
                    </nav>
                </article>


                <!----======= Left Side =======---->
                <aside class="left-side products-filter-form d-lg-block d-none col-lg-3 pe-lg-3">
                    <x-products-filter-form :common_collections="$common_collections" :product_sizes="$product_sizes" :products_prices="$products_prices"/>
                </aside>

                <!----======= Right Side =======---->
                <section class="right-side col-lg-9 ps-lg-3">
                    {{-- Products List Title --}}
                    <h1 class="fs-5 fw-600 text-uppercase">{{$products_list_title}}</h1>

                    {{-- Products View & Sort --}}
                    <div class="products-view-sort" data-grid-main-view="4">
                        <div class="box-content rounded-start mt-4">
                            <div class="d-flex justify-content-between align-items-center gap-4">
                                {{-- Products View --}}
                                <article class="products-view d-flex align-items-center gap-3 fs-8">
                                    <i class="ti ti-layout-grid3 grid cursor-pointer" data-grid-view="4"></i>
                                    <i class="ti ti-layout-list-thumb grid cursor-pointer" data-grid-view="1"></i>
                                </article>
                                {{-- Products Sort --}}
                                <form action="{{route(FILTER_PRODUCTS)}}" method="post" role="form" id="filter_products_sort_form" class="filter-products-sort-form grace-form d-flex justify-content-end" data-no_results="{{imageSource('no-results.png')}}">
                                    @csrf
                                    <div class="grace-form-body">
                                        <div class="form-group position-relative d-flex align-items-center gap-3">
                                            <label for="filter_products_sort" class="fw-600">{{ucfirst(SORT)}} By</label>
                                            <select name="filter_products_sort" id="filter_products_sort" class="form-select col py-2">
                                                <option disabled hidden selected>Select {{ucfirst(FILTER)}}</option>
                                                @foreach(SORT_PRODUCTS_ENUM as $sort_type => $sort_value)
                                                    <option value="{{$sort_value}}">{{$sort_type}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Products --}}
                    <div class="products pagination-container row gap-4">
                        @include(USER_PRODUCTS_PAGINATION, [PRODUCTS_TABLE => $products])
                    </div>
                </section>
            </div>
        </div>
    </main>

@endsection
