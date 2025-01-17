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
                        <button type="button" role="button" title="{{EDIT_CATEGORY_TITLE}}" class="btn edit-btn edit-category-btn" data-mdb-toggle="modal" data-mdb-target="#edit_category_modal" data-route="{{route(EDIT_CATEGORY, $category->id)}}" data-main_image="{{imageSource($category, MAIN_IMAGE)}}" data-banner_image="{{imageSource($category, BANNER_IMAGE)}}">
                            {{ucfirst(EDIT)}}
                        </button>
                        <button type="button" role="button" title="{{capitalizeAll(DELETE_CATEGORY)}}" class="btn delete-btn delete-category-btn" data-route="{{route(DELETE_CATEGORY, $category->id)}}" data-name="{{ $category->{NAME} }}">
                            {{ucfirst(DELETE)}}
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
