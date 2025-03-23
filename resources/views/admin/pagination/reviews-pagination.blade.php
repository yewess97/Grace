<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Title", "Body Text", "Rating", "Related Product", "From User", "Creation Date/Time", "Updated Date/Time")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($reviews as $key => $review)
            @include(REVIEW_ROW_PARTIAL, [REVIEW_MODEL => $review])
        @empty
            @noResults(REVIEWS_TABLE, 7)
        @endforelse
        </tbody>
    </table>
</div>

{{-- Reviews Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($reviews, ADMIN_REVIEWS_ROUTE)</div>
