<div role="toolbar" class="top-bar">
    <div class="container">
        <div class="row">
            <ul role="list" class="top-bar-content col-12 d-flex align-items-center p-0">
                <li role="listitem" class="top-info col d-flex justify-content-start align-items-center gap-4">
                    <article class="mail">
                        <a href="mailto:yewess97@gmail.com" target="_blank" role="link" title="{{ucfirst(EMAIL)}} me" class="top-contact d-flex align-items-center gap-2" aria-label="{{ucfirst(EMAIL)}} me">
                            <i class="fa-solid fa-envelope"></i>
                            <span>yewess97@gmail.com</span>
                        </a>
                    </article>
                    <article class="mobile">
                        <a href="tel:+201011836243" role="link" title="Call me" class="top-contact d-flex align-items-center gap-2" aria-label="Call me">
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
                        <span>{{ucfirst(WISHLIST_MODEL)}}</span>
                        (<span class="wishlist-total-items">{{$wishlist_total_items}}</span>)
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
