<div id="add_product_modal" class="modal admin-modal top fade" tabindex="-1" aria-labelledby="add_product" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <article class="modal-header">
                <h2 id="add_product" class="modal-title fs-6 fw-600">{{ADD_PRODUCT_TITLE}}</h2>
                @modalCloseBtn()
            </article>
            <article class="modal-body pb-0">
                <form action="{{route(CREATE_UPDATE_PRODUCT, ADD)}}" method="post" role="form" id="add_product_form" class="grace-form" enctype="multipart/form-data" data-main="{{route(ADMIN_PRODUCTS_ROUTE)}}" data-loading_spinner="{{imageSource('loading2.png')}}">
                    @csrf
                    <div class="grace-form-body row col-12 pt-2 pb-4">
                        {{-- Product Name --}}
                        <div class="add-product-name col-12">
                            <div class="form-outline mb-2">
                                <input type="text" name="add_product_name" id="add_product_name" class="form-control fs-7 rounded-2" min="3" max="100" aria-required="true">
                                <label for="add_product_name" class="form-label">
                                    <sup class="me-1">*</sup>{{ucfirst(NAME)}}
                                </label>
                            </div>
                            {{$add_product_error(NAME)}}
                        </div>

                        {{-- Product Short Description --}}
                        <div class="add-product-short-description">
                            <div class="form-group col-12 mb-2">
                                <label for="add_product_short_description" class="form-label textarea-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(SHORT_DESCRIPTION)}}
                                </label>
                                <textarea role="textbox" name="add_product_short_description" id="add_product_short_description" class="text-editor form-control" minlength="5" maxlength="1000" aria-required="true"></textarea>
                            </div>
                            {{$add_product_error(SHORT_DESCRIPTION)}}
                        </div>

                        {{-- Product Long Description --}}
                        <div class="add-product-long-description">
                            <div class="form-group col-12 mb-2">
                                <label for="add_product_long_description" class="form-label textarea-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(LONG_DESCRIPTION)}}
                                </label>
                                <textarea role="textbox" name="add_product_long_description" id="add_product_long_description" class="text-editor form-control" minlength="10" maxlength="10000" aria-required="true"></textarea>
                            </div>
                            {{$add_product_error(LONG_DESCRIPTION)}}
                        </div>

                        {{-- Product Main Image --}}
                        <div class="add-product-main-image">
                            <div class="form-group col-12 mb-2">
                                <label for="add_product_main_image" class="form-label">
                                    <sup class="me-1">*</sup>{{capitalizeAll(MAIN_IMAGE)}}
                                </label>
                                <div id="add_product_main_image_container" class="mx-auto mt-2 border overflow-auto"></div>
                                <input type="hidden" name="add_product_main_image">
                            </div>
                            {{$add_product_error(MAIN_IMAGE)}}
                        </div>

                        {{-- Product Thumbnail Images --}}
                        <div class="add-product-thumb-images">
                            <div class="form-group col-12">
                                <label for="add_product_thumb_image" class="form-label">Thumbnail Images (optional)</label>
                                <div id="add_thumb_images" class="product-thumb-images mx-auto mt-2 border overflow-auto"></div>
                            </div>
                        </div>

                        {{-- Product Related Categories & Related Subcategories & Sizes --}}
                        <div class="row col-12 align-items-center gap-2 gap-lg-0">
                            <!-- Product Related Categories -->
                            <div class="add-product-related-categories col-12 col-lg-4 pe-lg-2">
                                <div class="form-group position-relative mb-2">
                                    <label for="add_product_related_categories" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{capitalizeAll(RELATED_CATEGORIES)}}
                                    </label>
                                    <select name="add_product_related_categories[]" id="add_product_related_categories" multiple="multiple" aria-required="true">
                                        @foreach ($categories as $category)
                                            <option value="{{$category->id}}">{{ $category->{NAME} }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="add_product_related_categories[]">
                                </div>
                                {{$add_product_error(RELATED_CATEGORIES)}}
                            </div>

                            {{-- Product Related Subcategories --}}
                            <div class="add-product-related-subcategories col-12 col-lg-4 px-lg-2">
                                <div class="form-group position-relative mb-2">
                                    <label for="add_product_related_subcategories" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{capitalizeAll(RELATED_SUBCATEGORIES)}}
                                    </label>
                                    <select name="add_product_related_subcategories[]" id="add_product_related_subcategories" multiple="multiple" aria-required="true">
                                        @foreach ($subcategories as $subcategory)
                                            <option value="{{$subcategory->id}}">{{ $subcategory->{NAME} }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="add_product_related_subcategories[]">
                                </div>
                                {{$add_product_error(RELATED_SUBCATEGORIES)}}
                            </div>

                            {{-- Product Sizes --}}
                            <div class="add-product-sizes col-12 col-lg-4 ps-lg-2">
                                <div class="form-group position-relative mb-2">
                                    <label for="add_product_sizes" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>{{ucfirst(SIZES)}}
                                    </label>
                                    <select name="add_product_sizes[]" id="add_product_sizes" multiple="multiple" aria-required="true">
                                        @foreach ($sizes as $size => $value)
                                            <option value="{{$value}}">{{$size}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="add_product_sizes[]" id="add_product_sizes_input">
                                </div>
                                {{$add_product_error(SIZES)}}
                            </div>
                        </div>

                        {{-- Product Old & New Prices --}}
                        <div class="row col-12 align-items-end gap-2 gap-lg-0">
                            {{-- Product Old Price --}}
                            <div class="add-product-old-price col-12 col-lg-6 pe-lg-2">
                                <div class="form-group mb-2">
                                    <label for="add_product_old_price" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeAll(OLD_PRICE)}}
                                    </label>
                                    <input type="text" inputmode="decimal" name="add_product_old_price" id="add_product_old_price" class="form-control fs-7 rounded-2" min="1" max="7" readonly="readonly">
                                </div>
                                {{$add_product_error(OLD_PRICE)}}
                            </div>

                            {{-- Product New Price --}}
                            <div class="add-product-new-price col-12 col-lg-6 ps-lg-2">
                                <div class="form-outline mb-2">
                                    <input type="text" inputmode="decimal" name="add_product_new_price" id="add_product_new_price" class="form-control fs-7 rounded-2" min="1" max="7" aria-required="true">
                                    <label for="add_product_new_price" class="form-label">
                                        <sup class="me-1">*</sup>{{capitalizeAll(NEW_PRICE)}}
                                    </label>
                                </div>
                                {{$add_product_error(NEW_PRICE)}}
                            </div>
                        </div>

                        {{-- Product Quantity & Availability --}}
                        <div class="row col-12 align-items-end gap-3 gap-lg-0">
                            {{-- Product Quantity --}}
                            <div class="add-product-quantity col-12 col-lg-6 pe-lg-2">
                                <div class="form-outline mb-2">
                                    <input type="text" inputmode="numeric" name="add_product_quantity" id="add_product_quantity" class="form-control fs-7 rounded-2" min="1" aria-required="true">
                                    <label for="add_product_quantity" class="form-label">
                                        <sup class="me-1">*</sup>{{ucfirst(QUANTITY)}}
                                    </label>
                                </div>
                                {{$add_product_error(QUANTITY)}}
                            </div>

                            {{-- Product Availability --}}
                            <div class="add-product-availability col-12 col-lg-6 ps-lg-2">
                                <div class="form-group position-relative mb-2">
                                    <label for="add_product_status" class="label-select position-absolute user-select-none pe-none">
                                        <sup class="me-1">*</sup>Stock {{ucfirst(STATUS)}}
                                    </label>
                                    <select name="add_product_status" id="add_product_status" class="form-select" aria-required="true">
                                        <option value="1" selected="selected">Available</option>
                                    </select>
                                </div>
                                {{$add_product_error(STATUS)}}
                            </div>
                        </div>

                        {{-- Check Background --}}
                        <x-actions.check-image-background/>
                    </div>

                    {{-- Add Button --}}
                    @submitButton(ADD)
                </form>
            </article>
        </div>
    </div>
</div>
