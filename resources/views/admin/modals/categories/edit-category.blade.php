<div id="edit_category_modal" class="modal admin-modal admin-edit-modal top fade" tabindex="-1" aria-labelledby="edit_category" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <article class="modal-header">
                <h2 id="edit_category" class="modal-title fs-6 fw-600">{{EDIT_CATEGORY_TITLE}}</h2>
                @modalCloseBtn()
            </article>
            <article class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_CATEGORY, UPDATE)}}" method="post" role="form" id="update_category_form" class="grace-form" enctype="multipart/form-data" data-main="{{route(ADMIN_CATEGORIES_ROUTE)}}" data-loading_spinner="{{imageSource('loading2.png')}}">
                    @csrf
                    @method('PUT')
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        <input type="hidden" name="update_category_id" id="update_category_id">
                        {{-- Category Name --}}
                        <div class="update-category-name col-12">
                            <div class="form-outline mb-2">
                                <input type="text" name="update_category_name" id="update_category_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                <label for="update_category_name" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(NAME)}}
                                </label>
                            </div>
                            {{$update_category_error(NAME)}}
                        </div>

                        {{-- Category Main Image --}}
                        <div class="update-category-main-image">
                            <div class="form-group col-12 mb-2">
                                <label for="update_category_main_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(MAIN_IMAGE)}}
                                </label>
                                <div id="update_category_main_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="update_category_main_image">
                                <div id="update_category_main_image_preview"></div>
                            </div>
                            {{$update_category_error(MAIN_IMAGE)}}
                        </div>

                        {{-- Category Banner Image --}}
                        <div class="update-category-banner-image">
                            <div class="form-group col-12 mb-2">
                                <label for="update_category_banner_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(BANNER_IMAGE)}}
                                </label>
                                <div id="update_category_banner_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="update_category_banner_image">
                                <div id="update_category_banner_image_preview"></div>
                            </div>
                            {{$update_category_error(BANNER_IMAGE)}}
                        </div>

                        {{-- Check Background --}}
                        <x-actions.check-image-background/>
                    </div>

                    {{-- Save Changes Button --}}
                    @submitButton(SAVE_CHANGES)
                </form>
            </article>
        </div>
    </div>
</div>
