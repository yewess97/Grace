<div id="edit_review_modal" class="modal {{$role}}-modal {{$role}}-edit-modal top fade" tabindex="-1" aria-labelledby="edit_review" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <h2 id="edit_review" class="modal-title fs-6 fw-600">{{EDIT_REVIEW_TITLE}}</h2>
                @modalCloseBtn()
            </div>
            <div class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_REVIEW, UPDATE)}}" method="post" role="form" id="update_review_form" class="grace-form review-form" data-reviews="{{$dataReviews ?? ''}}">
                    @csrf
                    @method('PUT')
                    <div class="grace-form-body review-body row col-12 pt-2 pb-4">
                        <input type="hidden" id="update_review_id" name="update_review_id">
                        {{-- Review Rating --}}
                        <div class="update-review-rating">
                            <div class="form-group">
                                <label class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(RATING)}}
                                </label>
                                <div id="rating_container" class="rate d-flex flex-row-reverse justify-content-end align-items-center"></div>
                            </div>
                            {{$errorRating}}
                        </div>

                        {{-- Review Title --}}
                        <div class="update-review-title">
                            <div class="form-outline">
                                <input type="text" name="update_review_title" id="update_review_title" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                <label for="update_review_title" class="form-label">
                                    <sup class="me-1">*</sup>Give your {{REVIEW_MODEL}} a {{TITLE}}
                                </label>
                            </div>
                            {{$errorTitle}}
                        </div>
                        {{-- Review Body Text --}}
                        <div class="update-review-body-text">
                            <div class="form-outline">
                                <textarea name="update_review_body_text" id="update_review_body_text" class="form-control fs-7 review-body-text" rows="6" minlength="2" maxlength="1500" aria-required="true"></textarea>
                                <label for="update_review_body_text" class="form-label textarea-label">
                                    <sup class="me-1">*</sup>Your {{ucfirst(REVIEW_MODEL)}}
                                </label>
                            </div>
                            {{$errorBodyText}}
                        </div>

                        {{-- Review Product Id --}}
                        <input type="hidden" name="update_review_product_id" id="update_review_product_id">

                        <div class="chars-counter"></div>
                    </div>

                    {{-- Save Changes Button --}}
                    @submitButton(SAVE_CHANGES)
                </form>
            </div>
        </div>
    </div>
</div>
