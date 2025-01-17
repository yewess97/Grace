@isset($badge)
    <a href="javascript:;" role="link" class="category-title menu-item position-relative d-flex align-items-center gap-2">
        <span>{{capitalizeAll($wear_type)}}</span>
        <span class="badge position-relative d-flex justify-content-center align-items-center text-uppercase rounded">{{$badge}}</span>
    </a>
@else
    @foreach ($aside_menus[$wear_type] as $title => $menu_items)
        <li role="menuitem" class="dropdown-item">
            <h2 class="dropdown-item-title fs-6 fw-600">{{$title}}</h2>
            <ul role="list" class="dropdown-item-content d-grid gap-2">
                @foreach ($menu_items as $item)
                    <li role="listitem" class="dropdown-item-point">
                        <a href="javascript:;" role="link" class="dropdown-item-link text-capitalize">{{$item}}</a>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
@endisset
