<x-edit-review role="{{USER_MODEL}}" dataReviews="{{route(REVIEWS_TABLE, $product->id)}}">
    @include(UPDATE_REVIEW_ERRORS_PARTIAL)
</x-edit-review>
