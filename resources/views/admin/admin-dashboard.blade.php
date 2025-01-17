@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Dashboard Main --}}
    <main class="dashboard-main main-body" role="main">
        @include(ADMIN_DASHBOARD_COMPONENT)
    </main>

@endsection


@section('admin-js-links')
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
@endsection
