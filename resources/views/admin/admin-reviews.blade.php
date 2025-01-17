@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Reviews Main --}}
    <main role="main" class="reviews-main main-body">
        <div class="container">
            <div class="row">
                <section class="reviews row col-12 gap-4">
                    {{-- Reviews Search & Delete all selected Button --}}
                    <div class="row col-12 align-items-center gap-3">
                        {{-- Reviews Search --}}
                        @search(SEARCH_REVIEWS, $review_rating)

                        {{-- Reviews (Delete all selected) Button --}}
                        <div class="delete-all-selected-button col-12 col-md-5 d-flex justify-content-lg-end justify-content-md-end justify-content-sm-center align-items-center">
                            <button type="button" role="button" title="{{capitalizeAll(pluralize(DELETE_REVIEW))}}" id="delete_reviews_btn" class="btn delete-btn" data-route="{{route(pluralize(DELETE_REVIEW))}}">
                                Delete all selected
                            </button>
                        </div>
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
