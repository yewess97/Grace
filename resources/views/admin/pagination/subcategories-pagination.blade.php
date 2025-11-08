<div class="main-table admin-table table-responsive">
    <table role="table" class="subcategories-table table align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Main Image", "Related Categories", "Trashed")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse($subcategories as $key => $subcategory)
            @include(SUBCATEGORY_ROW_PARTIAL, [SUBCATEGORY_MODEL => $subcategory])
        @empty
            @noResults(SUBCATEGORIES_TABLE, 4)
        @endforelse
        </tbody>
    </table>
</div>

{{-- Subcategories Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($subcategories, ADMIN_SUBCATEGORIES_ROUTE)</div>
