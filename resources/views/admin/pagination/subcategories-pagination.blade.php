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
                        <button type="button" role="button" title="{{EDIT_SUBCATEGORY_TITLE}}" class="btn edit-btn edit-subcategory-btn" data-mdb-toggle="modal" data-mdb-target="#edit_subcategory_modal" data-route="{{route(EDIT_SUBCATEGORY, $subcategory->id)}}" data-main_image="{{imageSource($subcategory, MAIN_IMAGE)}}">
                            {{ucfirst(EDIT)}}
                        </button>
                        <button type="button" role="button" title="{{capitalizeAll(DELETE_SUBCATEGORY)}}" class="btn delete-btn delete-subcategory-btn" data-route="{{route(DELETE_SUBCATEGORY, $subcategory->id)}}" data-name="{{ $subcategory->{NAME} }}">
                            {{ucfirst(DELETE)}}
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
