<div class="box-content border rounded">
    {{-- Wishlist Content --}}
    @include(WISHLIST_CONTENT_PARTIAL, [USER_WISHLIST_ITEMS => $user_wishlist_items])

    {{-- Wishlist Beneath Buttons --}}
    <x-wishlist-cart-beneath-buttons collection="{{WISHLIST_MODEL}}"/>

    <form method="post" id="wishlist_product_remove_form" class="delete-wishlist-form d-none" aria-describedby="wishlist_product_remove">
        @csrf
        @method(strtoupper(DELETE))
    </form>
</div>

{{-- Wishlist Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($user_wishlist_items, WISHLIST_MODEL)</div>
