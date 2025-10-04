<tr id="row_{{$user->id}}" class="{{$user->isAdmin ? 'bg-highlight': ''}}">
    @checkRow($user->id)
    @loopIteration()
    <td>
        <p>{{ $user->{FULL_NAME} }}</p>
    </td>
    <td>
        <p>{{ $user->{EMAIL} }}</p>
    </td>
    <td>
        <p>{{array_search($user->{ROLE}, USER_ROLE_ENUM, true)}}</p>
    </td>
    <td>
        <div class="user-activity mx-auto">
            <img
                src="{{imageSource('user-'.(cache()->has('is_online_'.$user->{ID}) ? 'on' : 'off').'line-status.png')}}"
                alt="{{ucfirst(USER_MODEL)}} Activity">
        </div>
    </td>
    <td>
        <p>{{\Carbon\Carbon::parse($user->{LAST_SEEN})->diffForHumans()}}</p>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if ($user->{ADDRESSES_TABLE}->isNotEmpty())
                <a href="{{route(ADMIN_USER_ADDRESSES_ROUTE, [ID => encrypt($user->id)])}}" type="button" role="link"
                   title="{{capitalizeAll('view_'.ADDRESSES_TABLE)}}" data-tooltip="tooltip" data-mdb-placement="top"
                   class="view-user-addresses-btn fs-5 text-warning">
                    <x-action-icon action="view"/>
                </a>
            @endif

            @if($user->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_USER)}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-route="{{route(RESTORE_USER, $user->id)}}" data-id="{{$user->id}}"
                        data-name="{{ $user->{FULL_NAME} }}"
                        class="restore-user-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{RESTORE}}"/>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_USER_TITLE}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-mdb-toggle="modal" data-mdb-target="#edit_user_modal"
                        data-route="{{route(EDIT_USER, $user->id)}}"
                        class="edit-user-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{EDIT}}"/>
                </button>
            @endif

            <button type="button" role="button" title="{{capitalizeAll($user->trashed() ? DELETE_USER : REMOVE_USER)}}"
                    data-tooltip="tooltip" data-mdb-placement="top"
                    data-route="{{route(DELETE_USER, $user->id)}}" data-id="{{$user->id}}"
                    data-name="{{ $user->{FULL_NAME} }}"
                    class="delete-user-btn h-fit-content fs-5 text-danger bg-transparent border-0">
                <x-action-icon action="{{$user->trashed() ? DELETE : REMOVE}}"/>
            </button>
        </div>
    </td>
</tr>
