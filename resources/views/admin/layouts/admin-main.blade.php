@extends('all-main')

@section('main')

    {{-- Navigation Menu --}}
    @include(adminLayout('navbar'))


    {{-- Main Admin Body --}}
    <section class="main-admin-body position-absolute top-0">

        {{-- Header --}}
        @include(adminLayout('header'))


        @yield('content')


        {{-- Footer --}}
        @include(adminLayout('footer'))

    </section>

@endsection
