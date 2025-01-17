<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.js"></script>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.12/sweetalert2.min.js"></script>
<script type="application/javascript" src="{{asset('assets/filter-multiselect/filter-multi-select-bundle.min.js')}}"></script>
@if(isAdminRoute())
    <script type="application/javascript" src="https://unpkg.com/aksfileupload@1.0.0/dist/aksFileUpload.min.js"></script>
    @yield('admin-js-links')
    <script type="module" src="{{asset('js/admin.js')}}"></script>
    <script type="module" src="{{asset('js/admin-ajax.js')}}"></script>
    @yield('admin-js')
@else
    @yield('user-js-links')
    <script type="module" src="{{asset('js/app.js')}}"></script>
    <script type="module" src="{{asset('js/user-ajax.js')}}"></script>
    @yield('user-js')
@endif
