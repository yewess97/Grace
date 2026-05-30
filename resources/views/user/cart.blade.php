@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('content')

    {{-- Cart Breadcrumb --}}
    {{
        breadcrumb([
            ['title' => ucfirst(PRODUCTS_TABLE), 'url' => route(PRODUCTS_LIST)],
            ['title' => 'My '.ucfirst(CART_MODEL)],
        ])
    }}

    {{-- Cart Main --}}
    <main role="main" class="cart-main py-6">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                @if (session()->has(EMPTY_CART))
                    <x-empty-wishlist-cart collection="{{CART_MODEL}}"/>
                @else
                    <div class="main-sides row col-12">
                        <!----======= Left Side =======---->
                        <section class="cart pagination-container col-9 px-sm-3">
                            @include(CART_PAGINATION, [USER_CART_ITEMS => $user_cart_items])
                        </section>

                        <!----======= Right Side =======---->
                        <aside class="summary col-3 px-sm-3">
                            <div class="summary-content box-content position-sticky border rounded">
                                {{-- Summary Title --}}
                                <article class="summary-title border-top border-bottom">
                                    <h2 class="fs-6 fw-600">Summary</h2>
                                </article>
                                {{-- Summary Body --}}
                                <ul role="list" class="summary-body d-grid">
                                    <li role="listitem" class="d-flex justify-content-between align-items-center">
                                        <span>Subtotal</span>
                                        <span class="cart-total-cost fw-600">@priceFormat($total_cost)</span>
                                    </li>
                                    <li role="listitem" class="d-flex justify-content-between align-items-center">
                                        <span>Shipping</span>
                                        <span class="fw-600">Free</span>
                                    </li>
                                </ul>
                                {{-- Summary Total --}}
                                <article class="summary-total d-flex justify-content-between align-items-center border-top">
                                    <div>
                                        <h2 class="fw-600">Total</h2>
                                        <p>(Including VAT)</p>
                                    </div>
                                    <span class="cart-total-cost fw-600">@priceFormat($total_cost)</span>
                                </article>
                                {{-- Summary Proceed to Checkout Button --}}
                                <a href="{{route(CHECKOUT)}}" type="button" role="link" class="btn btn-block mt-2">
                                    Proceed to {{ucfirst(CHECKOUT)}}
                                </a>
                            </div>
                        </aside>
                    </div>
                @endif
            </div>
        </div>
    </main>

@endsection
