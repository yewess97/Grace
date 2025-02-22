<div class="main-table admin-table table-responsive">
    <table role="table" class="table align-middle mb-0 fs-7 bg-white">
        <thead class="text-center bg-light">
        <tr>
            @tableHeaders("Name", "Main Image", "Banner Image")
        </tr>
        </thead>
        <tbody class="text-center">
        @forelse ($categories as $key => $category)
            <tr>
                @checkRow($category->id)
                @loopIteration($categories->firstItem())
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
                        @if($category->trashed())
                            <button type="button" role="button" title="{{capitalizeAll(RESTORE_CATEGORY)}}" class="restore-category-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_CATEGORY, $category->id)}}" data-name="{{ $category->{NAME} }}">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                            </button>
                        @else
                            <button type="button" role="button" title="{{EDIT_CATEGORY_TITLE}}" class="edit-category-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_category_modal" data-route="{{route(EDIT_CATEGORY, $category->id)}}" data-main_image="{{imageSource($category, MAIN_IMAGE)}}" data-banner_image="{{imageSource($category, BANNER_IMAGE)}}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                        @endif
                        <button type="button" role="button" title="{{capitalizeAll($category->trashed() ? DELETE_CATEGORY : REMOVE_CATEGORY)}}" class="delete-category-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_CATEGORY, $category->id)}}" data-name="{{ $category->{NAME} }}">
                            <i class="{{$category->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            @noResults(CATEGORIES_TABLE, 3)
        @endforelse
        </tbody>
    </table>
</div>

<div class="table-pagination col-12 pt-4">@pagination($categories, ADMIN_CATEGORIES_ROUTE)</div>
