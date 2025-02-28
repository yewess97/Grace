<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Tracking Number", "Customer Name", "Creation Date/Time", "Updated Date/Time", "Number of Items", "Total Cost", "Status")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($orders as $key => $order)
            <tr>
                @checkRow($order->id)
                @loopIteration($orders->firstItem())
                <td>
                    <a href="{{route(ADMIN_ORDER_DETAILS_ROUTE, [TRACKING_NUM => $order->{TRACKING_NUM}])}}" role="link" class="order-tracking-num fw-600">{{ $order->{TRACKING_NUM} }}</a>
                </td>
                <td>
                    <p>{{ $order->{USER_MODEL}->{FULL_NAME} }}</p>
                </td>
                <td>
                    <p>{!! dates($order, 0, true) !!}</p>
                </td>
                <td>
                    <p>{!! dates($order, 1, true) !!}</p>
                </td>
                <td>
                    <p>{{ $order->{NUM_ITEMS} }}</p>
                </td>
                <td>
                    <p>@price($order->{TOTAL_COST})</p>
                </td>
                <td>
                    <span class="badge badge-{{orderStatus($order, 'badge')}} rounded-pill d-inline p-2">{{orderStatus($order)}}</span>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        @if($order->trashed())
                            <button type="button" role="button" title="{{capitalizeAll(RESTORE_ORDER)}}" class="restore-order-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_ORDER, $order->id)}}" data-name="{{ $order->{TRACKING_NUM} }}">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                            </button>
                        @else
                            <button type="button" role="button" title="{{EDIT_ORDER_TITLE}}" class="edit-order-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_order_modal" data-route="{{route(EDIT_ORDER, $order->id)}}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                        @endif
                        <button type="button" role="button" title="{{capitalizeAll($order->trashed() ? DELETE_ORDER : REMOVE_ORDER)}}" class="delete-order-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_ORDER, $order->id)}}" data-name="{{ $order->{TRACKING_NUM} }}">
                            <i class="{{$order->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            @noResults(ORDERS_TABLE, 7)
        @endforelse
        </tbody>
    </table>
</div>

<div class="table-pagination col-12 pt-4">@pagination($orders, ADMIN_ORDERS_ROUTE)</div>
