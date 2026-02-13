@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Wishlist Main --}}
    <main role="main" class="wishlist-main py-6">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                @if (Session()->has(EMPTY_WISHLIST))
                    <x-empty-wishlist-cart collection="{{WISHLIST_MODEL}}"/>
                @else
                    <div class="main-sides row col-12">
                        <!----======= Left Side =======---->
                        <section class="wishlist pagination-container col-9 px-sm-3">
                            @include(WISHLIST_PAGINATION, [USER_WISHLIST_ITEMS => $user_wishlist_items])
                        </section>
                    </div>
                @endif
            </div>
        </div>
    </main>

@endsection
