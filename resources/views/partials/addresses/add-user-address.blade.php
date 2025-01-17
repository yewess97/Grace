<div id="add_address_modal" class="modal {{$role}}-modal top fade" tabindex="-1" aria-labelledby="add_address" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <h2 id="add_address" class="modal-title fs-6 fw-600">{{ADD_ADDRESS_TITLE}}</h2>
                @modalCloseBtn()
            </div>
            <div class="modal-body pb-0">
                {{$add_address_error(USER_ID)}}

                <form action="{{route(CREATE_UPDATE_ADDRESS, ADD)}}" method="post" role="form" id="add_address_form" class="grace-form">
                    @csrf
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        <input type="hidden" name="add_address_user_id" id="add_address_user_id" value="{{request()?->input(ID)}}">
                        {{-- Address Line 1 --}}
                        <div class="add-address-address-1 col-12">
                            <div class="form-outline">
                                <input type="text" name="add_address_address_1" id="add_address_address_1" class="form-control fs-7 rounded-2" min="3" max="80">
                                <label for="add_address_address_1" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(ADDRESS_MODEL)}} line 1
                                </label>
                            </div>
                            {{$add_address_error(ADDRESS1)}}
                        </div>

                        {{-- Address Line 2 --}}
                        <div class="add-address-address-2 col-12">
                            <div class="form-outline">
                                <input type="text" name="add_address_address_2" id="add_address_address_2" class="form-control fs-7 rounded-2" min="3" max="80" placeholder="Apartment, Suit, etc...">
                                <label for="add_address_address_2" class="form-label">
                                    {{ucfirst(ADDRESS_MODEL)}} line 2 (optional)
                                </label>
                            </div>
                            {{$add_address_error(ADDRESS2)}}
                        </div>

                        {{-- City & State --}}
                        <div class="city-state row col-12 align-items-center gap-3 gap-lg-0">
                            {{-- City --}}
                            <div class="add-address-city col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="add_address_city" id="add_address_city" class="form-control fs-7 rounded-2" min="2" max="50">
                                    <label for="add_address_city" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(CITY)}}
                                    </label>
                                </div>
                                {{$add_address_error(CITY)}}
                            </div>

                            {{-- State --}}
                            <div class="add-address-state col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline">
                                    <input type="text" name="add_address_state" id="add_address_state" class="form-control fs-7 rounded-2" min="2" max="50">
                                    <label for="add_address_state" class="form-label">
                                        {{ucfirst(STATE)}} (optional)
                                    </label>
                                </div>
                                {{$add_address_error(STATE)}}
                            </div>
                        </div>

                        {{-- Postal Code & Country --}}
                        <div class="postal-country row col-12 align-items-center gap-3 gap-lg-0">
                            {{-- Postal Code --}}
                            <div class="add-address-postal-code col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline">
                                    <input type="number" inputmode="numeric" name="add_address_postal_code" id="add_address_postal_code" class="form-control fs-7 rounded-2">
                                    <label for="add_address_postal_code" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeAll(POSTAL_CODE)}}
                                    </label>
                                </div>
                                {{$add_address_error(POSTAL_CODE)}}
                            </div>

                            {{-- Country --}}
                            <div class="add-address-country col-12 col-lg-6 ps-lg-2">
                                <div class="form-group position-relative">
                                    <label for="add_address_country" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{ucfirst(COUNTRY)}}
                                    </label>
                                    <select name="add_address_country" id="add_address_country" class="form-select address-country"></select>
                                </div>
                                {{$add_address_error(COUNTRY)}}
                            </div>
                        </div>
                    </div>

                    {{-- Add Button --}}
                    @submitButton(ADD)
                </form>
            </div>
        </div>
    </div>
</div>
