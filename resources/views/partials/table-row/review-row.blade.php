<tr id="row_{{$review->id}}" @class(['bg-danger-highlight' => $review->{PRODUCT_MODEL}->trashed() || $review->{USER_MODEL}->trashed()])>
    @checkRow($review->id)
    @loopIteration()
    <td>
        <p>{{ $review->{TITLE} }}</p>
    </td>
    <td class="truncate lh-lg">
        {{ $review->{BODY_TEXT} }}
    </td>
    <td class="review-rating">
        @include(REVIEW_RATING_PARTIAL, [RATING => $review->{RATING}])
    </td>
    <td class="truncate lh-lg">
        {{ $review->{PRODUCT_MODEL}->{NAME} }}
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
        <p>
            @if ($review->{PRODUCT_MODEL}->trashed())
                <b>The {{ucfirst(PRODUCT_MODEL)}} has been removed</b>
            @elseif ($review->{USER_MODEL}->trashed())
                <b>The {{ucfirst(USER_MODEL)}} has been removed</b>
            @else
                <i>Nothing</i>
            @endif
        </p>
    </td>
    <td>
        <div class="d-flex justify-content-center align-items-center gap-3">
            @if ($review->trashed())
                <button type="button" role="button" title="{{capitalizeAll(RESTORE_REVIEW)}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-route="{{route(RESTORE_REVIEW, $review->id)}}"
                        data-name="{{ $review->{TITLE} }}"
                        data-main="{{route(ADMIN_REVIEWS_ROUTE, [RATING => $review->{RATING}, CONDITION => conditionRequest()])}}"
                        class="restore-review-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{RESTORE}}"/>
                </button>
            @else
                <button type="button" role="button" title="{{EDIT_REVIEW_TITLE}}"
                        data-tooltip="tooltip" data-mdb-placement="top"
                        data-mdb-toggle="modal" data-mdb-target="#edit_review_modal"
                        data-route="{{route(EDIT_REVIEW, $review->id)}}"
                        class="edit-review-btn h-fit-content fs-5 text-success bg-transparent border-0">
                    <x-action-icon action="{{EDIT}}"/>
                </button>
            @endif
            <button type="button" role="button"
                    title="{{capitalizeAll($review->trashed() ? DELETE_REVIEW : REMOVE_REVIEW)}}"
                    data-tooltip="tooltip" data-mdb-placement="top"
                    data-route="{{route(DELETE_REVIEW, $review->id)}}"
                    data-name="{{ $review->{TITLE} }}"
                    data-main="{{route(ADMIN_REVIEWS_ROUTE, [RATING => $review->{RATING}, CONDITION => conditionRequest()])}}"
                    class="delete-review-btn h-fit-content fs-5 text-danger bg-transparent border-0">
                <x-action-icon action="{{$review->trashed() ? DELETE : REMOVE}}"/>
            </button>
        </div>
    </td>
</tr>
