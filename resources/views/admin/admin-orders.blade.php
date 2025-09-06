@extends(key(viewLayoutTitle(ADMIN)), [TITLE => $orders_title])

@section('content')

    {{-- Orders Main --}}
    <main class="orders-main main-body" role="main">
        <div class="container">
            <div class="row">
                <section class="orders row col-12 gap-4">
                    {{-- Orders Search/Filter & Delete all selected Button --}}
                    <div class="search-filter-action-buttons row col-12 justify-content-between align-items-center">
                        <article class="search-filter row col-12 col-md-10 align-items-center gap-4">
                            {{-- Orders Search --}}
                            @search(SEARCH_ORDERS, [STATUS => $order_status])

                            {{-- Orders Filter by Date --}}
                            <form action="{{route(SEARCH_ORDERS, [STATUS => $order_status, 'type' => FILTER])}}" method="post" role="form" id="filter_orders_form" class="grace-form filter-form col-12 col-md-6" data-no_results="{{imageSource('no-results.png')}}">
                                @csrf
                                <div class="grace-form-body row">
                                    <div class="filter-orders-dates row col-12">
                                        {{-- Start Date --}}
                                        <div class="filter-orders-start-date col-12 col-lg-6 pe-lg-2">
                                            <div class="form-group">
                                                <label for="filter_orders_start_date" class="form-label fs-6 fw-500">{{capitalizeALL(START_DATE)}}: </label>
                                                <input type="date" name="filter_orders_start_date" id="filter_orders_start_date" class="col-12 rounded-2" min="2022-06-24" max="{{now()->toDateString()}}" aria-required="true" value="{{old('filter_orders_start_date')}}">
                                            </div>
                                            {{$filter_orders_error(START_DATE)}}
                                        </div>
                                        {{-- End Date --}}
                                        <div class="filter-orders-end-date col-12 col-lg-6 ps-lg-2">
                                            <div class="form-group">
                                                <label for="filter_orders_end_date" class="form-label fs-6 fw-500">{{capitalizeALL(END_DATE)}}: </label>
                                                <input type="date" name="filter_orders_end_date" id="filter_orders_end_date" class="col-12 rounded-2" min="2022-06-24" max="{{now()->toDateString()}}" aria-required="true" value="{{old('filter_orders_end_date')}}">
                                            </div>
                                            {{$filter_orders_error(END_DATE)}}
                                        </div>
                                    </div>
                                    {{-- Buttons --}}
                                    <div class="filter-orders-buttons row col-12 justify-content-between align-items-center">
                                        {{-- Filter Button --}}
                                        <button type="submit" role="button" title="{{ucfirst(FILTER)}}" class="btn col-12 col-md-7">
                                            {{ucfirst(FILTER)}}
                                        </button>
                                        {{-- Clear Search/Filter Button --}}
                                        <div class="col-12 col-md-4 mt-3 mt-md-0 text-center">
                                            @clearSearchFilter(route(ADMIN_ORDERS_ROUTE, [STATUS => $order_status, CONDITION => trashedConditionRequest()]))
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </article>

                        {{-- Orders Main Buttons --}}
                        @collectionButtons(ORDERS_TABLE, ADMIN_ORDERS_ROUTE)
                    </div>

                    {{-- Orders Table --}}
                    <div class="pagination-container search-table">
                        @include(ADMIN_ORDERS_PAGINATION, [ORDERS_TABLE => $orders])
                    </div>
                </section>
            </div>
        </div>
    </main>


    {{-- Edit Order Modal --}}
    @include(EDIT_ORDER_MODAL)

@endsection
