@props([COMMON_COLLECTIONS])

<div class="offer-sale position-relative d-grid place-items-center fw-500 fst-italic text-center text-uppercase text-white">
    @foreach ($common_collections['navbar_offers'] as $offer)
        <p>{{$offer}}</p>
    @endforeach
</div>
