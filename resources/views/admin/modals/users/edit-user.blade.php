<div id="edit_user_modal" class="modal admin-modal admin-edit-modal top fade" tabindex="-1" aria-labelledby="edit_user" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <h2 id="edit_user" class="modal-title fs-6 fw-600">{{EDIT_USER_TITLE}}</h2>
                @modalCloseBtn()
            </div>
            <div class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_USER, UPDATE)}}" method="post" role="form" id="update_user_form" class="grace-form">
                    @csrf
                    @method('PUT')
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        <input type="hidden" name="update_user_id" id="update_user_id">
                        {{-- User First name & Last name --}}
                        <div class="user-first-last-name row col-12 gap-3 gap-lg-0">
                            {{-- User First Name --}}
                            <div class="update-user-first-name col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="update_user_first_name" id="update_user_first_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                    <label for="update_user_first_name" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeFirst(FIRST_NAME)}}
                                    </label>
                                </div>
                                {{$update_user_error(FIRST_NAME)}}
                            </div>

                            {{-- User Last Name --}}
                            <div class="update-user-last-name col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="update_user_last_name" id="update_user_last_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                    <label for="update_user_last_name" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeFirst(LAST_NAME)}}
                                    </label>
                                </div>
                                {{$update_user_error(LAST_NAME)}}
                            </div>
                        </div>

                        {{-- User Email & Password --}}
                        <div class="user-email-password row col-12 gap-3 gap-lg-0">
                            {{-- User Email --}}
                            <div class="update-user-email col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline">
                                    <input type="email" inputmode="email" name="update_user_email" id="update_user_email" class="form-control fs-7 rounded-2" aria-required="true">
                                    <label for="update_user_email" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(EMAIL)}}
                                    </label>
                                </div>
                                {{$update_user_error(EMAIL)}}
                            </div>

                            {{-- User Password --}}
                            <div class="update-user-password col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline">
                                    <input type="password" name="update_user_password" id="update_user_password" class="form-control fs-7 rounded-2" min="8" aria-required="true">
                                    <label for="update_user_password" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(PASSWORD)}}
                                    </label>
                                </div>
                                {{$update_user_error(PASSWORD)}}
                            </div>
                        </div>

                        {{-- User Role --}}
                        <div class="update-user-role col-12">
                            <div class="form-group position-relative">
                                <label for="update_user_role" class="label-select position-absolute user-select-none pe-none">
                                    <sup class="me-1">*</sup>{{ucfirst(ROLE)}}
                                </label>
                                <select name="update_user_role" id="update_user_role" class="form-select" aria-required="true">
                                    @foreach ($roles as $role => $value)
                                        <option value="{{$value}}">{{$role}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{$update_user_error(ROLE)}}
                        </div>
                    </div>

                    {{-- Save Changes Button --}}
                    @submitButton(SAVE_CHANGES)
                </form>
            </div>
        </div>
    </div>
</div>
