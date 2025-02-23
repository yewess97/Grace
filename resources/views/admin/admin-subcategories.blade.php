@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Subcategories Main --}}
    <main class="subcategories-main main-body" role="main">
        <div class="container">
            <div class="row">
                <section class="subcategories row col-12 gap-4">
                    {{-- Subcategories Search & Action Buttons --}}
                    <div class="row col-12 justify-content-between align-items-center gap-3">
                        {{-- Subcategories Search --}}
                        @search(SEARCH_SUBCATEGORIES)

                        {{-- Subcategories Main Buttons --}}
                        @collectionButtons(SUBCATEGORIES_TABLE, ADMIN_SUBCATEGORIES_ROUTE)
                    </div>

                    {{-- Subcategories Table --}}
                    <div class="pagination-container search-table">
                        @include(ADMIN_SUBCATEGORIES_PAGINATION, [SUBCATEGORIES_TABLE => $subcategories])
                    </div>
                </section>
            </div>
        </div>
    </main>


    {{-- Add Subcategory Modal --}}
    @include(ADD_SUBCATEGORY_MODAL)

    {{-- Edit Subcategory Modal --}}
    @include(EDIT_SUBCATEGORY_MODAL)

@endsection
