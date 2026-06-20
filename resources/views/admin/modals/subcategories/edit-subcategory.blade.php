<div id="edit_subcategory_modal" class="modal admin-modal admin-edit-modal top fade" tabindex="-1" aria-labelledby="edit_subcategory" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <article class="modal-header">
                <h2 id="edit_subcategory" class="modal-title fs-6 fw-600">{{EDIT_SUBCATEGORY_TITLE}}</h2>
                @modalCloseBtn()
            </article>
            <article class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_SUBCATEGORY, UPDATE)}}" method="post" role="form" id="update_subcategory_form" class="grace-form" enctype="multipart/form-data" data-main="{{route(ADMIN_SUBCATEGORIES_ROUTE)}}" data-loading_spinner="{{imageSource('loading2.png')}}">
                    @csrf
                    @method('PUT')
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        <input type="hidden" name="update_subcategory_id" id="update_subcategory_id">
                        {{-- Subcategory Name --}}
                        <div class="update-subcategory-name col-12">
                            <div class="form-outline mb-2">
                                <input type="text" name="update_subcategory_name" id="update_subcategory_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                <label for="update_subcategory_name" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(NAME)}}
                                </label>
                            </div>
                            {{$update_subcategory_error(NAME)}}
                        </div>

                        {{-- Subcategory Main Image --}}
                        <div class="update-subcategory-main-image">
                            <div class="form-group col-12 mb-2">
                                <label for="update_subcategory_main_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(MAIN_IMAGE)}}
                                </label>
                                <div id="update_subcategory_main_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="update_subcategory_main_image">
                                <div id="update_subcategory_main_image_preview"></div>
                            </div>
                            {{$update_subcategory_error(MAIN_IMAGE)}}
                        </div>

                        {{-- Subcategory Related Categories --}}
                        <div class="update-subcategory-related-categories col-12" data-related_categories="{{json_encode($categories, JSON_THROW_ON_ERROR)}}">
                            <div class="form-group position-relative mb-2">
                                <label for="update_subcategory_related_categories" class="label-select position-absolute user-select-none pe-none">
                                    <sup class="me-1">*</sup>{{capitalizeAll(RELATED_CATEGORIES)}}
                                </label>
                                <select name="update_subcategory_related_categories[]" id="update_subcategory_related_categories" class="subcategory-related-categories" multiple="multiple" aria-required="true">
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{ $category->{NAME} }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="update_subcategory_related_categories[]">
                            </div>
                            {{$update_subcategory_error(RELATED_CATEGORIES)}}
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
