<div id="edit_product_modal" class="modal admin-modal admin-edit-modal top fade" tabindex="-1" aria-labelledby="edit_product" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <h2 id="edit_product" class="modal-title fs-6 fw-600">{{EDIT_PRODUCT_TITLE}}</h2>
                @modalCloseBtn()
            </div>
            <div class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_PRODUCT, UPDATE)}}" method="post" role="form" id="update_product_form" class="grace-form" enctype="multipart/form-data" data-main="{{route(ADMIN_PRODUCTS_ROUTE)}}">
                    @csrf
                    @method('PUT')
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        <input type="hidden" name="update_product_id" id="update_product_id">
                        {{-- Product Name --}}
                        <div class="update-product-name col-12">
                            <div class="form-outline mb-2">
                                <input type="text" name="update_product_name" id="update_product_name" class="form-control fs-7 rounded-2" min="3" max="100" aria-required="true">
                                <label for="update_product_name" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(NAME)}}
                                </label>
                            </div>
                            {{$update_product_error(NAME)}}
                        </div>

                        {{-- Product Short-Description --}}
                        <div class="update-product-short-description">
                            <div class="form-group col-12 mb-2">
                                <label for="update_product_short_description" class="form-label textarea-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(SHORT_DESCRIPTION)}}
                                </label>
                                <textarea role="textbox" name="update_product_short_description" id="update_product_short_description" class="text-editor form-control" minlength="5" maxlength="1000" aria-required="true"></textarea>
                            </div>
                            {{$update_product_error(SHORT_DESCRIPTION)}}
                        </div>

                        {{-- Product Long-Description --}}
                        <div class="update-product-long-description">
                            <div class="form-group col-12 mb-2">
                                <label for="update_product_long_description" class="form-label textarea-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(LONG_DESCRIPTION)}}
                                </label>
                                <textarea role="textbox" name="update_product_long_description" id="update_product_long_description" class="text-editor form-control" minlength="10" maxlength="10000" aria-required="true"></textarea>
                            </div>
                            {{$update_product_error(LONG_DESCRIPTION)}}
                        </div>

                        {{-- Product Main Image --}}
                        <div class="update-product-main-image">
                            <div class="form-group col-12 mb-2">
                                <label for="update_product_main_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(MAIN_IMAGE)}}
                                </label>
                                <div id="update_product_main_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="update_product_main_image">
                                <div id="update_product_main_image_preview"></div>
                            </div>
                            {{$update_product_error(MAIN_IMAGE)}}
                        </div>

                        {{-- Product Thumbnail Images --}}
                        <div class="update-product-thumb-images">
                            <div class="form-group col-12">
                                <label for="update_product_thumb_image" class="form-label">Thumbnail Images (optional)</label>
                                <div id="update_thumb_images" class="product-thumb-images mx-auto mt-2 border overflow-auto"></div>
                            </div>
                            <div id="update_product_thumb_images_preview"></div>
                        </div>

                        {{-- Product Related Categories & Related Subcategories & Sizes --}}
                        <div class="row col-12 align-items-center gap-2 gap-lg-0">
                            <!-- Product Related Categories -->
                            <div class="update-product-related-categories col-12 col-lg-4 pe-lg-2" data-related_categories="{{json_encode($categories, JSON_THROW_ON_ERROR)}}">
                                <div class="form-group position-relative mb-2">
                                    <label for="update_product_related_categories" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{capitalizeAll(RELATED_CATEGORIES)}}
                                    </label>
                                    <select name="update_product_related_categories[]" id="update_product_related_categories" class="product-related-categories" multiple="multiple" aria-required="true">
                                        @foreach ($categories as $category)
                                            <option value="{{$category->id}}">{{ $category->{NAME} }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="update_product_related_categories[]">
                                </div>
                                {{$update_product_error(RELATED_CATEGORIES)}}
                            </div>

                            {{-- Product Related Subcategories --}}
                            <div class="update-product-related-subcategories col-12 col-lg-4 px-lg-2" data-related_subcategories="{{json_encode($subcategories, JSON_THROW_ON_ERROR)}}">
                                <div class="form-group position-relative mb-2">
                                    <label for="update_product_related_subcategories" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{capitalizeAll(RELATED_SUBCATEGORIES)}}
                                    </label>
                                    <select name="update_product_related_subcategories[]" id="update_product_related_subcategories" class="product-related-subcategories" multiple="multiple" aria-required="true">
                                        @foreach ($subcategories as $subcategory)
                                            <option value="{{$subcategory->id}}">{{ $subcategory->{NAME} }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="update_product_related_subcategories[]">
                                </div>
                                {{$update_product_error(RELATED_SUBCATEGORIES)}}
                            </div>

                            {{-- Product Sizes --}}
                            <div class="update-product-sizes col-12 col-lg-4 ps-lg-2" data-sizes="{{json_encode($sizes, JSON_THROW_ON_ERROR)}}">
                                <div class="form-group position-relative mb-2">
                                    <label for="update_product_sizes" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{ucfirst(SIZES)}}
                                    </label>
                                    <select name="update_product_sizes[]" id="update_product_sizes" class="product-sizes" multiple="multiple" aria-required="true">
                                        @foreach ($sizes as $size => $value)
                                            <option value="{{$value}}">{{$size}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="update_product_sizes[]" id="update_product_sizes_input">
                                </div>
                                {{$update_product_error(SIZES)}}
                            </div>
                        </div>

                        {{-- Product Old & New Prices --}}
                        <div class="product-info row col-12 align-items-end gap-2 gap-lg-0">
                            {{-- Product Old Price --}}
                            <div class="update-product-old-price col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline mb-2">
                                    <input type="text" inputmode="decimal" name="update_product_old_price" id="update_product_old_price" class="form-control fs-7 rounded-2" min="1" max="7" aria-required="true">
                                    <label for="update_product_old_price" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeAll(OLD_PRICE)}}
                                    </label>
                                </div>
                                {{$update_product_error(OLD_PRICE)}}
                            </div>

                            {{-- Product New Price --}}
                            <div class="update-product-new-price col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline mb-2">
                                    <input type="text" inputmode="decimal" name="update_product_new_price" id="update_product_new_price" class="form-control fs-7 rounded-2" min="1" max="7" aria-required="true">
                                    <label for="update_product_new_price" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeAll(NEW_PRICE)}}
                                    </label>
                                </div>
                                {{$update_product_error(NEW_PRICE)}}
                            </div>

                            {{-- Product Quantity & Availability --}}
                            <div class="row col-12 align-items-end gap-3 gap-lg-0">
                                {{-- Product Quantity --}}
                                <div class="update-product-quantity col-12 col-lg-6 pe-lg-2">
                                    <div class="form-outline mb-2">
                                        <input type="text" inputmode="numeric" name="update_product_quantity" id="update_product_quantity" class="form-control fs-7 rounded-2" min="1" aria-required="true">
                                        <label for="update_product_quantity" class="form-label">
                                            <sup class="me-1">*</sup>{{ucfirst(QUANTITY)}}
                                        </label>
                                    </div>
                                    {{$update_product_error(QUANTITY)}}
                                </div>

                                {{-- Product Availability --}}
                                <div class="update-product-availability col-12 col-lg-6 ps-lg-2">
                                    <div class="form-group position-relative mb-2">
                                        <label for="update_product_status" class="label-select position-absolute user-select-none pe-none">
                                            <sup class="me-1">*</sup>Stock {{ucfirst(STATUS)}}
                                        </label>
                                        <select name="update_product_status" id="update_product_status" class="form-select" aria-required="true">
                                            <option value="0">Not Available</option>
                                            <option value="1">Available</option>
                                        </select>
                                    </div>
                                    {{$update_product_error(STATUS)}}
                                </div>
                            </div>
                        </div>

                        {{-- Check Background --}}
                        <x-check-image-background/>
                    </div>

                    {{-- Save Changes Button --}}
                    @submitButton(SAVE_CHANGES)
                </form>
            </div>
        </div>
    </div>
</div>
