<div class="box-content border rounded">
    <form action="{{route(CREATE_UPDATE_CART, UPDATE)}}" method="post" role="form" class="update-cart-form">
        {{-- Cart Content --}}
        @include(CART_CONTENT_PARTIAL, [USER_CART_ITEMS => $user_cart_items])

        {{-- Cart Buttons --}}
        <article class="cart-buttons d-flex justify-content-between align-items-center border-bottom">
            <a href="{{route(PRODUCTS_LIST)}}" role="link" class="text-decoration-underline">
                Continue Shopping
            </a>
            <input type="submit" class="p-0 text-decoration-underline bg-transparent border-0" value="{{capitalizeAll(UPDATE.' '.CART_MODEL)}}">
            <a href="{{route(pluralize(DELETE_CART))}}" role="link" id="clear_cart" class="text-decoration-underline">Clear {{ucfirst(CART_MODEL)}}</a>
        </article>
    </form>
    <form method="post" id="cart_product_remove_form" class="delete-cart-form d-none" aria-describedby="cart_product_remove">
        @csrf
        @method(strtoupper(DELETE))
    </form>
</div>

{{-- Cart Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($user_cart_items, CART_MODEL)</div>
