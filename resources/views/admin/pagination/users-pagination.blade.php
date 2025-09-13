<div class="main-table admin-table table-responsive">
    <table role="table" class="table table-bordered align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Email", "Role", "Status", "Last Seen")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($users as $key => $user)
            @include(USER_ROW_PARTIAL, [USER_MODEL => $user])
        @empty
            @noResults(USERS_TABLE, 5)
        @endforelse
        </tbody>
    </table>
</div>

{{-- Users Pagination --}}
<div class="table-pagination col-12 pt-4">@pagination($users, $users_pagination_route)</div>
