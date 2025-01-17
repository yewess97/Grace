<form action="{{route(SEARCH_PRODUCTS)}}" method="post" role="form" class="search-form position-relative">
    @csrf
    {{-- Search Input --}}
    <label for="search_products" class="w-100">
        <input type="search" name="search_value" id="search_products" class="w-100 rounded-start" placeholder="Search our products...." required="required">
    </label>
    {{-- Search Button --}}
    <button type="submit" role="button" title="Search" class="search-products-btn position-absolute top-0 end-0 h-100 border border-0" aria-label="{{capitalizeAll(SEARCH_PRODUCTS)}}">
        <i class="ti ti-search"></i>
    </button>
</form>
