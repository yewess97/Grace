@props(['url', 'icon', 'route_name', 'id' => null, 'submenu' => null])

@php
    $current_item = adminCurrentUrl($url, ['current-item', 'active'])
@endphp

@if($submenu)
    @props(['all_table', 'column_name'])

    <li role="listitem" id="{{$id ?? null}}" class="nav-menu-list-item {{$current_item}}">
        <a href="#nav_menu_item_{{$url}}_submenu" class="nav-menu-item d-flex justify-content-between align-items-center collapsed" data-mdb-toggle="collapse" aria-expanded="false">
            <div class="nav-menu-item-icon-title d-flex align-items-center gap-4">
                <i class="{{$icon}} nav-menu-item-icon"></i>
                <span class="nav-menu-item-title" data-mdb-slim="false">{{ucfirst($url)}}</span>
            </div>
            <i class="fas fa-angle-down nav-menu-item-rotate-icon {{adminCurrentUrl($url, ['rotate-180'])}}"></i>
        </a>
        <ul role="list" id="nav_menu_item_{{$url}}_submenu" class="nav-submenu-list row gap-3 box-content {{str_contains(url()->current(), ADMIN."/$url") ? 'show' : 'collapse'}}">
            @foreach ((object) $all_table as $table)
                <li role="listitem" class="nav-submenu-list-item {{str(Request::query($column_name))->whenContains($table->{$column_name}, fn() => 'active')}}">
                    <a href="{{str_contains(Request::query($column_name), $table->{$column_name}) ? 'javascript:;' : route($route_name, [$column_name => $table->{$column_name}])}}" role="link" class="nav-submenu-item d-flex align-items-center">
                        @if ($column_name === STATUS)
                            <span class="nav-submenu-item-title">{{orderStatus($table)}}</span>
                        @endif

                        @if ($column_name === RATING)
                            @for ($i = 1; $i <= $table->{$column_name}; $i++)
                                <span class="position-relative star-fill" aria-label="Filled Star">★</span>
                            @endfor
                            @for ($j = 1; $j <= 5 - $table->{$column_name}; $j++)
                                <span class="position-relative star-empty" aria-label="Empty Star">☆</span>
                            @endfor
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </li>

@else

    <li role="listitem" class="nav-menu-list-item {{$current_item}}">
        <a href="{{Route::currentRouteName() === $route_name ? 'javascript:;' : route($route_name)}}" role="link" class="nav-menu-item d-flex align-items-center gap-4">
            <i class="{{$icon}} nav-menu-item-icon"></i>
            <span class="nav-menu-item-title">{{ucfirst($url)}}</span>
        </a>
    </li>

@endif
