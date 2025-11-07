<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Short Description", "Long Description", "Main Image", "Thumbnail Images", "Sizes", "Old Price", "New Price", "Quantity", "Related Categories", "Related Subcategories", "Stock Status", "Trashed")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse($products as $key => $product)
            @include(PRODUCT_ROW_PARTIAL, [PRODUCT_MODEL => $product])
        @empty
            @noResults(PRODUCTS_TABLE, 12)
        @endforelse
        </tbody>
    </table>
</div>

{{-- Products Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($products, ADMIN_PRODUCTS_ROUTE)</div>
