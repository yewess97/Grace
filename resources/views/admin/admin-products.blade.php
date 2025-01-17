@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Products Main --}}
    <main class="products-main main-body" role="main">
        <div class="container">
            <div class="row">
                <section class="products row col-12 gap-4">
                    {{-- Products Search & Action Buttons --}}
                    <div class="row col-12 justify-content-between align-items-center gap-3">
                        {{-- Products Search --}}
                        @search(SEARCH_PRODUCTS)

                        {{-- Products (Delete all selected) & (Add) Buttons --}}
                        @collectionButtons(PRODUCTS_TABLE)
                    </div>

                    {{-- Products Table --}}
                    <div class="pagination-container search-table">
                        @include(ADMIN_PRODUCTS_PAGINATION, [PRODUCTS_TABLE => $products])
                    </div>
                </section>
            </div>
        </div>
    </main>


    {{-- Add Product Modal --}}
    @include(ADD_PRODUCT_MODAL)

    {{-- Edit Product Modal --}}
    @include(EDIT_PRODUCT_MODAL)

@endsection
