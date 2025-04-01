<tr id="row_{{$product->id}}" class="{{$product->{CATEGORIES_TABLE}?->isEmpty() || $product->{SUBCATEGORIES_TABLE}?->isEmpty() ? 'highlight-background' : ''}}">
    @checkRow($product->id)
    @loopIteration()
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
            <p><i>No Thumbnail Images</i></p>
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
        <p>@priceFormat($product->{OLD_PRICE})</p>
    </td>
    <td>
        <p>@priceFormat($product->{NEW_PRICE})</p>
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
        @if($product->{SUBCATEGORIES_TABLE}->isNotEmpty())
            <ul class="cell-menu overflow-auto">
                @foreach($product->{SUBCATEGORIES_TABLE} as $subcategory)
                    <li>{{ $subcategory->{NAME} }}</li>
                @endforeach
            </ul>
        @else
            <p><i>No {{ucfirst(SUBCATEGORIES_TABLE)}}</i></p>
        @endif
    </td>
    <td>
        <p>{{$product->{STATUS} === 1 ? 'Available' : 'Not Available'}}</p>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if($product->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_PRODUCT)}}" class="restore-product-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_PRODUCT, $product->id)}}" data-id="{{$product->id}}" data-name="{{ $product->{NAME} }}">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_PRODUCT_TITLE}}" class="edit-product-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_product_modal" data-route="{{route(EDIT_PRODUCT, $product->id)}}" data-main_image="{{imageSource($product, MAIN_IMAGE)}}" data-thumb_images="@foreach($product->{THUMB_IMAGES} as $thumb_image){{imageSource($thumb_image, THUMB_IMAGE)}} @endforeach">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>
            @endif
            <button type="button" role="button" title="{{capitalizeAll($product->trashed() ? DELETE_PRODUCT : REMOVE_PRODUCT)}}" class="delete-product-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_PRODUCT, $product->id)}}" data-id="{{$product->id}}" data-name="{{ $product->{NAME} }}">
                <i class="{{$product->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
            </button>
        </div>
    </td>
</tr>
