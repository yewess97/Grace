<div class="main-table admin-table table-responsive">
    <table role="table" class="table align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Main Image", "Banner Image")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($categories as $key => $category)
            @include(CATEGORY_ROW_PARTIAL, [CATEGORY_MODEL => $category])
        @empty
            @noResults(CATEGORIES_TABLE, 3)
        @endforelse
        </tbody>
    </table>
</div>

<div class="table-pagination col-12 pt-4">@pagination($categories, ADMIN_CATEGORIES_ROUTE)</div>
