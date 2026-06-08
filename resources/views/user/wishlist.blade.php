@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Wishlist Breadcrumb --}}
    {{
        breadcrumb([
            ['title' => ucfirst(PROFILE), 'url' => route(PROFILE)],
            ['title' => 'My '.ucfirst(WISHLIST_MODEL)],
        ])
    }}

    {{-- Wishlist Main --}}
    <main role="main" class="wishlist-main py-6">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                @if (session()->has(EMPTY_WISHLIST))
                    <x-wishlist-cart.empty collection="{{WISHLIST_MODEL}}"/>
                @else
                    <div class="main-sides row col-12">
                        <!----======= Left Side =======---->
                        <section class="wishlist pagination-container">
                            @include(WISHLIST_PAGINATION, [USER_WISHLIST_ITEMS => $user_wishlist_items])
                        </section>
                    </div>
                @endif
            </div>
        </div>
    </main>

@endsection
