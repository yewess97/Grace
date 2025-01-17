<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Email", "Role", "Status", "Last Seen")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($users as $key => $user)
            <tr style="{{$user->isadmin ? 'background-color:#ffffe0': ''}}">
                @checkRow($user->id)
                @loopIteration($users->firstItem())
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
                        <button type="button" role="button" title="{{EDIT_USER_TITLE}}" class="btn edit-btn edit-user-btn" data-mdb-toggle="modal" data-mdb-target="#edit_user_modal" data-route="{{route(EDIT_USER, $user->id)}}">
                            {{ucfirst(EDIT)}}
                        </button>
                        <button type="button" role="button" title="{{capitalizeAll(DELETE_USER)}}" class="btn delete-btn delete-user-btn" data-route="{{route(DELETE_USER, $user->id)}}" data-name="{{ $user->{FULL_NAME} }}">
                            {{ucfirst(DELETE)}}
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            @noResults(USERS_TABLE, 3)
        @endforelse
        </tbody>
    </table>
</div>

<div class="table-pagination col-12 pt-4">@pagination($users, ADMIN_USERS_ROUTE)</div>
