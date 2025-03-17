<tr id="row_{{$user->id}}" style="{{$user->isAdmin ? 'background-color:#ffffe0': ''}}">
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
            <img src="{{imageSource('user-'.(Cache::has('is_online_'.$user->{ID}) ? 'on' : 'off').'line-status.png')}}" alt="{{ucfirst(USER_MODEL)}} Activity">
        </div>
    </td>
    <td>
        <p>{{\Carbon\Carbon::parse($user->{LAST_SEEN})->diffForHumans()}}</p>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if ($user->{ADDRESSES_TABLE}->isNotEmpty())
                <a href="{{route(ADMIN_USER_ADDRESSES_ROUTE, [ID => encrypt($user->id)])}}" type="button" role="link" class="btn view-btn view-user-addresses-btn">
                    View {{ucfirst(ADDRESSES_TABLE)}}
                </a>
            @endif

            @if($user->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_USER)}}" class="restore-user-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_USER, $user->id)}}" data-id="{{$user->id}}" data-name="{{ $user->{FULL_NAME} }}">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_USER_TITLE}}" class="edit-user-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_user_modal" data-route="{{route(EDIT_USER, $user->id)}}">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>
            @endif

            <button type="button" role="button" title="{{capitalizeAll($user->trashed() ? DELETE_USER : REMOVE_USER)}}" class="delete-user-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_USER, $user->id)}}" data-id="{{$user->id}}" data-name="{{ $user->{FULL_NAME} }}">
                <i class="{{$user->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
            </button>
        </div>
    </td>
</tr>
