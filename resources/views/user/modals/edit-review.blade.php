<x-reviews.edit-review role="{{USER_MODEL}}" dataReviews="{{route(REVIEWS_TABLE, $product->id)}}">
    @include(UPDATE_REVIEW_ERRORS_PARTIAL)
</x-reviews.edit-review>
