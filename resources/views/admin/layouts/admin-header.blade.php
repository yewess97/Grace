<header role="banner" class="main-header position-relative">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            {{-- Header Responsive Nav Menu --}}
            <article class="header-nav-menu col-1 d-none">
                {{-- Header Nav Menu Toggler --}}
                <div role="button" class="nav-menu-toggler fs-4 text-black" tabindex="0" aria-label="Open navigation menu" aria-expanded="false" aria-controls="admin_header_nav_menu">
                    <i class="ti ti-menu"></i>
                </div>

                {{-- Header Nav Menu Content --}}
                <nav role="navigation" id="admin_header_nav_menu" class="nav-menu responsive-nav-menu position-fixed top-0 h-100 overflow-auto" aria-label="{{ucfirst(USER_MODEL)}} navigation">
                    <div class="container h-100">
                        <div class="nav-menu-content d-flex flex-column h-100">
                            {{-- Nav Menu Layout --}}
                            @include(ADMIN_NAV_MENU_LAYOUT_PARTIAL, ['responsive' => true])
                        </div>
                    </div>
                </nav>
            </article>

            {{-- Header Main Title --}}
            <article class="col-10 col-md-6 col-sm-6">
                <h1 role="heading" class="main-header-title fs-5 fs-lg-4 fw-600">{{$title}}</h1>
            </article>

            {{-- Header Main Admin --}}
            <article class="main-header-admin-notification-profile col col-lg-6 d-flex justify-content-end align-items-center gap-4">
                {{-- Notification --}}
                <div class="admin-notification dropdown">
                    <button type="button" role="button" title="Notifications" id="admin_notification_menu" class="notification-icon dropdown-toggle bg-transparent border-0" data-mdb-toggle="dropdown" aria-expanded="false">
                        <i class="fa-regular fa-bell fs-5"></i>
                        <span class="badge rounded-pill badge-notification bg-danger">1</span>
                    </button>
                    <ul role="list" class="dropdown-menu" aria-labelledby="admin_notification_menu">
                        <li role="listitem" class="notification-list-item">
                            <p class="notification-text mb-2">User Folany has joined to the website</p>
                            <span class="notification-timer text-muted">a few seconds ago</span>
                        </li>
                    </ul>
                </div>

                {{-- Profile --}}
                <div class="admin-profile dropdown">
                    <button type="button" role="button" title="{{ auth()->user()->{FULL_NAME} }}" id="admin_heading_menu" class="admin-name dropdown-toggle fs-6 fw-500 text-black bg-transparent border-0" data-mdb-toggle="dropdown" aria-expanded="false">@userFullName()</button>
                    <ul role="list" class="dropdown-menu" aria-labelledby="admin_heading_menu">
                        <li role="listitem">
                            <a href="{{route(PROFILE)}}" role="link" class="dropdown-item d-block">My {{ucfirst(PROFILE)}}</a>
                        </li>
                        <li role="listitem"><hr class="dropdown-divider m-0 text-danger"></li>
                        <li role="listitem">
                            <form action="{{route(LOGOUT)}}" method="post" role="form" class="logout-form">
                                @csrf
                                <button type="submit" role="button" title="{{ucfirst(LOGOUT)}}" class="dropdown-item d-block">{{ucfirst(LOGOUT)}}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </article>
        </div>
    </div>
</header>
