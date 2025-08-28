<div class="profile-order-history-table main-table table-responsive rounded-5">
    <table role="table" class="table align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Tracking Number", "Creation Date/Time", "Number of Items", "Total Cost", "Status", "Action")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($user_orders as $key => $order)
            <tr>
                @loopIteration($user_orders->firstItem())
                <td>
                    <p class="fw-600">{{ $order->{TRACKING_NUM} }}</p>
                </td>
                <td>
                    <p class="fw-500">{!! dates($order, 0, true) !!}</p>
                </td>
                <td>
                    <p class="fw-500">{{ $order->{NUM_ITEMS} }}</p>
                </td>
                <td>
                    <p class="fw-500">@priceFormat($order->{TOTAL_COST})</p>
                </td>
                <td>
                    <span class="badge badge-{{orderStatus($order, 'badge')}} rounded-pill d-inline p-2">{{orderStatus($order)}}</span>
                </td>
                <td>
                    <a href="{{route(ORDER_DETAILS, [TRACKING_NUM => $order->{TRACKING_NUM}])}}" role="link" class="fw-500 text-main text-decoration-underline">View Details</a>
                </td>
            </tr>
        @empty
            @noResults(ORDERS_TABLE, 5)
        @endforelse
        </tbody>
    </table>
</div>

{{-- User Orders Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($user_orders, PROFILE)</div>
