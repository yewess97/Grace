@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Category Main --}}
    <main class="categories-main main-body" role="main">
        <div class="container">
            <div class="row">
                <section class="categories row col-12 gap-4">
                    {{-- Categories Search & Action Buttons --}}
                    <article class="row col-12 justify-content-between align-items-center gap-3">
                        {{-- Categories Search --}}
                        @search(SEARCH_CATEGORIES)

                        {{-- Categories (Delete all selected) & (Add) Buttons --}}
                        @collectionButtons(CATEGORIES_TABLE)
                    </article>

                    {{-- Categories Table --}}
                    <article class="pagination-container search-table">
                        @include(ADMIN_CATEGORIES_PAGINATION, [CATEGORIES_TABLE => $categories])
                    </article>
                </section>
            </div>
        </div>
    </main>


    {{-- Add Category Modal --}}
    @include(ADD_CATEGORY_MODAL)

    {{-- Edit Category Modal --}}
    @include(EDIT_CATEGORY_MODAL)

@endsection
