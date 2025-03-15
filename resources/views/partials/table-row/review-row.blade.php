<tr id="row_{{$review->id}}">
    @checkRow($review->id)
    @loopIteration()
    <td>
        <p>{{ $review->{TITLE} }}</p>
    </td>
    <td>
        <p class="truncate">{{ $review->{BODY_TEXT} }}</p>
    </td>
    <td class="review-rating">
        @include(REVIEW_RATING_PARTIAL, [RATING => $review->{RATING}])
    </td>
    <td class="truncate">
        <p>{{ $review->{PRODUCT_MODEL}->{NAME} }}</p>
    </td>
    <td>
        <p>{{ $review->{USER_MODEL}->{FULL_NAME} }}</p>
    </td>
    <td>
        <p>{!! dates($review, 0, true) !!}</p>
    </td>
    <td>
        <p>{!! dates($review, 1, true) !!}</p>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if($review->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_REVIEW)}}" class="restore-review-btn h-fit-content fs-5 text-success bg-transparent border-0" data-route="{{route(RESTORE_REVIEW, $review->id)}}" data-name="{{ $review->{TITLE} }}">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_REVIEW_TITLE}}" class="edit-review-btn h-fit-content fs-5 text-success bg-transparent border-0" data-mdb-toggle="modal" data-mdb-target="#edit_review_modal" data-route="{{route(EDIT_REVIEW, $review->id)}}">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>
            @endif
            <button type="button" role="button" title="{{capitalizeAll($review->trashed() ? DELETE_REVIEW : REMOVE_REVIEW)}}" class="delete-review-btn h-fit-content fs-5 text-danger bg-transparent border-0" data-route="{{route(DELETE_REVIEW, $review->id)}}" data-name="{{ $review->{TITLE} }}">
                <i class="{{$review->trashed() ? 'fa-solid fa-trash' : 'fa-regular fa-trash-can'}}"></i>
            </button>
        </div>
    </td>
</tr>
