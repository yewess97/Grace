<footer role="contentinfo" class="footer">
    <!----======= Footer Top =======---->
    <section class="footer-top py-5">
        <div class="container">
            <ul role="list" class="footer-list row col-12 justify-content-evenly align-items-baseline">
                {{-- Logo & About --}}
                <li role="listitem" class="footer-item row gap-3">
                    <div class="footer-logo row align-items-center">
                        <a href="{{route(HOME)}}" role="link" class="d-block">
                            <img src="{{imageSource('logo.webp')}}" alt="{{config('app.name')}} Logo" fetchpriority="high">
                        </a>
                    </div>
                    <p>The best shop for selling all kinds of different clothes and accessories with the finest materials according to the highest quality standards.</p>
                    <ul role="list" class="footer-social-links d-flex flex-wrap align-items-center">
                        <li role="listitem">
                            <a href="https://www.facebook.com/YousufAymooni" target="_blank" type="button" role="link" class="fs-6 text-white rounded-circle" aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        </li>
                        <li role="listitem">
                            <a href="javascript:;" target="_blank" type="button" role="link" class="fs-6 text-white rounded-circle" aria-label="X">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                        </li>
                        <li role="listitem">
                            <a href="javascript:;" target="_blank" type="button" role="link" class="fs-6 text-white rounded-circle" aria-label="Instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        </li>
                        <li role="listitem">
                            <a href="https://www.linkedin.com/in/yewess97/" target="_blank" type="button" role="link" class="fs-6 text-white rounded-circle" aria-label="LinkedIn">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Menus --}}
                @foreach ($common_collections['footer_menus'] as $menu_title => $menu_items)
                    <li role="listitem" class="footer-item">
                        {{-- From screen 992px to above --}}
                        <h2 class="fs-6 fw-500 text-uppercase">{{$menu_title}}</h2>

                        {{-- From screen 991px to below --}}
                        <a href="#footer_menu_{{str($menu_title)->snake()->value()}}_list" role="button" class="footer-menu-list-header col-12 d-flex justify-content-between align-items-center d-lg-none collapsed" data-mdb-toggle="collapse" aria-expanded="false">
                            <span class="footer-menu-item-title fs-6 fw-500 text-uppercase text-black" data-mdb-slim="false">{{$menu_title}}</span>
                            <i class="fa fa-angle-down footer-menu-item-rotate-icon"></i>
                        </a>

                        <ul role="list" class="footer-menu row gap-3 d-lg-flex mt-4 collapse" id="footer_menu_{{str($menu_title)->snake()->value()}}_list">
                            @foreach ($menu_items as $item)
                                <li role="listitem">
                                    <a href="{{$item->route}}" role="link" title="{{ $item->{TITLE} }}" class="position-relative d-flex align-items-center overflow-hidden">{{ $item->{TITLE} }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach

                {{-- Contact Us --}}
                <li role="listitem" class="footer-item">
                    {{-- From 992px to above --}}
                    <h2 class="fs-6 fw-500 text-uppercase d-lg-block d-sm-none">{{capitalizeAll(CONTACT_US)}}</h2>

                    {{-- From 991px to below --}}
                    <a href="#footer_menu_{{CONTACT_US}}_list" role="button" class="footer-menu-list-header col-12 d-flex justify-content-between align-items-center d-lg-none collapsed" data-mdb-toggle="collapse" aria-expanded="false">
                        <span class="footer-menu-item-title fs-6 fw-500 text-uppercase text-black" data-mdb-slim="false">{{capitalizeAll(CONTACT_US)}}</span>
                        <i class="fa fa-angle-down footer-menu-item-rotate-icon"></i>
                    </a>

                    <ul role="list" class="footer-menu row gap-3 d-lg-flex mt-4 collapse" id="footer_menu_{{CONTACT_US}}_list">
                        <li role="listitem" class="d-flex align-items-center gap-2">
                            <div role="img" class="contact-icon fs-9" aria-label="{{ADDRESS_MODEL}}">
                                <i class="ti ti-location-pin"></i>
                            </div>
                            <div class="contact-info">
                                <p class="m-0">Al-Mohafzah ST, Asyut, Egypt, 71111</p>
                            </div>
                        </li>
                        <li role="listitem" class="d-flex align-items-center gap-2">
                            <div role="img" class="contact-icon fs-9" aria-label="Send me Whatsapp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div class="contact-info">
                                <a href="https://wa.me/+201011836243" target="_blank" title="Send me Whatsapp" aria-label="Send me Whatsapp">+201011836243</a>
                            </div>
                        </li>
                        <li role="listitem" class="d-flex align-items-center gap-2">
                            <div role="img" class="contact-icon fs-9" aria-label="{{ucfirst(EMAIL)}}">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <div class="contact-info">
                                <a href="mailto:yewess97@gmail.com" target="_blank" title="{{ucfirst(EMAIL)}} me" aria-label="{{ucfirst(EMAIL)}} me">yewess97@gmail.com</a>
                            </div>
                        </li>
                        <li role="listitem">
                            <ul role="list" class="d-flex align-items-center gap-1">
                                @php($stores = ['google-play', 'app-store'])
                                @foreach ($stores as $store)
                                    <li role="listitem" class="d-block p-0 overflow-hidden rounded-3 cursor-pointer">
                                        <img src="{{imageSource("$store.webp")}}" alt="Our App on {{capitalizeAll($store)}}">
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </section>

    <!----======= Footer Bottom =======---->
    <x-layout.bottom-footer/>
</footer>
