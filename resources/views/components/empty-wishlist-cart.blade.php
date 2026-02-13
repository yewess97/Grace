@props(['collection'])

<div class="col">
    <div class="empty-wishlist-cart d-grid place-items-center gap-5">
        <h2 class="empty-wishlist-cart-title d-grid place-items-center gap-3 fs-1 fw-600">
            <span>Your</span>
            <span>{{ucfirst($collection)}}</span>
            <span>is currently</span>
            <span>Empty</span>
        </h2>
        <a href="{{route(PRODUCTS_LIST)}}" type="button" role="link" class="btn">
            {{pluralize(ADD_PRODUCT_TITLE)}} Now
        </a>
    </div>
</div>
