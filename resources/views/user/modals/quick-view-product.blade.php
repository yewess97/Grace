<div id="product_quick_view_modal" class="modal product-quick-view-modal top fade" tabindex="-1" aria-labelledby="quick_view" aria-hidden="true" data-mdb-backdrop="true" data-mdb-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3">
            <article class="modal-header">
                <h2 id="quick_view" class="modal-title fs-6 fw-600">{{capitalizeAll(PRODUCT_MODEL.' '.QUICK_VIEW)}}</h2>
                @modalCloseBtn()
            </article>
            <article class="modal-body">
                <form action="{{route(CREATE_UPDATE_CART, ADD)}}" method="post" role="form" class="add-cart-form grace-form" data-loading_spinner="{{imageSource('loading.png')}}">
                    @csrf
                    <div class="grace-form-body row row-cols-1 row-cols-md-2 justify-content-evenly px-lg-0 py-3">
                        <input type="hidden" name="add_cart_product_id" id="add_cart_product_id">
                        {{-- Product Quick View Main Image --}}
                        <article class="product-quick-view-img col d-flex align-items-center rounded-3"></article>
                        <article class="product-quick-view-info col d-grid gap-3 mt-2">
                            {{-- Product Quick View Name & Price --}}
                            <div class="product-info-name-price d-grid gap-3">
                                {{-- Product Quick View Name --}}
                                <h2 class="product-info-name fs-6 fw-600"></h2>
                                {{-- Product Quick View Price --}}
                                <div class="product-info-price product-info-quick-view-price d-flex flex-wrap align-items-center w-100 m-0 overflow-hidden lh-1">
                                    <span class="new-price fs-6 fw-600"></span>
                                    <s class="old-price"></s>
                                </div>
                            </div>
                            {{-- Product Quick View Status --}}
                            <h2 class="product-info-availability fw-600">
                                <span>Availability:</span>
                                <span class="text-main"></span>
                            </h2>
                            {{-- Product Quick View Rating --}}
                            <div class="product-info-rating"></div>
                            {{-- Product Quick View Short Description --}}
                            <div class="product-info-short-description fs-7 text-muted"></div>
                            {{-- Product Quick View Buttons --}}
                            <div class="product-info-btns d-grid gap-3">
                                {{-- Product Quick View Select Size --}}
                                <div class="add-cart-product-size-quick-view col-8" data-product_size_quick_view="{{json_encode(PRODUCT_SIZE_ENUM, JSON_THROW_ON_ERROR)}}">
                                    <div class="form-group position-relative mb-2">
                                        <label for="add_cart_product_size_quick_view" class="label-select position-absolute user-select-none pe-none">
                                            <sup class="me-1">*</sup>{{ucfirst(SIZES)}}
                                        </label>
                                        <select name="add_cart_product_size_quick_view[]" id="add_cart_product_size_quick_view" class="product-size-quick-view" multiple="multiple" aria-required="true"></select>
                                        <input type="hidden" name="add_cart_product_size_quick_view[]">
                                    </div>
                                    {{formError(ADD, CART_MODEL, PRODUCT_SIZE_QUICK_VIEW)}}
                                </div>
                                {{-- Product Quick View Select Quantity --}}
                                <div class="cart-product-quantity">
                                    <div class="form-group d-flex align-items-center">
                                        <x-products.quantity id="add_cart_product_quantity_quick_view"/>
                                    </div>
                                    {{formError(ADD, CART_MODEL, PRODUCT_QUANTITY_QUICK_VIEW)}}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Add To/Remove From Wishlist Button --}}
                                    @submitButton(WISHLIST_MODEL, $product->id, QUICK_VIEW)
                                    {{-- Add To Cart Button --}}
                                    @submitButton(ADD_TO_CART)
                                </div>
                            </div>
                        </article>
                    </div>
                </form>
                {{-- Add or Remove Wishlist Form --}}
                <x-wishlist-cart.add-remove-wishlist-form product_id="{{$product->id}}" />
            </article>
        </div>
    </div>
</div>
