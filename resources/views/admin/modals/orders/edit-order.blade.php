<div id="edit_order_modal" class="modal admin-modal admin-edit-modal top fade" tabindex="-1" aria-labelledby="edit_order" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <article class="modal-header">
                <h2 id="edit_order" class="modal-title fs-6 fw-600">{{EDIT_ORDER_TITLE}} {{ucfirst(STATUS)}}</h2>
                @modalCloseBtn()
            </article>
            <article class="modal-body pb-0">
                <form action="{{route(UPDATE.'_'.ORDER_MODEL)}}" method="post" role="form" id="update_order_form" class="grace-form" data-loading_spinner="{{imageSource('loading2.png')}}">
                    @csrf
                    @method('PUT')
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        <input type="hidden" name="update_order_id" id="update_order_id">
                        <div class="form-group position-relative mb-2">
                            <label for="update_order_status" class="label-select position-absolute user-select-none pe-none">
                                <sup class="me-1">*</sup>{{ucfirst(STATUS)}}
                            </label>
                            <select name="update_order_status" id="update_order_status" class="form-select" aria-required="true">
                                @foreach ($statuses as $status => $value)
                                    <option value="{{$value}}">{{$status}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{$update_order_error(STATUS)}}
                    </div>

                    {{-- Save Changes Button --}}
                    @submitButton(SAVE_CHANGES)
                </form>
            </article>
        </div>
    </div>
</div>
