<div class="shipping-address row row-cols-1 row-cols-md-2">
    @foreach($user_addresses as $user_address)
        <div class="shipping-address-card col px-2">
            <div class="card justify-content-center h-100 py-2 border border-1">
                <label for="add_order_address_id{{ $user_address->{ID} }}" class="h-0">
                    <input type="radio" role="radio" name="add_order_address_id" id="add_order_address_id{{ $user_address->{ID} }}" value="{{ $user_address->{ID} }}" aria-required="true">
                    <span class="custom-check position-absolute top-50" aria-labelledby="add_order_address_id{{ $user_address->{ID} }}"></span>
                </label>
                <ul role="list" class="list-group list-group-light list-group-small ms-5 border-0">
                    <li role="listitem" class="list-group-item px-3">{{ $user_address->{ADDRESS1} }}</li>
                    @isset($user_address->{ADDRESS2})
                        <li role="listitem" class="list-group-item px-3">{{ $user_address->{ADDRESS2} }}</li>
                    @endisset
                    <li role="listitem" class="list-group-item px-3">{{ $user_address->{CITY} }}, {{ $user_address->{STATE}.', ' ?? '' }}{{ $user_address->{COUNTRY} }}, {{ $user_address->{POSTAL_CODE} }}</li>
                </ul>
            </div>
        </div>
    @endforeach
</div>

<div class="table-pagination col-12 pt-4">@pagination($user_addresses, CHECKOUT)</div>
