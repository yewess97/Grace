@extends('all-main')

@section('main')

    @unless(in_array(Route::currentRouteName(), [CHECKOUT, CREATE_ORDER], true))
        {{-- Top Bar --}}
        @include(userLayout('top-bar'))

        {{-- Header --}}
        @include(userLayout('header'))

        {{-- Navbar --}}
        @include(userLayout('navbar'))
    @endunless

    @yield('content')

    {{-- Footer --}}
    @includeIf(Route::currentRouteName() === CHECKOUT ? userLayout(CHECKOUT.'-footer') : userLayout('footer'))

@endsection
