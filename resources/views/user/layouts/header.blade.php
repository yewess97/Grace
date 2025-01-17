<header role="banner" class="header">
    <div class="container">
        <div class="row col-12 justify-content-md-between align-items-center">
            {{-- Header Responsive Nav Menu --}}
            <article class="header-nav-menu col d-none">
                {{-- Header Nav Menu Toggler --}}
                <div role="button" class="nav-menu-toggler fs-4 text-black" tabindex="0" aria-label="Open navigation menu" aria-expanded="false" aria-controls="header_nav_menu">
                    <i class="ti ti-menu"></i>
                </div>

                {{-- Header Nav Menu Content --}}
                <nav role="navigation" id="header_nav_menu" class="nav-menu position-fixed top-0 h-100 overflow-auto bg-white" aria-label="{{ucfirst(USER_MODEL)}} navigation">
                    <div class="container">
                        <div class="nav-menu-content d-flex flex-column">
                            {{-- Nav Menu Header --}}
                            <header role="banner" class="nav-menu-header nav-offer position-relative">
                                {{-- Offers Sales --}}
                                <x-offers-sales :common_collections="$common_collections"/>

                                {{-- Close Button --}}
                                @menuCloseBtn("header_nav_menu")
                            </header>

                            {{-- Nav Menu Main Body --}}
                            <main role="main" class="nav-menu-main row">
                                {{-- First Nav Menu List --}}
                                <article class="nav-menu-list">
                                    {{-- Nav Menu List Header --}}
                                    <a href="#nav_menu_menu_list" role="button" class="nav-menu-list-header box-content collapsed d-flex align-items-center fs-6 fw-600" data-mdb-toggle="collapse" aria-expanded="false">
                                        <i class="ti ti-menu nav-menu-list-icon"></i>
                                        <span class="nav-menu-list-title">Menu</span>
                                    </a>
                                    {{-- Nav Menu List Content --}}
                                    <ul role="list" id="nav_menu_menu_list" class="nav-menu-list-content row bg-white collapse">
                                        <li role="listitem" class="nav-menu-list-item">Home</li>

                                        @foreach ($common_collections['navbar_dropdowns'] as $navbar_dropdown)
                                            <li role="listitem" class="nav-menu-list-item">
                                                <a href="#nav_submenu_{{ $navbar_dropdown->{TITLE} }}_list" role="button" class="nav-submenu-list-header col-12 d-flex justify-content-between align-items-center collapsed" data-mdb-toggle="collapse" aria-expanded="false">
                                                    <span class="nav-submenu-item-title" data-mdb-slim="false">
                                                        {{ucfirst($navbar_dropdown->{TITLE})}}
                                                    </span>
                                                    <i class="ti ti-angle-down nav-submenu-item-rotate-icon"></i>
                                                </a>
                                                <ul role="list" id="nav_submenu_{{ $navbar_dropdown->{TITLE} }}_list" class="nav-submenu-list-content row collapse justify-content-center align-items-center">
                                                    @foreach ($navbar_dropdown->collection as $collection)
                                                        <li role="listitem" class="nav-submenu-list-item col-12 box-content rounded-3">
                                                            <a href="{{route($navbar_dropdown->route_name, $collection->{SLUG})}}" role="link" class="d-grid place-items-center gap-2">
                                                                <div class="dropdown-img img-hover-effect position-relative overflow-hidden">
                                                                    <img src="{{imageSource($collection, MAIN_IMAGE)}}" alt="{{ $collection->{NAME} }}" class="w-100">
                                                                </div>
                                                                <h3 class="dropdown-name fw-500 text-capitalize lh-1">
                                                                    {{ $collection->{NAME} }}
                                                                </h3>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach

                                        <li role="listitem" class="nav-menu-list-item">
                                            <a href="#nav_submenu_more_list" role="button" class="nav-submenu-list-header col-12 d-flex justify-content-between align-items-center collapsed" data-mdb-toggle="collapse" aria-expanded="false">
                                                <span class="nav-submenu-item-title" data-mdb-slim="false">More</span>
                                                <i class="ti ti-angle-down nav-submenu-item-rotate-icon"></i>
                                            </a>
                                            <ul role="list" id="nav_submenu_more_list" class="nav-submenu-list-content row collapse justify-content-center align-items-center">
                                                @foreach ($common_collections['navbar_items'] as $navbar_item)
                                                    <li role="listitem" class="nav-submenu-list-item col-12 box-content rounded-3">
                                                        <a href="{{route($navbar_item->route_name)}}" role="link" class="d-grid place-items-center gap-2">
                                                            <h3 class="dropdown-name fw-500 text-capitalize lh-1">
                                                                {{capitalizeAll($navbar_item->route_name)}}
                                                            </h3>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                </article>

                                {{-- Second Nav Menu List --}}
                                <article class="nav-menu-list">
                                    {{-- Nav Menu List Header --}}
                                    <a href="#nav_menu_top_{{CATEGORIES_TABLE}}_list" role="button" class="nav-menu-list-header box-content collapsed d-flex align-items-center fs-6 fw-600" data-mdb-toggle="collapse" aria-expanded="false">
                                        <i class="ti ti-menu-alt nav-menu-list-icon"></i>
                                        <span class="nav-menu-list-title">Top {{ucfirst(CATEGORIES_TABLE)}}</span>
                                    </a>
                                    {{-- Nav Menu List Content --}}
                                    <ul role="list" id="nav_menu_top_{{CATEGORIES_TABLE}}_list" class="nav-menu-list-content row bg-white collapse">
                                        {{-- Top Wear List --}}
                                        <li role="listitem" class="nav-menu-list-item">
                                            <a href="#nav_submenu_top_wear_list" role="button" class="nav-submenu-list-header category-title menu-item col-12 position-relative d-flex justify-content-between align-items-center gap-2 collapsed" data-mdb-toggle="collapse" aria-expanded="false">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="nav-submenu-item-title" data-mdb-slim="false">Top Wear</span>
                                                    <span class="badge nav-submenu-item-badge position-relative d-flex justify-content-center align-items-center text-uppercase rounded">New</span>
                                                </div>
                                                <i class="ti ti-angle-down nav-submenu-item-rotate-icon"></i>
                                            </a>
                                            <ul role="list" id="nav_submenu_top_wear_list" class="nav-submenu-list-content row collapse align-items-center pt-2">
                                                @foreach ($aside_menus['top_wear'] as $title => $menu_items)
                                                    <li role="listitem" class="dropdown-item pt-0 pb-2">
                                                        <h2 class="dropdown-item-title fw-600 text-capitalize">{{$title}}</h2>
                                                        <ul role="list" class="dropdown-item-content d-grid gap-2">
                                                            @foreach ($menu_items as $item)
                                                                <li role="listitem" class="dropdown-item-point">
                                                                    <a href="javascript:;" class="dropdown-item-link text-capitalize">{{$item}}</a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endforeach
                                                <li role="listitem" class="dropdown-item">
                                                    <div class="top-wear-dropdown-image position-relative img-hover-effect cursor-pointer">
                                                        <img src="{{imageSource('site_banners/banner1.png')}}" alt="Banner 1">
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>

                                        {{-- Accessories --}}
                                        <li role="listitem" class="nav-menu-list-item">
                                            <a href="{{route(SUBCATEGORY_MODEL, 'accessories')}}" role="link">
                                                <p class="category-title">Accessories</p>
                                            </a>
                                        </li>

                                        {{-- Bottom Wear List --}}
                                        <li role="listitem" class="nav-menu-list-item">
                                            <a href="#nav_submenu_bottom_wear_list" role="button" class="nav-submenu-list-header category-title menu-item col-12 position-relative d-flex justify-content-between align-items-center gap-2 collapsed" data-mdb-toggle="collapse" aria-expanded="false">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="nav-submenu-item-title" data-mdb-slim="false">Bottom Wear</span>
                                                    <span class="badge nav-submenu-item-badge position-relative d-flex justify-content-center align-items-center text-uppercase rounded">Sale</span>
                                                </div>
                                                <i class="ti ti-angle-down nav-submenu-item-rotate-icon"></i>
                                            </a>
                                            <ul role="list" id="nav_submenu_bottom_wear_list" class="nav-submenu-list-content row collapse align-items-center pt-2">
                                                @foreach ($aside_menus['bottom_wear'] as $title => $menu_items)
                                                    <li role="listitem" class="dropdown-item pt-0 pb-2">
                                                        <h2 class="dropdown-item-title fw-600 text-capitalize">{{$title}}</h2>
                                                        <ul role="list" class="dropdown-item-content d-grid gap-2">
                                                            @foreach ($menu_items as $item)
                                                                <li role="listitem" class="dropdown-item-point">
                                                                    <a href="javascript:;" class="dropdown-item-link text-capitalize">{{$item}}</a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endforeach
                                                <li role="listitem" class="row align-items-center">
                                                    <div class="dropdown-item dropdown-item-image">
                                                        <div class="bottom-wear-dropdown-image position-relative img-hover-effect cursor-pointer">
                                                            <img src="{{imageSource('site_banners/banner2.png')}}" alt="Banner 2">
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-item dropdown-item-image">
                                                        <div class="bottom-wear-dropdown-image position-relative img-hover-effect cursor-pointer">
                                                            <img src="{{imageSource('site_banners/banner3.png')}}" alt="Banner 3">
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>

                                        {{-- Bags --}}
                                        <li role="listitem" class="nav-menu-list-item row">
                                            <a href="{{route(SUBCATEGORY_MODEL, 'bags')}}" role="link">
                                                <p class="category-title">Bags</p>
                                            </a>
                                        </li>

                                        {{-- Categories --}}
                                        @foreach ($common_collections[CATEGORIES_TABLE] as $category)
                                            <li role="listitem" class="nav-menu-list-item row">
                                                <a href="{{route(CATEGORY_MODEL, $category->{SLUG})}}" role="link">
                                                    <p class="main-category-title">{{ucfirst($category->{NAME})}}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </article>
                            </main>
                        </div>
                    </div>
                </nav>
            </article>

            {{-- Header Logo --}}
            <article class="header-logo col-2 d-flex justify-content-center">
                <a href="{{route('home')}}" role="link" class="d-block me-lg-5">
                    <img src="{{imageSource('logo.png')}}" alt="{{config('app.name')}} Logo">
                </a>
            </article>

            {{-- Header Products search --}}
            <article class="header-search products-search row col-5">
                <x-products-search/>
            </article>

            {{-- Header User's Account & Cart --}}
            <article class="header-user row col-5 justify-content-end gap-sm-2 position-relative">
                {{-- User's Account --}}
                <article class="header-user-account col-lg-5 dropdown">
                    {{-- Account --}}
                    <div role="button" id="user_account_menu" class="header-user-account-content row align-items-center dropdown-toggle" tabindex="0" aria-expanded="false" aria-haspopup="true" aria-controls="user_account_dropdown" data-mdb-toggle="dropdown">
                        <i class="fa-solid fa-user user-icon col-2 d-grid place-items-center me-3 fs-10 border rounded-circle"></i>
                        <div class="header-user-title row col gap-1">
                            <p class="fs-6 fw-500 text-black">My Account</p>
                            @guest()
                                <p>{{ucfirst(LOGIN)}}</p>
                            @else
                                <p>Hello @userFullName()</p>
                            @endguest
                        </div>
                    </div>

                    {{-- Account Menu --}}
                    <div id="user_account_dropdown" class="account-menu dropdown-menu position-absolute py-0 border rounded-3" aria-labelledby="user_account_menu">
                        @guest()
                            <a href="{{route(LOGIN)}}" role="link" class="d-block fs-7">{{ucfirst(LOGIN)}}</a>
                            <a href="{{route(REGISTER)}}" role="link" class="d-block fs-7">{{ucfirst(REGISTER)}}</a>
                        @else
                            <a href="{{route(PROFILE)}}" role="link" id="profile" class="d-block fs-7">My {{ucfirst(PROFILE)}}</a>
                            @admin()
                                <a href="{{route(ADMIN_DASHBOARD_ROUTE)}}" target="_blank" role="link" class="d-block fs-7">
                                    {{capitalizeAll(ADMIN_DASHBOARD_ROUTE)}}
                                </a>
                            @endadmin
                            <hr class="dropdown-divider m-0 text-danger">
                            <form action="{{route(LOGOUT)}}" method="post" role="form">
                                @csrf
                                <button type="submit" role="button" title="{{ucfirst(LOGOUT)}}" class="logout d-block w-100 fs-7 text-start border-0">{{ucfirst(LOGOUT)}}</button>
                            </form>
                        @endguest
                    </div>
                </article>

                {{-- User's Cart --}}
                <article class="header-user-cart col-lg-4 dropdown">
                    {{-- Cart --}}
                    <div role="button" id="user_cart_menu" class="header-user-cart-content row align-items-center dropdown-toggle" tabindex="0" aria-expanded="false" aria-haspopup="true" aria-controls="user_cart_dropdown" data-mdb-toggle="dropdown">
                        <div class="cart-icon col-2 position-relative d-grid place-items-center me-3 fs-10 border rounded-circle">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span class="cart-total position-absolute top-0 end-0 d-flex justify-content-center align-items-center rounded-circle text-white">
                                {{Session::has(EMPTY_CART) ? '0' : $user_cart_items->sum(PRODUCT_QUANTITY)}}
                            </span>
                        </div>
                        <div class="header-user-title row col gap-1">
                            <p class="fs-6 fw-500 text-black">My {{ucfirst(CART_MODEL)}}</p>
                            <span>@price($total_cost)</span>
                        </div>
                    </div>

                    {{-- Cart Menu --}}
                    <div id="user_cart_dropdown" class="cart-menu dropdown-menu position-absolute border rounded-3" aria-labelledby="user_cart_menu">
                        @if (Session::has(EMPTY_CART))
                            {{-- Cart Empty --}}
                            <div class="cart-empty d-grid place-items-center gap-1">
                                <i class="ti ti-bag opacity-25"></i>
                                <p>No {{PRODUCTS_TABLE}} in your {{CART_MODEL}}.</p>
                            </div>
                        @else
                            {{-- Cart Product Title --}}
                            <p class="cart-title px-3 py-2">
                                <span>There are {{$user_cart_items->sum(PRODUCT_QUANTITY)}} {{$user_cart_items->sum(PRODUCT_QUANTITY) > 1 ? PRODUCTS_TABLE : PRODUCT_MODEL}}</span>
                            </p>
                            {{-- Cart Details --}}
                            <ul role="list" class="cart-details border-top">
                                @foreach ($user_cart_items as $cart_item)
                                    <li role="listitem" class="cart-product position-relative d-flex justify-content-between align-items-center w-100">
                                        @if ($cart_item->{PRODUCT_MODEL}->{STATUS} === 0)
                                            <div class="product-not-available-overlay position-absolute"></div>
                                        @endif
                                        {{-- Cart Product Info --}}
                                        <a href="{{route(PRODUCT_DETAILS, $cart_item->{PRODUCT_MODEL}->{SLUG})}}" role="link" class="row align-items-center">
                                            <article class="cart-product-img col-2 me-2 rounded-2">
                                                <img src="{{imageSource($cart_item->{PRODUCT_MODEL}, MAIN_IMAGE)}}" alt="{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}">
                                            </article>
                                            <article class="cart-product-details row col gap-1">
                                                <h5 class="fw-500">{{ $cart_item->{PRODUCT_MODEL}->{NAME} }}</h5>
                                                <p>Size: {{$cart_item->selected_product_size}}</p>
                                                <p>
                                                    <span class="quantity">{{ $cart_item->{PRODUCT_QUANTITY} }} &times;</span>
                                                    <span class="price fs-6 fw-bold lh-sm">@price($cart_item->product->new_price)</span>
                                                </p>
                                            </article>
                                        </a>
                                        {{-- Remove Cart Product --}}
                                        <form action="{{route(DELETE_CART, $cart_item->id)}}" method="post" role="form" class="delete-cart-form">
                                            @csrf
                                            @method(strtoupper(DELETE))
                                            <button type="submit" role="button" title="Remove {{ucfirst(PRODUCT_MODEL)}}" data-tooltip="tooltip" data-mdb-placement="top" class="fs-6 bg-transparent text-danger border-0">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                            {{-- Cart Subtotal & Buttons --}}
                            <ul role="list" class="cart-subtotal-btns border-top">
                                <li role="listitem" class="cart-subtotal d-flex justify-content-between">
                                    <h3>Sub Total:</h3>
                                    <span>@price($total_cost)</span>
                                </li>
                                <li role="listitem" class="cart-btns d-flex gap-3 mt-3">
                                    <a href="{{route(CART_MODEL)}}" type="button" role="link" id="view_cart" class="btn w-100">
                                        View {{ucfirst(CART_MODEL)}}
                                    </a>
                                    <a href="{{route(CHECKOUT)}}" type="button" role="link" class="btn w-100">
                                        {{ucfirst(CHECKOUT)}}
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </article>
            </article>
        </div>
    </div>
</header>
