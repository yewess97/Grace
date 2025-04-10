@extends(key(viewLayoutTitle(ADMIN)), current(viewLayoutTitle(ADMIN)))

@section('content')

    {{-- Dashboard Main --}}
    <main class="dashboard-main main-body" role="main">
        <div class="container">
            <div class="row">
                <!----======= Left Side =======---->
                <section class="left-side row col-9">
                    {{-- Filter By Date Form --}}
                    <article class="row col-12">
                        <form action="{{route(FILTER_DASHBOARD)}}" method="post" role="form" id="filter_dashboard_form" class="grace-form filter-form" data-no_results="{{imageSource('no-results.png')}}">
                            @csrf
                            <div class="grace-form-body row col-12 justify-content-lg-between justify-content-md-center align-items-lg-end align-items-md-center">
                                {{-- Start Date --}}
                                <div class="filter-dashboard-start-date col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="filter_dashboard_start_date" class="form-label fs-6 fw-500">{{capitalizeAll(START_DATE)}}: </label>
                                        <input type="date" name="filter_dashboard_start_date" id="filter_dashboard_start_date" class="col-12 rounded-2" min="2022-06-24" max="{{now()->toDateString()}}" aria-required="true" value="{{old('filter_dashboard_start_date')}}">
                                    </div>
                                    {{$filter_dashboard_error(START_DATE)}}
                                </div>
                                {{-- End Date --}}
                                <div class="filter-dashboard-end-date col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="filter_dashboard_end_date" class="form-label fs-6 fw-500">{{capitalizeAll(END_DATE)}}: </label>
                                        <input type="date" name="filter_dashboard_end_date" id="filter_dashboard_end_date" class="col-12 rounded-2" min="2022-06-24" max="{{now()->toDateString()}}" aria-required="true" value="{{old('filter_dashboard_end_date')}}">
                                    </div>
                                    {{$filter_dashboard_error(END_DATE)}}
                                </div>
                                {{-- Buttons --}}
                                <div class="filter-dashboard-buttons row flex-lg-nowrap col-12 col-lg-5 align-items-center gap-3 gap-lg-4">
                                    {{-- Filter Button --}}
                                    <button type="submit" role="button" title="{{ucfirst(FILTER)}}" class="btn col-12 col-lg-4 col-md">{{ucfirst(FILTER)}}</button>
                                    {{-- Clear Filter Button --}}
                                    <a href="{{route(ADMIN_DASHBOARD_ROUTE)}}" role="link" id="clear_filter" class="col d-grid d-lg-block place-items-center text-decoration-underline">Clear {{ucfirst(FILTER)}}</a>
                                </div>
                            </div>
                        </form>
                    </article>

                    {{-- Orders Metrics --}}
                    <ul class="orders-metrics row row-cols-3 align-items-center gap-3 gap-lg-0">
                        @foreach ($orders_metrics as $order_metric)
                            <li class="order-metric col {{$order_metric->card_padding}}">
                                <div class="metric-card d-flex justify-content-between align-items-center">
                                    {{-- Total Metrics Info --}}
                                    <article class="total-metrics-info row gap-2">
                                        <span class="metrics-icon total-{{strtolower($order_metric->{NAME})}}-icon material-symbols-rounded fw-500 rounded-circle">{{$order_metric->icon}}</span>
                                        <h2 class="metrics-title fw-500">Total {{ $order_metric->{NAME} }}</h2>
                                        <p class="metrics-total fs-5 fw-bold">@priceFormat($order_metric->{TOTAL_COST})</p>
                                        <small class="text-muted">{{Route::currentRouteName() === FILTER_DASHBOARD ? 'In Filtered Dates' : 'In the Last 24 Hours'}}</small>
                                    </article>
                                    {{-- Total Metrics Statistic --}}
                                    <article class="total-metrics-statistic fw-500 text-{{$order_metric->statistic > 0 ? 'success' : 'danger'}}">
                                <span class="metrics-statistic-icon me-2">
                                    <i class="ti ti-stats-{{$order_metric->statistic > 0 ? 'up' : 'down'}}"></i>
                                </span>
                                        <span class="metrics-statistic-num">{{number_format($order_metric->statistic, 2)}}%</span>
                                    </article>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Users Countries Analytics --}}
                    <article class="users-countries-analytics">
                        {{-- Users Countries Geo Map Title --}}
                        <h2 class="fs-6 fw-600"># Registered {{ucfirst(USERS_TABLE)}} Places</h2>
                        {{-- Users Countries Geo Map Chart --}}
                        <div class="users-countries-geo-map">
                            <div id="geo_map_chart" class="d-flex justify-content-center align-items-center" data-users="{{$registered_users}}"></div>
                        </div>
                    </article>
                    {{-- Recent Customers Orders --}}
                    <article class="recent-customers-orders row col-12">
                        {{-- Recent Customers Orders Title --}}
                        <h2 class="fs-6 fw-600"># {{ucfirst(ORDERS_TABLE)}} for Each Customer</h2>

                        {{-- Recent Customers Orders Table --}}
                        <div class="customers-orders-table pagination-container">
                            @include(ADMIN_DASHBOARD_PAGINATION, [USERS_TABLE => $users])
                        </div>
                    </article>
                </section>

                <!----======= Right Side =======---->
                <aside class="right-side row col-3" role="region">
                    {{-- Subcategories Products Analytics --}}
                    <article class="subcategories-products-analytics">
                        {{-- Subcategories Products Analytics Title --}}
                        <h2 class="fs-6 fw-600"># {{ucfirst(PRODUCTS_TABLE)}} in Each {{ucfirst(SUBCATEGORY_MODEL)}}</h2>
                        {{-- Subcategories Products Analytics Chart --}}
                        <div class="subcategories_products_analytics_chart">
                            <div id="pie_chart" class="d-flex justify-content-center align-items-center" data-subcategories="{{$subcategories}}" aria-label="Number of {{PRODUCTS_TABLE}} in each {{SUBCATEGORY_MODEL}}"></div>
                        </div>
                    </article>
                    {{-- Orders Analytics --}}
                    <article class="orders-analytics">
                        {{-- Orders Analytics Title --}}
                        <h2 class="fs-6 fw-600">{{ucfirst(ORDERS_TABLE)}} Analytics</h2>
                        {{-- Orders Analytics Numbers --}}
                        <ul role="list" class="orders-analytics-nums row gap-3">
                            @foreach($orders_statuses as $order_status)
                                <li role="listitem">
                                    <a href="{{route(ADMIN_ORDERS_ROUTE, $order_status->{STATUS})}}" role="link" class="analytic-card d-flex justify-content-between align-items-center">
                                        <div class="order-analytic-icon-name d-flex align-items-center gap-3">
                                            <span class="order-analytic-icon {{strtolower($order_status->label)}}-icon material-symbols-rounded rounded-circle">{{orderStatus($order_status, 'icon')}}</span>
                                            <h3 class="fw-500">{{$order_status->label}} {{ucfirst(ORDERS_TABLE)}}</h3>
                                        </div>
                                        <span class="fw-600">{{$order_status->orders_count}}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                    {{-- Reviews Analytics --}}
                    <article class="reviews-analytics">
                        {{-- Reviews Analytics Title --}}
                        <h2 class="fs-6 fw-600">{{ucfirst(REVIEWS_TABLE)}} Analytics</h2>
                        {{-- Reviews Analytics Numbers --}}
                        <ul role="list" class="reviews-analytics-nums d-grid gap-3">
                            @foreach($reviews_ratings as $review_rating)
                                <li role="listitem">
                                    <a href="{{route(ADMIN_REVIEWS_ROUTE, [RATING => $review_rating->{RATING}])}}" role="link" class="analytic-card d-flex justify-content-between align-items-center">
                                        <div class="review-analytic-stars d-flex align-items-center gap-1">
                                            @include(REVIEW_RATING_PARTIAL, [RATING => $review_rating->rating_count])
                                        </div>
                                        <span class="fw-600">{{$review_rating->reviews_count}}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                </aside>
            </div>
        </div>
    </main>

@endsection


@section('admin-js-links')
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
@endsection
