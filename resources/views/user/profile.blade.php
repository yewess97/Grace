@extends(key(viewLayoutTitle(USER_MODEL)), [TITLE => $user_profile_title])

@section('content')

    {{-- Profile Main --}}
    <main role="main" class="profile-main py-6">
        <div class="container">
            <div class="main-sides row col-12">
                <!----======= Top Side =======---->
                <section class="top-side row col-12 justify-content-center align-items-center">
                    {{-- Profile Title --}}
                    <h2 class="profile-title fs-9 fw-600 lh-1 text-center">Welcome @userFullName()</h2>
                    {{-- Profile Account Details --}}
                    <ul role="list" class="profile-account-details box-content row col-12 py-4 border rounded">
                        {{-- My Account --}}
                        <li role="listitem" class="my-account col-6">
                            <article class="my-account-title mb-3">
                                <h6 class="fs-8 fw-600">My Account</h6>
                            </article>
                            <article class="my-account-addresses">
                                <a href="{{route(USER_ADDRESSES, [ID => encrypt($user->{ID})])}}" role="link" class="text-main">View My {{ucfirst(ADDRESSES_TABLE)}}</a>
                            </article>
                            <article class="my-account-logout">
                                <form action="{{route(LOGOUT)}}" method="post" role="form">
                                    @csrf
                                    <button type="submit" role="button" title="{{ucfirst(LOGOUT)}}" class="logout border-0 bg-transparent text-main">{{ucfirst(LOGOUT)}}</button>
                                </form>
                            </article>
                        </li>
                        {{-- Account Details --}}
                        <li role="listitem" class="account-details col-6">
                            <article class="account-details-title mb-3">
                                <h6 class="fs-8 fw-600">Account Details</h6>
                            </article>
                            <article class="account-details-table table-responsive">
                                <table role="table" class="align-middle mb-0 bg-white">
                                    <tr>
                                        <td>{{ucfirst(NAME)}}:</td>
                                        <td>
                                            <h6 class="ms-2 fw-500">{{ $user->{FULL_NAME} }}</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ucfirst(EMAIL)}}:</td>
                                        <td>
                                            <h6 class="ms-2 fw-500">{{ $user->{EMAIL} }}</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ucfirst(COUNTRY)}}:</td>
                                        <td class="d-flex align-items-center">
                                            @forelse ($user->{ADDRESSES_TABLE} as $address)
                                                <h6 class="fw-500 many-user-countries position-relative ms-2">
                                                    {{ $address->{COUNTRY} }}
                                                </h6>
                                            @empty
                                                <h6 class="fw-500 ms-2">No {{ucfirst(ADDRESS_MODEL)}} yet</h6>
                                            @endforelse
                                        </td>
                                    </tr>
                                </table>
                            </article>
                        </li>
                    </ul>
                </section>

                <!----======= Bottom Side =======---->
                <section class="bottom-side row col-12 gap-3">
                    {{-- Profile Order History Title --}}
                    <h6 class="profile-order-history-title fs-8 fw-600">{{ucfirst(ORDERS_TABLE)}} History</h6>
                    {{-- Profile Order History Table --}}
                    <div class="pagination-container search-table">
                        @include(PROFILE_ORDERS_PAGINATION, [USER_ORDERS => $user_orders])
                    </div>
                </section>
            </div>
        </div>
    </main>

@endsection
