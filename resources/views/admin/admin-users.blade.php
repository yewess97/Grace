@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Users Main --}}
    <main class="users-main main-body" role="main">
        <div class="container">
            <div class="row">
                <section class="users row col-12 gap-4">
                    {{-- Users Search/Filter & Action Buttons --}}
                    <div class="search-filter-action-buttons row col-12 justify-content-between align-items-baseline">
                        <article
                                class="search-filter-users row col-12 col-md-8 justify-content-between align-items-center gap-3">
                            {{-- Users Search --}}
                            @search(SEARCH_USERS)

                            {{-- Users Filter by Role --}}
                            <form action="{{route(SEARCH_USERS, ['type' => FILTER])}}" method="post" role="form"
                                  id="filter_users_form" class="grace-form filter-form col-12 col-md-5"
                                  data-no_results="{{imageSource('no-results.png')}}">
                                @csrf
                                <div class="grace-form-body row col-12 justify-content-between">
                                    {{-- Role --}}
                                    <div class="filter-users-role">
                                        <div class="form-group position-relative">
                                            <label for="filter_users_role"
                                                   class="label-select position-absolute user-select-none pe-none"></label>
                                            <select name="filter_users_role" id="filter_users_role"
                                                    class="form-select py-2">
                                                <option disabled selected>{{ucfirst(FILTER)}}
                                                    by {{ucfirst(ROLE)}} </option>
                                                @foreach ($roles as $role => $value)
                                                    <option value="{{$value}}">{{pluralize($role)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{$filter_users_error(ROLE)}}
                                    </div>
                                </div>
                            </form>

                            {{-- Clear Search/Filter Button --}}
                            <div class="d-grid place-items-center">
                                @clearSearchFilter(route(ADMIN_USERS_ROUTE, [CONDITION => conditionRequest()]))
                            </div>
                        </article>

                        {{-- Users Main Buttons --}}
                        @collectionButtons(USERS_TABLE, ADMIN_USERS_ROUTE)
                    </div>

                    {{-- Users Table --}}
                    <div class="pagination-container search-table">
                        @include(ADMIN_USERS_PAGINATION, [USERS_TABLE => $users])
                    </div>
                </section>
            </div>
        </div>
    </main>


    {{-- Add User Modal --}}
    @include(ADD_USER_MODAL)

    {{-- Edit User Modal --}}
    @include(EDIT_USER_MODAL)

@endsection
