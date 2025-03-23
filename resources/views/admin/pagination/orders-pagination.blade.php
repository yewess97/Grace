<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Tracking Number", "Customer Name", "Creation Date/Time", "Updated Date/Time", "Number of Items", "Total Cost", "Status")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($orders as $key => $order)
            @include(ORDER_ROW_PARTIAL, [ORDER_MODEL => $order])
        @empty
            @noResults(ORDERS_TABLE, 7)
        @endforelse
        </tbody>
    </table>
</div>

{{-- Orders Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($orders, ADMIN_ORDERS_ROUTE)</div>
