<div id="wishlist_content">
    {{-- Wishlist Title --}}
    <article class="title d-flex justify-content-between align-items-center border-top border-bottom">
        <h2 class="fs-6 fw-600">My {{ucfirst(WISHLIST_MODEL)}}:</h2>
        <p class="count">
            <span class="counter">{{$wishlist_total_items}} Items</span>
        </p>
    </article>

    {{-- Wishlist Products --}}
    <ul role="list" class="products pagination-container">
        @foreach ($user_wishlist_items as $wishlist_item)
            <li role="listitem" id="item_{{$wishlist_item->id}}" class="product position-relative row justify-content-between align-items-center border-bottom">
                @if ($wishlist_item->{PRODUCT_MODEL}->{STATUS} === 0)
                    <div role="dialog" class="product-not-available-overlay position-absolute" aria-label="{{PRODUCT_MODEL}} Not Available Overlay"></div>
                @endif
                {{-- Wishlist Product Image & Info--}}
                <article class="product-img-info row col-12">
                    {{-- Wishlist Product Image --}}
                    <a href="{{route(PRODUCT_DETAILS, $wishlist_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="product-img d-flex justify-content-center align-items-center col-4">
                        <img src="{{imageSource($wishlist_item->{PRODUCT_MODEL}, MAIN_IMAGE)}}" alt="{{ $wishlist_item->{PRODUCT_MODEL}->{NAME} }}">
                    </a>

                    {{-- Wishlist Product Info --}}
                    <div class="product-info col-7 px-2">
                        <a href="{{route(PRODUCT_DETAILS, $wishlist_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="fs-sm-6 fw-600">{{ $wishlist_item->{PRODUCT_MODEL}->{NAME} }}</a>
                        <p>{{config('app.name')}} Store</p>
                    </div>
                </article>

                {{-- Wishlist Product Actions --}}
                <article class="product-actions d-grid place-items-center">
                    {{-- Add Product to Cart --}}
                    @if ($wishlist_item->{PRODUCT_MODEL}->{STATUS} === 1)
                        <form role="form" action="{{route(CREATE_UPDATE_CART, ADD)}}" method="post" class="add-cart-form">
                            @csrf
                            <input type="hidden" name="add_cart_product_id" value="{{$wishlist_item->product->id}}">
                            <button type="submit" role="button" title="{{capitalizeAll(ADD_TO_CART)}}" class="product-add-to-cart text-decoration-underline bg-transparent border-0" aria-label="{{capitalizeAll(ADD_TO_CART)}}">
                                {{capitalizeAll(ADD_TO_CART)}}
                            </button>
                        </form>
                    @else
                        <p>The {{ucfirst(PRODUCT_MODEL)}} is Out Of Stock</p>
                    @endif
                    {{-- Remove Wishlist Product --}}
                    <button type="button" role="button" title="Remove {{PRODUCT_MODEL}} from the {{WISHLIST_MODEL}}" class="product-remove text-decoration-underline bg-transparent border-0" data-route="{{route(DELETE_WISHLIST, $wishlist_item->id)}}" data-id="{{$wishlist_item->id}}">Remove</button>
                </article>

                {{-- Wishlist Item Price --}}
                <article class="product-total-price text-end">
                    <p class="new-price fs-6 fw-600">@priceFormat($wishlist_item->product->new_price)</p>
                    @oldprice ($wishlist_item->product->old_price, $wishlist_item->product->new_price)
                        <p class="old-price fs-7 text-decoration-line-through">@priceFormat($wishlist_item->product->old_price)</p>
                    @endoldprice
                </article>
            </li>
        @endforeach
    </ul>
</div>
