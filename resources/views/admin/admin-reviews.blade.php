@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Reviews Main --}}
    <main role="main" class="reviews-main main-body">
        <div class="container">
            <div class="row">
                <section class="reviews row col-12 gap-4">
                    {{-- Reviews Search & Delete all selected Button --}}
                    <div class="row col-12 justify-content-between align-items-center gap-3">
                        {{-- Reviews Search --}}
                        @search(SEARCH_REVIEWS, $review_rating)

                        {{-- Reviews Main Buttons --}}
                        @collectionButtons(REVIEWS_TABLE, ADMIN_REVIEWS_ROUTE)
                    </div>

                    {{-- Reviews Table --}}
                    <div class="pagination-container search-table">
                        @include(ADMIN_REVIEWS_PAGINATION, [REVIEWS_TABLE => $reviews])
                    </div>
                </section>
            </div>
        </div>
    </main>


    {{-- Edit Review Modal --}}
    @include(EDIT_REVIEW_MODAL)

@endsection
