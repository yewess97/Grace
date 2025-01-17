<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Short Description", "Long Description", "Main Image", "Thumbnail Images", "Sizes", "Old Price", "New Price", "Quantity", "Related Categories", "Related Subcategories", "Stock Status")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse($products as $key => $product)
            <tr>
                @checkRow($product->id)
                @loopIteration($products->firstItem())
                <td>
                    <div class="truncate">
                        <p>{{ $product->{NAME} }}</p>
                    </div>
                </td>
                <td>
                    <div class="truncate">
                        <p>{{ $product->{SHORT_DESCRIPTION} }}</p>
                    </div>
                </td>
                <td>
                    <div class="truncate">
                        <p>{{ $product->{LONG_DESCRIPTION} }}</p>
                    </div>
                </td>
                <td>
                    <div class="main-image mx-auto">
                        <img src="{{imageSource($product, MAIN_IMAGE)}}" alt="{{ $product->{NAME} }}">
                    </div>
                </td>
                <td>
                    @if ($product->{THUMB_IMAGES}->isNotEmpty())
                        <div id="admin_product_thumb_images_carousel{{$product->id}}" class="admin-carousel carousel slide carousel-fade" data-mdb-ride="carousel" data-mdb-interval="false">
                            <ul class="carousel-inner">
                                @foreach($product->{THUMB_IMAGES} as $thumb_image)
                                    <li class="carousel-item admin-product-imgs">
                                        <img src="{{imageSource($thumb_image, THUMB_IMAGE)}}" alt="{{ $product->{NAME} }}">
                                    </li>
                                @endforeach
                            </ul>
                            <button type="button" role="button" title="Previous" class="carousel-control-prev position-absolute top-50" data-mdb-target="#admin_product_thumb_images_carousel{{$product->id}}" data-mdb-slide="prev">
                                <i class="fa-solid fa-angle-left" aria-hidden="true"></i>
                            </button>
                            <button type="button" role="button" title="Next" class="carousel-control-next position-absolute top-50" data-mdb-target="#admin_product_thumb_images_carousel{{$product->id}}" data-mdb-slide="next">
                                <i class="fa-solid fa-angle-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    @else
                        <p><i>No Thumbnail images</i></p>
                    @endif
                </td>
                <td>
                    <ul class="cell-menu overflow-auto">
                        @foreach (productSizes($product) as $size)
                            <li>{{$size}}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <p>@price($product->{OLD_PRICE})</p>
                </td>
                <td>
                    <p>@price($product->{NEW_PRICE})</p>
                </td>
                <td>
                    <p>{{ $product->{QUANTITY} }}</p>
                </td>
                <td>
                    <ul class="cell-menu overflow-auto">
                        @foreach($product->{CATEGORIES_TABLE} as $category)
                            <li>{{ $category->{NAME} }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <ul class="cell-menu overflow-auto">
                        @foreach($product->{SUBCATEGORIES_TABLE} as $subcategory)
                            <li>{{ $subcategory->{NAME} }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <p>{{$product->{STATUS} === 1 ? 'Available' : 'Not Available'}}</p>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <button type="button" role="button" title="{{EDIT_PRODUCT_TITLE}}" class="btn edit-btn edit-product-btn" data-mdb-toggle="modal" data-mdb-target="#edit_product_modal" data-route="{{route(EDIT_PRODUCT, $product->id)}}" data-main_image="{{imageSource($product, MAIN_IMAGE)}}" data-thumb_images="@foreach($product->{THUMB_IMAGES} as $thumb_image){{imageSource($thumb_image, THUMB_IMAGE)}} @endforeach">
                            {{ucfirst(EDIT)}}
                        </button>
                        <button type="button" role="button" title="{{capitalizeAll(DELETE_PRODUCT)}}" class="btn delete-btn delete-product-btn" data-route="{{route(DELETE_PRODUCT, $product->id)}}" data-name="{{ $product->{NAME} }}">
                            {{ucfirst(DELETE)}}
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            @noResults(PRODUCTS_TABLE, 11)
        @endforelse
        </tbody>
    </table>
</div>

<div class="table-pagination col-12 pt-4">@pagination($products, ADMIN_PRODUCTS_ROUTE)</div>
