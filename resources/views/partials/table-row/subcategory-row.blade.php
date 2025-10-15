<tr id="row_{{$subcategory->id}}">
    @checkRow($subcategory->id)
    @loopIteration()
    <td>
        <p>{{ $subcategory->{NAME} }}</p>
    </td>
    <td>
        <div class="main-image mx-auto">
            <img src="{{imageSource($subcategory, MAIN_IMAGE)}}" alt="{{ $subcategory->{NAME} }}">
        </div>
    </td>
    <td>
        <ul class="cell-menu overflow-auto">
            @foreach ($subcategory->{CATEGORIES_TABLE} as $category)
                <li>{{ $category->{NAME} }}</li>
            @endforeach
        </ul>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if ($subcategory->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_SUBCATEGORY)}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-route="{{route(RESTORE_SUBCATEGORY, $subcategory->id)}}"
                        data-name="{{ $subcategory->{NAME} }}"
                        data-main="{{route(ADMIN_SUBCATEGORIES_ROUTE, [CONDITION => conditionRequest()])}}"
                        class="restore-subcategory-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{RESTORE}}"/>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_SUBCATEGORY_TITLE}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-mdb-toggle="modal" data-mdb-target="#edit_subcategory_modal"
                        data-route="{{route(EDIT_SUBCATEGORY, $subcategory->id)}}"
                        data-main_image="{{imageSource($subcategory, MAIN_IMAGE)}}"
                        class="edit-subcategory-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{EDIT}}"/>
                </button>
            @endif
            <button type="button" role="button"
                    title="{{capitalizeAll($subcategory->trashed() ? DELETE_SUBCATEGORY : REMOVE_SUBCATEGORY)}}"
                    data-tooltip="tooltip" data-mdb-placement="top"
                    data-route="{{route(DELETE_SUBCATEGORY, $subcategory->id)}}"
                    data-name="{{ $subcategory->{NAME} }}"
                    data-main="{{route(ADMIN_SUBCATEGORIES_ROUTE, [CONDITION => conditionRequest()])}}"
                    class="delete-subcategory-btn h-fit-content fs-5 text-danger bg-transparent border-0">
                <x-action-icon action="{{$subcategory->trashed() ? DELETE : REMOVE}}"/>
            </button>
        </div>
    </td>
</tr>
