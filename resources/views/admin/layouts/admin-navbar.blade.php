<nav role="navigation" @if($responsive) id="admin_header_nav_menu" @endif @class([
        'nav-menu position-fixed top-0 h-100',
        'responsive-nav-menu overflow-auto' => $responsive,
        'default-nav-menu start-0' => !$responsive,
    ]) aria-label="{{ucfirst(ADMIN)}} navigation">
    <div class="container h-100">
        <div class="nav-menu-content d-flex flex-column justify-content-between h-100">
            @if (!$responsive)
                {{-- Nav Menu Open/CLose Icon --}}
                <span class="nav-menu-toggle position-absolute d-flex justify-content-center align-items-center p-2 fs-9 rounded-circle cursor-pointer" aria-label="Close or Open the navigation menu">
                    <i class="fa-solid fa-angle-right nav-menu-toggle-icon"></i>
                </span>
            @endif

            {{-- Nav Menu Header --}}
            <header role="banner" @class([
                    'nav-menu-header',
                    'd-flex justify-content-between align-items-center' => $responsive,
                ])>
                <a href="{{route(HOME)}}" role="link" class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-shop nav-menu-header-icon fs-5"></i>
                    <h2 role="heading" class="nav-menu-header-title fs-8 fw-600">{{config('app.name')}} Store</h2>
                </a>

                @if ($responsive)
                    @menuCloseBtn("admin_header_nav_menu")
                @endif
            </header>

            {{-- Nav Menu Main Body --}}
            <main role="main" class="nav-menu-main flex-grow-1 bg-transparent">
                {{-- Nav Menu List --}}
                <ul role="list" class="nav-menu-list row box-content bg-transparent">
                    {{-- Dashboard --}}
                    <x-admin-nav-item url="{{DASHBOARD}}" route_name="{{ADMIN_DASHBOARD_ROUTE}}" icon="ti ti-layout-grid2-alt"/>
                    {{-- Categories --}}
                    <x-admin-nav-item url="{{CATEGORIES_TABLE}}" route_name="{{ADMIN_CATEGORIES_ROUTE}}" icon="fa-solid fa-layer-group"/>
                    {{-- Subcategories --}}
                    <x-admin-nav-item url="{{SUBCATEGORIES_TABLE}}" route_name="{{ADMIN_SUBCATEGORIES_ROUTE}}" icon="fa-solid fa-shapes"/>
                    {{-- Products --}}
                    <x-admin-nav-item url="{{PRODUCTS_TABLE}}" route_name="{{ADMIN_PRODUCTS_ROUTE}}" icon="fa-solid fa-box-open"/>
                    {{-- Users --}}
                    <x-admin-nav-item url="{{USERS_TABLE}}" route_name="{{ADMIN_USERS_ROUTE}}" icon="fa-solid fa-users"/>
                    {{-- Orders --}}
                    <x-admin-nav-item id="{{ORDERS_TABLE}}_list" url="{{ORDERS_TABLE}}" icon="fa-solid fa-truck-fast" route_name="{{ADMIN_ORDERS_ROUTE}}" submenu="true" :all_table="\App\Models\Order::withTrashed()->get([STATUS])->unique(STATUS)" column_name="{{STATUS}}"/>
                    {{-- Reviews --}}
                    <x-admin-nav-item id="{{REVIEWS_TABLE}}_list" url="{{REVIEWS_TABLE}}" icon="fa-solid fa-star" route_name="{{ADMIN_REVIEWS_ROUTE}}" submenu="true" :all_table="\App\Models\Review::withTrashed()->get([RATING])->unique(RATING)" column_name="{{RATING}}"/>
                </ul>
            </main>

            {{-- Nav Menu Footer --}}
            <footer role="contentinfo" class="py-3">
                <div @class([
                        'nav-menu-footer px-3 cursor-pointer',
                        'mx-2' => $responsive,
                    ])>
                    <form action="{{route(LOGOUT)}}" method="post" role="form" class="logout-form w-100">
                        @csrf
                        <button type="submit" role="button" title="{{ucfirst(LOGOUT)}}" class="d-flex align-items-center w-100 text-start bg-transparent border-0">
                            <span class="nav-menu-item-icon me-3">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </span>
                            <span class="nav-menu-item-title">{{ucfirst(LOGOUT)}}</span>
                        </button>
                    </form>
                </div>
            </footer>
        </div>
    </div>
</nav>
