@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Wishlist Main --}}
    <main role="main" class="cart-main py-6">
        <div class="container">
            <div id="cart_main" class="row justify-content-center align-items-center">
                @if (Session()->has(EMPTY_WISHLIST))
                    <div class="col">
                        <div class="empty-cart d-grid place-items-center gap-5">
                            <h2 class="empty-cart-title d-grid place-items-center gap-3 fs-1 fw-600">
                                <span>Your</span>
                                <span>{{ucfirst(WISHLIST_MODEL)}}</span>
                                <span>is currently</span>
                                <span>Empty</span>
                            </h2>
                            <a href="{{route(PRODUCTS_LIST)}}" type="button" role="link" class="btn">
                                {{pluralize(ADD_PRODUCT_TITLE)}} Now
                            </a>
                        </div>
                    </div>
                @else
                    <div class="main-sides row col-12">
                        <!----======= Left Side =======---->
                        <section class="cart pagination-container col-9 px-sm-3">
                            @include(CART_PAGINATION, [USER_CART_ITEMS => $user_cart_items])
                        </section>
                    </div>
                @endif
            </div>
        </div>
    </main>

@endsection
