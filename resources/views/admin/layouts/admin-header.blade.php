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
            <article class="main-header-admin-notifications-profile col col-lg-6 d-flex justify-content-end align-items-center gap-4">
                {{-- Notificationss --}}
                <div class="admin-notifications dropdown">
                    {{-- Notifications Icon --}}
                    <button type="button" role="button" title="Notifications" id="admin_notifications_list" class="notifications-icon dropdown-toggle bg-transparent border-0" tabindex="0" aria-expanded="false" aria-haspopup="true" aria-controls="notifications_dropdown" data-mdb-toggle="dropdown">
                        <i class="fa-regular fa-bell fs-5"></i>
                        <span class="notifications-count badge rounded-pill badge-notification bg-danger">{{auth()->user()?->unreadNotifications->count()}}</span>
                    </button>

                    {{-- Notifications List --}}
                    <div id="notifications_dropdown" class="notifications-list dropdown-menu border rounded-3" aria-labelledby="admin_notifications_list">
                        {{-- Notification Sound --}}
                        <audio id="notification_sound" src="{{asset('sounds/grace-notification-sound.mp3')}}" preload="auto"></audio>
                        {{-- Notifications Title --}}
                        <div class="notifications-title row justify-content-between align-items-center px-3 py-2">
                            <h2 class="notifications-title-text col fs-6 fw-600 text-white">Notifications</h2>
                            <a href="{{route('mark_all_as_read')}}" role="link" class="notifications-title-mark mark-all-as-read col text-end text-white">
                                <i class="fa-solid fa-check"></i>
                                <span>Mark all as read</span>
                            </a>
                        </div>
                        {{-- Notifications Details --}}
                        <ul role="list" class="notifications-details border-top">
                            @forelse (auth()->user()?->notifications as $notification)
                                <li role="listitem" id="notification{{ $notification->{ID} }}" class="notification-item position-relative d-flex align-items-center w-100 {{$notification->read_at ? '' : 'highlight-background'}}">
                                    <div class="d-grid gap-2">
                                        <p class="notifications-text mb-2">{{$notification->data['message']}}</p>
                                        <span class="notifications-timer text-muted">{{\Carbon\Carbon::parse($notification->{DATES[0]})->diffForHumans()}}</span>
                                    </div>

                                    @if (is_null($notification->read_at))
                                        <a href="{{route('mark_as_read', [ID => $notification->{ID}])}}" role="link" title="Mark as read" class="notifications-link mark-as-read-icon">
                                            <span class="new-notification-circle d-inline-block rounded-pill bg-info"></span>
                                        </a>
                                    @else
                                        <form action="{{route(DELETE.'_notification', [ID => $notification->{ID}])}}" method="post" role="form" class="delete-notification-form">
                                            @csrf
                                            @method(strtoupper(DELETE))
                                            <button type="submit" role="button" title="Delete Notification" data-tooltip="tooltip" data-mdb-placement="top" class="fs-6 bg-transparent text-danger border-0">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </li>
                            @empty
                                <li role="listitem" class="notification-item no-notifications position-relative d-grid gap-1 w-100">
                                    <p class="notifications-text text-center">No notifications available</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- Profile --}}
                <div class="admin-profile dropdown">
                    <button type="button" role="button" title="{{ auth()->user()?->{FULL_NAME} }}" id="admin_heading_menu" class="admin-name dropdown-toggle fs-6 fw-500 text-black bg-transparent border-0" data-mdb-toggle="dropdown" aria-expanded="false">@userFullName()</button>
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
