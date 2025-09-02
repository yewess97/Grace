<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered address-table align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Address line 1", "Address line 2", "City", "State", "Country", "Postal Code")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($user_addresses as $key => $address)
            @include(ADDRESS_ROW_PARTIAL, [ADDRESS_MODEL => $address])
        @empty
            @noResults(ADDRESSES_TABLE, 6)
        @endforelse
        </tbody>
    </table>
</div>

{{-- User Addresses Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($user_addresses, (isAdminRoute() ? ADMIN_USER_ADDRESSES_ROUTE : USER_ADDRESSES))</div>
