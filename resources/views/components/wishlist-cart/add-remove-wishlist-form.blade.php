@props([PRODUCT_ID])

<form action="{{route(CREATE_DELETE_WISHLIST)}}" method="post" role="form" class="add-remove-wishlist-form" data-loading_spinner="{{imageSource('loading.webp')}}">
    @csrf
    <input type="hidden" name="add_remove_wishlist_product_id" value="{{$product_id}}">
</form>
