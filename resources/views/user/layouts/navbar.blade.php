<nav role="navigation" class="navbar p-0" aria-label="{{ucfirst(USER_MODEL)}} navigation">
    <div class="container">
        {{-- Navbar Products search --}}
        <article class="nav-search products-search row col-12">
            <x-products.search/>
        </article>

        {{-- Navbar List Content --}}
        <div class="nav-list row col-12 align-items-center">
            <article class="nav-content col-8 position-relative">
                <ul role="list" class="navbar-nav flex-row gap-5">
                    {{-- Nav Home --}}
                    <li role="listitem" class="nav-item">
                        <a href="{{route(HOME)}}" role="link" class="position-relative d-block py-3 fs-6 text-white">
                            <span class="text-capitalize">Home</span>
                        </a>
                    </li>
                    {{-- Nav Dropdowns --}}
                    @foreach ($common_collections['navbar_dropdowns'] as $navbar_dropdown)
                        <li role="listitem" class="nav-item nav-dropdown">
                            {{-- Nav Dropdown Title --}}
                            <a href="javascript:;" class="dropdown position-relative d-block py-3 fs-6 text-white">
                                <span class="me-2">{{ucfirst($navbar_dropdown->{TITLE})}}</span>
                                <i class="fa-solid fa-angle-down caret-icon fw-bold"></i>
                            </a>
                            {{-- Nav Dropdown List Content --}}
                            <ul role="list" class="dropdown-content box-content row {{count($navbar_dropdown->collection) <= 3 ? 'col-12' : 'row-cols-1 row-cols-md-3'}} position-absolute start-0 align-items-center border rounded-bottom">
                                @foreach ($navbar_dropdown->collection as $collection)
                                    <li role="listitem" class="col">
                                        <a href="{{route($navbar_dropdown->route_name, $collection->{SLUG})}}" role="link" class="d-grid place-items-center gap-2">
                                            <div class="dropdown-img img-hover-effect position-relative overflow-hidden">
                                                <img src="{{imageSource($collection, MAIN_IMAGE)}}" alt="{{ $collection->{NAME} }}" class="w-100">
                                            </div>
                                            <h3 class="dropdown-name fs-6 fw-500 text-capitalize lh-1">{{ $collection->{NAME} }}</h3>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                    {{-- Nav Items --}}
                    @foreach ($common_collections['navbar_items'] as $navbar_item)
                        <li class="nav-item">
                            <a href="{{route($navbar_item->route_name)}}" role="link" class="position-relative d-block py-3 fs-6 text-white">
                                <span class="text-capitalize">{{capitalizeAll($navbar_item->route_name)}}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </article>
            {{-- Navbar Best Offer --}}
            <article class="nav-offer col-4 d-flex gap-4">
                {{-- Offer Label --}}
                <div class="offer-label position-relative d-grid place-items-center">
                    <p>Best Offer</p>
                </div>

                {{-- Offers Sales --}}
                <x-home.offers-sales :common_collections="$common_collections"/>
            </article>
        </div>
    </div>
</nav>
