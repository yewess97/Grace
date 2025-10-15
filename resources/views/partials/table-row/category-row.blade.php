<tr id="row_{{$category->id}}">
    @checkRow($category->id)
    @loopIteration()
    <td>
        <p>{{ $category->{NAME} }}</p>
    </td>
    <td>
        <div class="main-image mx-auto">
            <img src="{{imageSource($category, MAIN_IMAGE)}}" alt="{{ $category->{NAME} }}">
        </div>
    </td>
    <td>
        <div class="main-image mx-auto">
            <img src="{{imageSource($category, BANNER_IMAGE)}}" alt="{{ $category->{NAME} }}">
        </div>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if ($category->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_CATEGORY)}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-route="{{route(RESTORE_CATEGORY, $category->id)}}"
                        data-name="{{ $category->{NAME} }}"
                        data-main="{{route(ADMIN_CATEGORIES_ROUTE, [CONDITION => conditionRequest()])}}"
                        class="restore-category-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{RESTORE}}"/>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_CATEGORY_TITLE}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-mdb-toggle="modal" data-mdb-target="#edit_category_modal"
                        data-route="{{route(EDIT_CATEGORY, $category->id)}}"
                        data-main_image="{{imageSource($category, MAIN_IMAGE)}}"
                        data-banner_image="{{imageSource($category, BANNER_IMAGE)}}"
                        class="edit-category-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{EDIT}}"/>
                </button>
            @endif
            <button type="button" role="button"
                    title="{{capitalizeAll($category->trashed() ? DELETE_CATEGORY : REMOVE_CATEGORY)}}"
                    data-tooltip="tooltip" data-mdb-placement="top"
                    data-route="{{route(DELETE_CATEGORY, $category->id)}}"
                    data-name="{{ $category->{NAME} }}"
                    data-main="{{route(ADMIN_CATEGORIES_ROUTE, [CONDITION => conditionRequest()])}}"
                    class="delete-category-btn h-fit-content fs-5 text-danger bg-transparent border-0">
                <x-action-icon action="{{$category->trashed() ? DELETE : REMOVE}}"/>
            </button>
        </div>
    </td>
</tr>
