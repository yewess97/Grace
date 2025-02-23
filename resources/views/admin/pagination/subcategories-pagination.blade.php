<div class="main-table admin-table table-responsive">
    <table role="table" class="subcategories-table table align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Main Image", "Related Categories")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse($subcategories as $key => $subcategory)
            <tr>
                @checkRow($subcategory->id)
                @loopIteration($subcategories->firstItem())
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
                        @foreach($subcategory->{CATEGORIES_TABLE} as $category)
                            <li>{{ $category->{NAME} }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        @if($subcategory->trashed())
                            <button type="button" role="button" title="{{capitalizeAll(RESTORE_SUBCATEGORY)}}" class="restore-subcategory-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_SUBCATEGORY, $subcategory->id)}}" data-name="{{ $subcategory->{NAME} }}">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                            </button>
                        @else
                            <button type="button" role="button" title="{{EDIT_SUBCATEGORY_TITLE}}" class="edit-subcategory-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_subcategory_modal" data-route="{{route(EDIT_SUBCATEGORY, $subcategory->id)}}" data-main_image="{{imageSource($subcategory, MAIN_IMAGE)}}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                        @endif
                        <button type="button" role="button" title="{{capitalizeAll($subcategory->trashed() ? DELETE_SUBCATEGORY : REMOVE_SUBCATEGORY)}}" class="delete-subcategory-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_SUBCATEGORY, $subcategory->id)}}" data-name="{{ $subcategory->{NAME} }}">
                            <i class="{{$subcategory->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            @noResults(SUBCATEGORIES_TABLE, 3)
        @endforelse
        </tbody>
    </table>
</div>

<div class="table-pagination col-12 pt-4">@pagination($subcategories, ADMIN_SUBCATEGORIES_ROUTE)</div>
