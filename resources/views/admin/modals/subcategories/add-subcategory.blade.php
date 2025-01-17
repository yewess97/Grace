<div id="add_subcategory_modal" class="modal admin-modal top fade" tabindex="-1" aria-labelledby="add_subcategory" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <h2 id="add_subcategory" class="modal-title fs-6 fw-600">{{ADD_SUBCATEGORY_TITLE}}</h2>
                @modalCloseBtn()
            </div>
            <div class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_SUBCATEGORY, ADD)}}" method="post" role="form" id="add_subcategory_form" class="grace-form" enctype="multipart/form-data">
                    @csrf
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        {{-- Subcategory Name --}}
                        <div class="add-subcategory-name col-12">
                            <div class="form-outline mb-2">
                                <input type="text" name="add_subcategory_name" id="add_subcategory_name" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                                <label for="add_subcategory_name" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(NAME)}}
                                </label>
                            </div>
                            {{$add_subcategory_error(NAME)}}
                        </div>
                        {{-- Subcategory Main Image --}}
                        <div class="add-subcategory-main-image">
                            <div class="form-group col-12 mb-2">
                                <label for="add_subcategory_main_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(MAIN_IMAGE)}}
                                </label>
                                <div id="add_subcategory_main_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="add_subcategory_main_image">
                            </div>
                            {{$add_subcategory_error(MAIN_IMAGE)}}
                        </div>
                        {{-- Subcategory Related Categories --}}
                        <div class="add-subcategory-related-categories col-12">
                            <div class="form-group position-relative mb-2">
                                <label for="add_subcategory_related_categories" class="label-select position-absolute user-select-none pe-none">
                                    <sup class="me-1">*</sup>{{capitalizeAll(RELATED_CATEGORIES)}}
                                </label>
                                <select name="add_subcategory_related_categories[]" id="add_subcategory_related_categories" multiple="multiple" aria-required="true">
                                    @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{ $category->{NAME} }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="add_subcategory_related_categories[]">
                            </div>
                            {{$add_subcategory_error(RELATED_CATEGORIES)}}
                        </div>
                    </div>

                    {{-- Add Button --}}
                    @submitButton(ADD)
                </form>
            </div>
        </div>
    </div>
</div>
