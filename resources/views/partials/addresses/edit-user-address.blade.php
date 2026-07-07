<div id="edit_address_modal" class="modal {{$role}}-modal {{$role}}-edit-modal top fade" tabindex="-1" aria-labelledby="edit_address" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <article class="modal-header">
                <h2 id="edit_address" class="modal-title fs-6 fw-600">{{EDIT_ADDRESS_TITLE}}</h2>
                @modalCloseBtn()
            </article>
            <article class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_ADDRESS, UPDATE)}}" method="post" role="form" id="update_address_form" class="grace-form" data-main="{{route(isAdminRoute() ? ADMIN_USER_ADDRESSES_ROUTE : USER_ADDRESSES, [ID => request()?->input(ID)])}}" data-loading_spinner="{{imageSource('loading2.webp')}}">
                    @csrf
                    @method('PUT')
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        <input type="hidden" name="update_address_id" id="update_address_id">
                        <input type="hidden" name="update_address_user_id" id="update_address_user_id" value="{{request()?->input(ID)}}">
                        {{-- Address Line 1 --}}
                        <div class="update-address-address-1 col-12">
                            <div class="form-outline">
                                <input type="text" name="update_address_address_1" id="update_address_address_1" class="form-control fs-7 rounded-2" min="3" max="80">
                                <label for="update_address_address_1" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(ADDRESS_MODEL)}} line 1
                                </label>
                            </div>
                            {{$update_address_error(ADDRESS1)}}
                        </div>

                        {{-- Address Line 2 --}}
                        <div class="update-address-address-2 col-12">
                            <div class="form-outline">
                                <input type="text" name="update_address_address_2" id="update_address_address_2" class="form-control fs-7 rounded-2" min="3" max="80">
                                <label for="update_address_address_2" class="form-label">
                                    {{ucfirst(ADDRESS_MODEL)}} line 2 (optional)
                                </label>
                            </div>
                            {{$update_address_error(ADDRESS2)}}
                        </div>

                        {{-- Country & City & State --}}
                        <div class="country-city-state row col-12 align-items-center gap-3 gap-lg-0">
                            {{-- Country --}}
                            <div class="update-address-country col-12 col-lg-4 pe-lg-2">
                                <div class="form-group position-relative">
                                    <label for="update_address_country" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{ucfirst(COUNTRY)}}
                                    </label>
                                    <select name="update_address_country" id="update_address_country" class="form-select address-country"></select>
                                </div>
                                {{$update_address_error(COUNTRY)}}
                            </div>

                            {{-- City --}}
                            <div class="update-address-city col-12 col-lg-4 px-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="update_address_city" id="update_address_city" class="form-control fs-7 rounded-2" min="2" max="50">
                                    <label for="update_address_city" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(CITY)}}
                                    </label>
                                </div>
                                {{$update_address_error(CITY)}}
                            </div>

                            {{-- State --}}
                            <div class="update-address-state col-12 col-lg-4 ps-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="update_address_state" id="update_address_state" class="form-control fs-7 rounded-2" min="2" max="50">
                                    <label for="update_address_state" class="form-label">
                                        {{ucfirst(STATE)}} (optional)
                                    </label>
                                </div>
                                {{$update_address_error(STATE)}}
                            </div>
                        </div>

                        {{-- Phone Number & Postal Code --}}
                        <div class="phone-postal row col-12 align-items-end gap-3 gap-lg-0">
                            {{-- Phone Number --}}
                            <div class="address-phone col-12 col-lg-6 pe-lg-2">
                                <div class="form-group col-12">
                                    <label for="update_address_phone" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(PHONE)}}
                                    </label>
                                    <div id="update_address_phone_container" class="address-phone-container position-relative d-flex align-items-center border rounded-2 overflow-visible">
                                        <div class="address-phone-country-selector d-flex align-items-center px-3 cursor-pointer">
                                            <img class="selected-flag" src="" alt="Flag">
                                            <span class="selected-calling-code fw-600">+20</span>
                                            <span class="chevron-icon mb-1"></span>
                                        </div>
                                        <input type="tel" id="update_address_phone" class="update-address-phone address-phone-input px-3 border-0" placeholder="101 183 6243">
                                        <div class="address-phone-dropdown-container position-absolute w-100 border rounded-3 overflow-hidden">
                                            <input type="text" id="update_address_phone_country_search" class="country-search-input w-100 px-3 border-bottom" placeholder="Search country or code...">
                                            <ul class="countries-list overflow-auto"></ul>
                                        </div>
                                    </div>
                                    <input type="hidden" name="update_address_phone">
                                </div>
                                {{$update_address_error(PHONE)}}
                            </div>

                            {{-- Postal Code --}}
                            <div class="update-address-postal-code col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline">
                                    <input type="number" inputmode="numeric" name="update_address_postal_code" id="update_address_postal_code" class="form-control fs-7 rounded-2">
                                    <label for="update_address_postal_code" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeAll(POSTAL_CODE)}}
                                    </label>
                                </div>
                                {{$update_address_error(POSTAL_CODE)}}
                            </div>
                        </div>
                    </div>

                    {{-- Save Changes Buttons --}}
                    @submitButton(SAVE_CHANGES)
                </form>
            </article>
        </div>
    </div>
</div>
