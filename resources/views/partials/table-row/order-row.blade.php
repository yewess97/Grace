<tr id="row_{{$order->id}}" @class(['bg-highlight' => empty($order->{USER_MODEL})])>
    @checkRow($order->id)
    @loopIteration()
    <td>
        <a href="{{route(ADMIN_ORDER_DETAILS_ROUTE, [TRACKING_NUM => $order->{TRACKING_NUM}])}}" role="link"
           class="order-tracking-num fw-600">{{ $order->{TRACKING_NUM} }}</a>
    </td>
    <td>
        <p>{!! $order->{USER_MODEL}->{FULL_NAME} ?? '<b>The '.ucfirst(USER_MODEL).' has been removed</b>' !!}</p>
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
        <p>@priceFormat($order->{TOTAL_COST})</p>
    </td>
    <td>
        <span class="badge badge-{{orderStatus($order, 'badge')}} rounded-pill d-inline p-2">{{orderStatus($order)}}</span>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if ($order->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_ORDER)}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-route="{{route(RESTORE_ORDER, $order->id)}}"
                        data-name="{{ $order->{TRACKING_NUM} }}"
                        data-main="{{route(ADMIN_ORDERS_ROUTE, [STATUS => $order->{STATUS}, CONDITION => conditionRequest()])}}"
                        class="restore-order-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{RESTORE}}"/>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_ORDER_TITLE}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-mdb-toggle="modal" data-mdb-target="#edit_order_modal"
                        data-route="{{route(EDIT_ORDER, $order->id)}}"
                        class="edit-order-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{EDIT}}"/>
                </button>
            @endif
            <button type="button" role="button"
                    title="{{capitalizeAll($order->trashed() ? DELETE_ORDER : REMOVE_ORDER)}}"
                    data-tooltip="tooltip" data-mdb-placement="top"
                    data-route="{{route(DELETE_ORDER, $order->id)}}"
                    data-name="{{ $order->{TRACKING_NUM} }}"
                    data-main="{{route(ADMIN_ORDERS_ROUTE, [STATUS => $order->{STATUS}, CONDITION => conditionRequest()])}}"
                    class="delete-order-btn h-fit-content fs-5 text-danger bg-transparent border-0">
                <x-action-icon action="{{$order->trashed() ? DELETE : REMOVE}}"/>
            </button>
        </div>
    </td>
</tr>
