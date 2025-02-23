@extends(key(viewLayoutTitle(isAdminRoute(true))), [TITLE => $user_addresses_title])

@section('content')

    {{-- Addresses Breadcrumb --}}
    @if(!isAdminRoute())
        <nav role="navigation" class="nav-breadcrumb breadcrumb-navigation" aria-label="breadcrumb">
            <div class="container">
                <div class="row">
                    <ol role="list" class="breadcrumb">
                        <li role="listitem" class="breadcrumb-item fw-500">
                            <a href="{{route(PROFILE)}}" role="link">{{ucfirst(PROFILE)}}</a>
                        </li>
                        <li role="listitem" class="breadcrumb-item active fw-500" aria-current="page">
                            My {{ucfirst(ADDRESSES_TABLE)}}
                        </li>
                    </ol>
                </div>
            </div>
        </nav>
    @endif

    {{-- Addresses Main --}}
    <main role="main" class="addresses-main {{isAdminRoute() ? 'main-body' : 'py-6'}}">
        <div class="container">
            <div class="row">
                <section class="addresses row col-12 gap-4">
                    {{-- Addresses Search & Delete all selected Button --}}
                    <div class="addresses-add-delete-multiple-buttons row col-12 justify-content-between align-items-center gap-3">
                        {{-- Addresses Search --}}
                        @search(SEARCH_ADDRESSES, $user_id)

                        {{-- Addresses Main Buttons --}}
                        @collectionButtons(ADDRESSES_TABLE, ADMIN_USER_ADDRESSES_ROUTE)
                    </div>

                    {{-- Addresses Table --}}
                    <div class="pagination-container search-table row gap-4">
                        <div class="main-table admin-table table-responsive">
                            <table role="table" class="table table-bordered address-table align-middle mb-0 fs-7 bg-white">
                                <thead class="text-center bg-light">
                                <tr>
                                    @tableHeaders("Address line 1", "Address line 2", "City", "State", "Country", "Postal Code")
                                </tr>
                                </thead>
                                <tbody class="text-center">
                                @forelse ($user_addresses as $key => $address)
                                    <tr>
                                        @checkRow($address->id)
                                        @loopIteration($user_addresses->firstItem())
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
                                                    <button type="button" role="button" title="{{capitalizeAll(RESTORE_ADDRESS)}}" class="restore-address-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_ADDRESS, $address->id)}}" data-name="{{ $address->{USER_MODEL}->{FULL_NAME} }}">
                                                        <i class="fa-solid fa-arrow-rotate-left"></i>
                                                    </button>
                                                @else
                                                    <button type="button" role="button" title="{{EDIT_ADDRESS_TITLE}}" class="edit-address-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_address_modal" data-route="{{route(EDIT_ADDRESS, $address->id)}}">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                @endif
                                                <button type="button" role="button" title="{{capitalizeAll($address->trashed() ? DELETE_ADDRESS : REMOVE_ADDRESS)}}" class="delete-address-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_ADDRESS, $address->id)}}" data-name="{{ $address->{USER_MODEL}->{FULL_NAME} }}">
                                                    <i class="{{$address->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    @noResults(ADDRESSES_TABLE, 6)
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12">
                            @pagination($user_addresses, isAdminRoute() ? ADMIN_USER_ADDRESSES_ROUTE : USER_ADDRESSES)
                        </div>

                    </div>
                </section>
            </div>
        </div>
    </main>


    {{-- Add User Address --}}
    @include(ADD_USER_ADDRESS_PARTIAL, [ROLE => isAdminRoute(true)])

    {{-- Edit User Address --}}
    @include(EDIT_USER_ADDRESS_PARTIAL, [ROLE => isAdminRoute(true)])

@endsection
