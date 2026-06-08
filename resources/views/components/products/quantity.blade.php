@props(['id', 'class' => null, 'cart_item' => null])

{{-- Decrement Product Quantity --}}
<button type="button" role="button" title="Decrement" class="btn decrement-btn quantity-btn d-grid place-items-center p-0 border rounded-0">
    -
</button>

{{-- Product Quantity Number --}}
<label for="{{$id}}"></label>
<input type="number" inputmode="numeric" name="{{$id}}" id="{{$id}}" title="{{capitalizeAll(PRODUCT_QUANTITY)}}" class="{{$class ?? null}} quantity-input p-0 fs-7 text-center border-top border-bottom rounded-0" min="1" value="{{ $cart_item->{PRODUCT_QUANTITY} ?? 1 }}">

{{-- Increment Product Quantity --}}
<button type="button" role="button" title="Increment" class="btn increment-btn quantity-btn d-grid place-items-center p-0 border rounded-0">
    +
</button>
