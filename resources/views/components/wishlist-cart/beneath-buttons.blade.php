@props(['collection'])

<article class="beneath-buttons d-flex justify-content-between align-items-center border-bottom">
    <a href="{{route(PRODUCTS_LIST)}}" role="link" class="text-decoration-underline">
        Continue Shopping
    </a>
    @if ($collection === CART_MODEL)
        <input type="submit" class="p-0 text-decoration-underline bg-transparent border-0" value="{{capitalizeAll(UPDATE.' '.CART_MODEL)}}">
    @endif
    <a href="{{route(DELETE.'_'.pluralize($collection))}}" role="link" id="clear_{{$collection}}" class="text-decoration-underline">Clear {{ucfirst($collection)}}</a>
</article>
