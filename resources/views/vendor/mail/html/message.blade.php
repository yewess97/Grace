@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => route('home')])
            <img src="{{storage_path('app/public/images/favicon.png')}}" alt="{{config('app.name')}} Logo" class="logo">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            Copyright &copy; {{ date('Y') }}. Made with &hearts; By <a href="https://www.linkedin.com/in/yewess97/" target="_blank">Yousif Ayman</a><sup>TM</sup>
        @endcomponent
    @endslot
@endcomponent
