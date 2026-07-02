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
                                    <x-home.offers-sales :common_collections="$common_collections"/>
                                </header>

                                {{-- Nav Menu Main Body --}}
                                <main role="main" class="nav-menu-main">
                                    <div class="container">
                                        <div class="row left-side products-filter-form-menu"></div>
                                    </div>
                                </main>
                            </div>
                        </div>
                    </nav>
                </article>


                <!----======= Left Side =======---->
                <aside class="left-side products-filter-form d-none d-lg-block col-lg-3 pe-lg-3">
                    <form action="{{route(FILTER_PRODUCTS)}}" method="post" role="form" id="filter_products_form" enctype="multipart/form-data" data-no_results="{{imageSource('no-results.png')}}">
                        @csrf
                        {{-- Filters Titles --}}
                        <article class="box-filter d-flex justify-content-between align-items-center border bg-white rounded-start">
                            {{-- Filter Label --}}
                            <h4 class="filter-title fw-600 rounded">{{ucfirst(FILTER)}} By:</h4>
                            {{-- Clear All Selections --}}
                            <button type="button" role="button" title="Clear All {{ucfirst(pluralize(FILTER))}}" class="clear-filter px-3 bg-transparent border-0">Clear All</button>
                        </article>

                        {{-- Filter By Categories --}}
                        <article class="box-filter border bg-white rounded-start">
                            {{-- Filter Label --}}
                            <h4 class="filter-title fw-600 rounded">{{capitalizeAll(FILTER.' by '.CATEGORIES_TABLE)}}</h4>
                            {{-- Filter Content --}}
                            <ul role="list" class="filter-content">
                                @foreach ($common_collections[CATEGORIES_TABLE] as $category)
                                    @php $category_name = str($category->{NAME})->lower()->replace(' ', '_') @endphp

                                    <li role="listitem" class="filter-item d-flex justify-content-between align-items-center">
                                        <label for="filter_products_categories_{{$category->id}}" class="filter-check position-relative d-flex align-items-center user-select-none cursor-pointer">
                                            <input type="checkbox" role="checkbox" name="filter_products_categories[]" id="filter_products_categories_{{$category->id}}" class="filter-checkbox" multiple="multiple" value="{{$category->id}}" aria-labelledby="filter_categories_{{$category_name}}">
                                            <span role="checkbox" class="custom-check position-absolute start-0" aria-labelledby="filter_categories_{{$category_name}}" aria-checked="mixed"></span>
                                            <span id="filter_categories_{{$category_name}}" class="filter-name text-capitalize">
                                                {{ucfirst($category->{NAME})}}
                                            </span>
                                        </label>
                                        <span class="count">({{$category->{PRODUCTS_TABLE}->count()}})</span>
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="filter_products_categories[]">
                        </article>

                        {{-- Filter By Collections (Subcategories) --}}
                        <article class="box-filter border bg-white rounded-start">
                            {{-- Filter Label --}}
                            <h4 class="filter-title fw-600 rounded">{{ucfirst(FILTER)}} By Collections</h4>
                            {{-- Filter Content --}}
                            <ul role="list" class="filter-content">
                                @foreach ($common_collections[SUBCATEGORIES_TABLE] as $subcategory)
                                    @php $collection_name = str($subcategory->{NAME})->lower()->replace(' ', '_') @endphp

                                    <li role="listitem" class="filter-item d-flex justify-content-between align-items-center">
                                        <label for="filter_products_subcategories_{{$subcategory->id}}" class="filter-check position-relative d-flex justify-content-center align-items-center user-select-none cursor-pointer">
                                            <input type="checkbox" role="checkbox" name="filter_products_subcategories[]" id="filter_products_subcategories_{{$subcategory->id}}" class="filter-checkbox" multiple="multiple" value="{{$subcategory->id}}" aria-labelledby="filter_subcategories_{{$collection_name}}">
                                            <span role="checkbox" class="custom-check position-absolute start-0" aria-labelledby="filter_subcategories_{{$collection_name}}" aria-checked="mixed"></span>
                                            <span id="filter_subcategories_{{$collection_name}}" class="filter-name text-capitalize">
                                                {{ucfirst($subcategory->{NAME})}}
                                            </span>
                                        </label>
                                        <span class="count">({{$subcategory->{PRODUCTS_TABLE}->count()}})</span>
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="filter_products_subcategories[]">
                        </article>

                        {{-- Filter By Sizes --}}
                        <article class="box-filter border bg-white rounded-start">
                            {{-- Filter Label --}}
                            <h4 class="filter-title fw-600 rounded">{{capitalizeAll(FILTER.' by '.SIZES)}}</h4>
                            {{-- Filter Content --}}
                            <ul role="list" class="filter-content">
                                @foreach ($product_sizes as $product_size)
                                    <li role="listitem" class="filter-item d-flex justify-content-between align-items-center">
                                        <label for="filter_products_sizes_{{$product_size->size_value}}" class="filter-check position-relative d-flex justify-content-center align-items-center user-select-none cursor-pointer">
                                            <input type="checkbox" role="checkbox" name="filter_products_sizes[]" id="filter_products_sizes_{{$product_size->size_value}}" class="filter-checkbox" multiple="multiple" value="{{$product_size->size_value}}" aria-labelledby="filter_sizes_{{ $product_size->{SIZE} }}">
                                            <span role="checkbox" class="custom-check position-absolute start-0" aria-labelledby="filter_sizes_{{ $product_size->{SIZE} }}" aria-checked="mixed"></span>
                                            <span id="filter_sizes_{{ $product_size->{SIZE} }}" class="filter-name text-capitalize">
                                                {{ $product_size->{SIZE} }}
                                            </span>
                                        </label>
                                        <span class="count">({{$product_size->products_count}})</span>
                                    </li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="filter_products_sizes[]">
                        </article>

                        {{-- Filter By Price --}}
                        <article class="box-filter position-relative bg-white border rounded-start">
                            {{-- Filter Label --}}
                            <h4 class="filter-title fw-600 rounded">{{capitalizeAll(FILTER.' by '.PRICE)}}</h4>
                            {{-- Filter Content --}}
                            <div class="filter-content d-grid gap-4">
                                {{-- Price Inputs --}}
                                <article class="filter-price-content d-flex align-items-center gap-1 w-100">
                                    <div class="price-input d-flex align-items-center w-100">
                                        <span>Min</span>
                                        <label for="min_price" class="w-100">
                                            <input type="number" title="Minimum price" name="filter_products_min_price" id="min_price" class="input-min w-100 p-0 text-center border" value="{{ $products_prices->{MIN_PRICE} }}">
                                        </label>
                                    </div>
                                    <span class="separator d-flex justify-content-center align-items-center fs-9">-</span>
                                    <div class="price-input d-flex align-items-center w-100">
                                        <span>Max</span>
                                        <label for="max_price" class="w-100">
                                            <input type="number" title="Maximum price" name="filter_products_max_price" id="max_price" class="input-max w-100 p-0 text-center border" value="{{ $products_prices->{MAX_PRICE} }}">
                                        </label>
                                    </div>
                                </article>
                                {{-- Price Range Slider --}}
                                <article class="filter-range-content position-relative">
                                    <div class="price-range">
                                        <label for="min_range">
                                            <input type="range" title="Minimum range" id="min_range" class="position-absolute start-0 bottom-0 w-100 p-0 border-0" min="{{ $products_prices->{MIN_PRICE} }}" max="{{ $products_prices->{MAX_PRICE} }}" value="{{ $products_prices->{MIN_PRICE} }}">
                                        </label>
                                    </div>
                                    <div class="price-range">
                                        <label for="max_range">
                                            <input type="range" title="Maximum range" id="max_range" class="position-absolute start-0 bottom-0 w-100 p-0 border-0" min="{{ $products_prices->{MIN_PRICE} }}" max="{{ $products_prices->{MAX_PRICE} }}" value="{{ $products_prices->{MAX_PRICE} }}">
                                        </label>
                                    </div>
                                </article>
                            </div>
                        </article>

                        {{-- Filter Button --}}
                        <article class="box-filter">
                            <button type="submit" role="button" title="{{ucfirst(FILTER)}}" class="btn btn-block">{{ucfirst(FILTER)}}</button>
                        </article>
                    </form>
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
                                <div class="filter-products-sort-form position-relative d-flex align-items-center gap-3">
                                    <label for="filter_products_sort" class="fw-600">{{ucfirst(SORT)}} By</label>
                                    <select name="filter_products_sort" id="filter_products_sort" class="form-select col py-2">
                                        <option disabled hidden selected>Select {{ucfirst(FILTER)}}</option>
                                        @foreach (SORT_PRODUCTS_ENUM as $sort_type => $sort_value)
                                            <option value="{{$sort_value}}">{{$sort_type}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="filter_products_sort">
                                </div>
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
