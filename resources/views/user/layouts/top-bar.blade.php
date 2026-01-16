<div role="toolbar" class="top-bar">
    <div class="container">
        <div class="row">
            <ul role="list" class="top-bar-content col-12 d-flex align-items-center p-0">
                <li role="listitem" class="top-info col d-flex justify-content-start align-items-center gap-4">
                    <article class="mail">
                        <a href="mailto:yewess97@gmail.com" target="_blank" role="link" class="top-contact d-flex align-items-center gap-2">
                            <i class="fa-solid fa-envelope"></i>
                            <span>yewess97@gmail.com</span>
                        </a>
                    </article>
                    <article class="mobile">
                        <a href="tel:+201011836243" role="link" class="top-contact d-flex align-items-center gap-2">
                            <i class="fa-solid fa-phone"></i>
                            <span>+201011836243</span>
                        </a>
                    </article>
                </li>
                <li role="listitem" class="top-date col d-flex justify-content-lg-center align-items-center">
                    {{\Carbon\Carbon::now()->format('l - d F Y')}}
                </li>
                <li role="listitem" class="top-wishlist col d-lg-flex d-sm-none justify-content-end align-items-center">
                    <a href="{{route(WISHLIST_MODEL)}}" role="link">
                        <i class="fa-solid fa-heart me-1"></i>
                        <span>Wishlist (0)</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
