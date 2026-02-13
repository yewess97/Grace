<div class="box-content border rounded">
    <form action="{{route(CREATE_UPDATE_CART, UPDATE)}}" method="post" role="form" class="update-cart-form">
        @csrf
        {{-- Cart Content --}}
        @include(CART_CONTENT_PARTIAL, [USER_CART_ITEMS => $user_cart_items])

        {{-- Cart Beneath Buttons --}}
        <x-wishlist-cart-beneath-buttons collection="{{CART_MODEL}}"/>
    </form>
    <form method="post" id="cart_product_remove_form" class="delete-cart-form d-none" aria-describedby="cart_product_remove">
        @csrf
        @method(strtoupper(DELETE))
    </form>
</div>

{{-- Cart Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($user_cart_items, CART_MODEL)</div>
