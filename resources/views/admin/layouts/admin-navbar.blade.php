<nav role="navigation" class="nav-menu default-nav-menu position-fixed top-0 start-0 h-100" aria-label="{{ucfirst(ADMIN)}} navigation">
    <div class="container h-100">
        <div class="nav-menu-content d-flex flex-column h-100">
            {{-- Nav Menu Open/CLose Icon --}}
            <span class="nav-menu-toggle position-absolute d-flex justify-content-center align-items-center p-2 rounded-circle" aria-label="Close or Open the navigation menu">
                <i class="fa-solid fa-angle-right nav-menu-toggle-icon"></i>
            </span>
            {{-- Nav Menu Layout --}}
            @include(ADMIN_NAV_MENU_LAYOUT_PARTIAL, ['responsive' => false])
        </div>
    </div>
</nav>
