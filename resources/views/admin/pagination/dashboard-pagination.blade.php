<div class="main-table admin-table table-responsive h-fit-content">
    <table role="table" class="table align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Email", "Country", "Number of Orders")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse($users as $key => $user)
            <tr>
                @loopIteration()
                <td>
                    <p>{{ $user->{FULL_NAME} }}</p>
                </td>
                <td>
                    <p>{{ $user->{EMAIL} }}</p>
                </td>
                <td>
                    <ul class="cell-menu overflow-auto">
                        @foreach ($user->{ADDRESSES_TABLE} as $user_address)
                            <li>{{ $user_address->{COUNTRY} }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <p>{{$user->orders_count}}</p>
                </td>
            </tr>
        @empty
            @noResults(USERS_TABLE, 4)
        @endforelse
        </tbody>
    </table>
</div>

{{-- Dashboard Users Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($users, ADMIN_DASHBOARD_ROUTE)</div>
