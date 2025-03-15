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
                        @include(USER_ADDRESSES_PAGINATION, [ADDRESSES_TABLE => $user_addresses])
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
