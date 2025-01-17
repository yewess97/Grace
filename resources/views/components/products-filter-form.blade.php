@props(['class', COMMON_COLLECTIONS, PRODUCT_SIZES_TABLE, PRODUCTS_PRICES])

<form action="{{route(FILTER_PRODUCTS)}}" method="post" role="form" id="filter_products_form" class="{{$class ?? null}}" enctype="multipart/form-data" data-no_results="{{imageSource('no-results.png')}}">
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
                    <label for="filter_products_categories_{{$category->id}}" class="filter-check position-relative d-flex justify-content-center align-items-center user-select-none">
                        <input type="checkbox" role="checkbox" name="filter_products_categories[]" id="filter_products_categories_{{$category->id}}" class="filter-checkbox" multiple="multiple" value="{{$category->id}}" aria-labelledby="filter_categories_{{$category_name}}">
                        <span role="checkbox" class="custom-check position-absolute start-0" aria-labelledby="filter_categories_{{$category_name}}"></span>
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
                    <label for="filter_products_subcategories_{{$subcategory->id}}" class="filter-check position-relative d-flex justify-content-center align-items-center user-select-none">
                        <input type="checkbox" role="checkbox" name="filter_products_subcategories[]" id="filter_products_subcategories_{{$subcategory->id}}" class="filter-checkbox" multiple="multiple" value="{{$subcategory->id}}" aria-labelledby="filter_subcategories_{{$collection_name}}">
                        <span role="checkbox" class="custom-check position-absolute start-0" aria-labelledby="filter_subcategories_{{$collection_name}}"></span>
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
                    <label for="filter_products_sizes_{{$product_size->size_value}}" class="filter-check position-relative d-flex justify-content-center align-items-center user-select-none">
                        <input type="checkbox" role="checkbox" name="filter_products_sizes[]" id="filter_products_sizes_{{$product_size->size_value}}" class="filter-checkbox" multiple="multiple" value="{{$product_size->size_value}}" aria-labelledby="filter_sizes_{{ $product_size->{SIZE} }}">
                        <span role="checkbox" class="custom-check position-absolute start-0" aria-labelledby="filter_sizes_{{ $product_size->{SIZE} }}"></span>
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
                <span class="separator d-flex justify-content-center align-items-center fs-10">-</span>
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
