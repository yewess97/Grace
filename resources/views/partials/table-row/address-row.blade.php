<tr id="row_{{$address->id}}">
    @checkRow($address->id)
    @loopIteration()
    <td class="truncate">
        <p>{{ $address->{ADDRESS1} }}</p>
    </td>
    <td class="truncate">
        <p><i>{{$address->{ADDRESS2} ?? 'No Address line 2'}}</i></p>
    </td>
    <td>
        <p>{{ $address->{CITY} }}</p>
    </td>
    <td>
        <p><i>{{$address->{STATE} ?? 'No '.STATE}}</i></p>
    </td>
    <td>
        <p>{{ $address->{COUNTRY} }}</p>
    </td>
    <td>
        <p>{{ $address->{POSTAL_CODE} }}</p>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if($address->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_ADDRESS)}}" class="restore-address-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_ADDRESS, $address->id)}}" data-id="{{$address->id}}" data-name="{{ $address->{USER_MODEL}->{FULL_NAME} }}">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_ADDRESS_TITLE}}" class="edit-address-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_address_modal" data-route="{{route(EDIT_ADDRESS, $address->id)}}">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>
            @endif
            <button type="button" role="button" title="{{capitalizeAll($address->trashed() ? DELETE_ADDRESS : REMOVE_ADDRESS)}}" class="delete-address-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_ADDRESS, $address->id)}}" data-id="{{$address->id}}" data-name="{{ $address->{USER_MODEL}->{FULL_NAME} }}">
                <i class="{{$address->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
            </button>
        </div>
    </td>
</tr>
