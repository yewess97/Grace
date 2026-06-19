<div id="add_user_modal" class="modal admin-modal top fade" tabindex="-1" aria-labelledby="add_user" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <article class="modal-header">
                <h2 id="add_user" class="modal-title fs-6 fw-600">{{ADD_USER_TITLE}}</h2>
                @modalCloseBtn()
            </article>
            <article class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_USER, ADD)}}" method="post" role="form" id="add_user_form" class="grace-form" data-main="{{route(ADMIN_USERS_ROUTE)}}" data-loading_spinner="{{imageSource('loading2.png')}}">
                    @csrf
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        {{-- User First name & Last name --}}
                        <div class="user-first-last-name row col-12 gap-3 gap-lg-0">
                            {{-- User First Name --}}
                            <div class="add-user-first-name col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="add_user_first_name" id="add_user_first_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                    <label for="add_user_first_name" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeFirst(FIRST_NAME)}}
                                    </label>
                                </div>
                                {{$add_user_error(FIRST_NAME)}}
                            </div>

                            {{-- User Last Name --}}
                            <div class="add-user-last-name col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="add_user_last_name" id="add_user_last_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                    <label for="add_user_last_name" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeFirst(LAST_NAME)}}
                                    </label>
                                </div>
                                {{$add_user_error(LAST_NAME)}}
                            </div>
                        </div>

                        {{-- User Email & Password --}}
                        <div class="user-email-password row col-12 gap-3 gap-lg-0">
                            {{-- User Email --}}
                            <div class="add-user-email col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline">
                                    <input type="email" inputmode="email" name="add_user_email" id="add_user_email" class="form-control fs-7 rounded-2" aria-required="true">
                                    <label for="add_user_email" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(EMAIL)}}
                                    </label>
                                </div>
                                {{$add_user_error(EMAIL)}}
                            </div>

                            {{-- User Password --}}
                            <div class="add-user-password col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline">
                                    <input type="password" name="add_user_password" id="add_user_password" class="form-control fs-7 rounded-2" min="8" aria-required="true" autocomplete="off">
                                    <label for="add_user_password" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(PASSWORD)}}
                                    </label>
                                </div>
                                {{$add_user_error(PASSWORD)}}
                            </div>
                        </div>

                        {{-- User Role --}}
                        <div class="add-user-role col-12">
                            <div class="form-group position-relative">
                                <label for="add_user_role" class="label-select position-absolute user-select-none pe-none">
                                    <sup class="me-1">*</sup>{{ucfirst(ROLE)}}
                                </label>
                                <select name="add_user_role" id="add_user_role" class="form-select" aria-required="true">
                                    @foreach ($roles as $role => $value)
                                        <option value="{{$value}}">{{$role}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{$add_user_error(ROLE)}}
                        </div>
                    </div>

                    {{-- Add Button --}}
                    @submitButton(ADD)
                </form>
            </article>
        </div>
    </div>
</div>
