<div id="add_category_modal" class="modal admin-modal top fade" tabindex="-1" aria-labelledby="add_category" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <h2 id="add_category" class="modal-title fs-6 fw-600">{{ADD_CATEGORY_TITLE}}</h2>
                @modalCloseBtn()
            </div>
            <div class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_CATEGORY, ADD)}}" method="post" role="form" id="add_category_form" class="grace-form" enctype="multipart/form-data" data-main="{{route(ADMIN_CATEGORIES_ROUTE)}}">
                    @csrf
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        {{-- Category Name --}}
                        <div class="add-category-name col-12">
                            <div class="form-outline mb-2">
                                <input type="text" name="add_category_name" id="add_category_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                <label for="add_category_name" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(NAME)}}
                                </label>
                            </div>
                            {{$add_category_error(NAME)}}
                        </div>

                        {{-- Category Main Image --}}
                        <div class="add-category-main-image">
                            <div class="form-group col-12 mb-2">
                                <label for="add_category_main_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(MAIN_IMAGE)}}
                                </label>
                                <div id="add_category_main_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="add_category_main_image">
                            </div>
                            {{$add_category_error(MAIN_IMAGE)}}
                        </div>

                        {{-- Category Banner Image --}}
                        <div class="add-category-banner-image">
                            <div class="form-group col-12 mb-2">
                                <label for="add_category_banner_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(BANNER_IMAGE)}}
                                </label>
                                <div id="add_category_banner_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="add_category_banner_image">
                            </div>
                            {{$add_category_error(BANNER_IMAGE)}}
                        </div>
                    </div>

                    {{-- Add Button --}}
                    @submitButton(ADD)
                </form>
            </div>
        </div>
    </div>
</div>
