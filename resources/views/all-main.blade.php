<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    @include('head-tags')

    {{-- Title --}}
    <title>
        @isset($title) {{$title}} | @endisset {{isAdminRoute() ? ucfirst(ADMIN).' Panel - ' : ''}} {{config('app.name')}}
    </title>
</head>
<body>

{{-- Preloader --}}
<div id="preloader" class="position-fixed">
    <div class="preloader-container d-grid place-items-center w-100 h-100">
        <svg class="preloader-cart" role="img" aria-label="Shopping cart line animation" viewBox="0 0 128 128" width="128px" height="128px" xmlns="http://www.w3.org/2000/svg">
            <g fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="8">
                <g class="cart-track" stroke="hsla(0,10%,10%,0.1)">
                    <polyline points="4,4 21,4 26,22 124,22 112,64 35,64 39,80 106,80" />
                    <circle cx="43" cy="111" r="13" />
                    <circle cx="102" cy="111" r="13" />
                </g>
                <g class="cart-lines" stroke="currentColor">
                    <polyline class="cart-top" points="4,4 21,4 26,22 124,22 112,64 35,64 39,80 106,80" stroke-dasharray="338 338" stroke-dashoffset="-338" />
                    <g class="cart-wheel1" transform="rotate(-90,43,111)">
                        <circle class="cart-wheel-stroke" cx="43" cy="111" r="13" stroke-dasharray="81.68 81.68" stroke-dashoffset="81.68" />
                    </g>
                    <g class="cart-wheel2" transform="rotate(90,102,111)">
                        <circle class="cart-wheel-stroke" cx="102" cy="111" r="13" stroke-dasharray="81.68 81.68" stroke-dashoffset="81.68" />
                    </g>
                </g>
            </g>
        </svg>
    </div>
</div>

@yield('main')

{{-- Responsive Nav Menu Overlay --}}
<div role="dialog" class="nav-menu-overlay position-fixed top-0 start-0 d-none w-100 h-100" aria-label="Overlay"></div>

{{-- Scripts --}}
@include('scripts')

</body>
</html>
