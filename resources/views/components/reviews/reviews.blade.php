{{-- Reviews Header --}}
<article class="reviews-header d-grid">
    {{-- Reviews Header Title --}}
    <h2 class="title fs-6 fw-600">{{capitalizeAll(CUSTOMERS_REVIEWS)}}</h2>
    {{-- Reviews Header Average Rate --}}
    <div class="average-rate user-select-none">
        @include(REVIEW_RATING_PARTIAL, [RATING => $average_rate])

        <p class="rate-count mt-2">Based on
            {{$product->{REVIEWS_TABLE}->count().' '.pluralize(REVIEW_MODEL, $product->{REVIEWS_TABLE}->count())}}
        </p>
    </div>
    {{-- Reviews Header Write Review Button --}}
    <button type="button" role="button" title="Write a {{REVIEW_MODEL}}" class="col-lg-3 col-md-12 col-sm-12 btn write-review-btn px-4 fw-500">Write a {{REVIEW_MODEL}}</button>
</article>

{{-- Review Exists Alert --}}
<div id="review_exists" role="alert" class="alert alert-dismissible fade show alert-danger d-none justify-content-between align-items-center pe-4" data-mdb-color="danger">
    <div class="error-message">
        <i class="fas fa-times-circle me-3"></i>
        <span id="review_exists_message"></span>
    </div>
    <button type="button" role='button' title='Close' class="btn-close position-relative p-0" data-mdb-dismiss="alert" aria-label="Close"></button>
</div>

{{-- Product Add Review Form --}}
<form action="{{route(CREATE_UPDATE_REVIEW, ADD)}}" method="post" role="form" id="add_review_form" class="grace-form review-form flex-wrap py-4 px-0 border-top" data-reviews="{{route(PRODUCT_DETAILS, $productSlug)}}">
    @csrf
    {{-- Review Form Header --}}
    <h3 class="mb-4 fs-6 fw-600">Write a {{REVIEW_MODEL}}</h3>
    {{-- Review Form Body --}}
    <article class="grace-form-body review-body row col-12">
        {{-- Review Rating --}}
        <div class="add-review-rating">
            <div class="form-group">
                <label class="form-label">
                    <sup class="me-1">*</sup>{{ucfirst(RATING)}}
                </label>
                <div class="rate d-flex flex-row-reverse justify-content-end align-items-center">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" role="radio" name="add_review_rating" id="add_review_rating{{$i}}" value="{{$i}}">
                        <label for="add_review_rating{{$i}}" class="position-relative fs-4 text-main cursor-pointer">☆</label>
                    @endfor
                </div>
                <input type="hidden" name="add_review_rating">
            </div>
            {{$add_review_error(RATING)}}
        </div>
        {{-- Review Title --}}
        <div class="add-review-title">
            <div class="form-outline">
                <input type="text" name="add_review_title" id="add_review_title" class="form-control fs-7 rounded-2" min="2" max="50" aria-required="true">
                <label for="add_review_title" class="form-label">
                    <sup class="me-1">*</sup>Give your {{REVIEW_MODEL}} a {{TITLE}}
                </label>
                <div class="not-form-notch d-none"></div>
            </div>
            {{$add_review_error(TITLE)}}
        </div>
        {{-- Review Body Text --}}
        <div class="add-review-body-text">
            <div class="form-outline">
                <textarea name="add_review_body_text" id="add_review_body_text" class="form-control fs-7 review-body-text" rows="6" minlength="2" maxlength="1500" aria-required="true"></textarea>
                <label for="add_review_body_text" class="form-label textarea-label">
                    <sup class="me-1">*</sup>Your {{ucfirst(REVIEW_MODEL)}}
                </label>
                <div class="not-form-notch d-none"></div>
            </div>
            {{$add_review_error(BODY_TEXT)}}
        </div>
        {{-- Review Product Id --}}
        <input type="hidden" name="add_review_product_id" value="{{$product->id}}">
        {{-- Review Characters Counter & Send Button --}}
        <div class="form-group submit-review-btn col-12 d-flex">
            <button type="submit" role="button" title="Submit {{ucfirst(REVIEW_MODEL)}}" class="btn">
                Submit {{ucfirst(REVIEW_MODEL)}}
            </button>
        </div>
    </article>
</form>
{{-- Product Reviews Content --}}
<article class="reviews-content d-grid gap-4">
    {{-- Users Reviews --}}
    @forelse ($product->{REVIEWS_TABLE} as $review)
        <article class="user-review d-flex justify-content-between pt-3 border-top">
            {{-- Main User Review --}}
            <article class="user-main-review d-grid">
                {{-- User Review Rate --}}
                <div class="user-review-rate">
                    @include(REVIEW_RATING_PARTIAL, [RATING => $review->{RATING}])
                </div>
                {{-- User Review Title --}}
                <h3 class="user-review-title fs-6 fw-600">{{ $review->{TITLE} }}</h3>
                {{-- User Review Info --}}
                <div class="user-review-info fs-7 fst-italic">
                    <strong class="user-review-name fw-600">{{ $review->{USER_MODEL}->{FULL_NAME} }}</strong>
                    <span>on</span>
                    <strong class="user-review-date fw-600">{{$review->{DATES[0]}->format('d F Y')}}</strong>
                </div>
                {{-- User Review Body --}}
                <p class="user-review-body">{{ $review->{BODY_TEXT} }}</p>
            </article>
            @if (auth()->check() && auth()->id() === $review->{USER_ID})
                <article class="user-edit-delete-review-btns d-flex gap-3">
                    {{-- User Review Edit Button --}}
                    <button type="button" role="button" title="{{ucfirst(EDIT)}} my {{REVIEW_MODEL}}" class="edit-review-btn h-fit-content text-success bg-transparent border-0" data-tooltip="tooltip" data-mdb-placement="top" data-mdb-toggle="modal" data-mdb-target="#edit_review_modal" data-route="{{route(EDIT_REVIEW, $review->id)}}" aria-label="{{ucfirst(EDIT)}} my {{REVIEW_MODEL}}">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>
                    {{-- User Review Delete Button --}}
                    <form action="{{route(DELETE_REVIEW, $review->id)}}" method="post" role="form" class="delete-review-form h-fit-content" data-reviews="{{route(PRODUCT_DETAILS, $productSlug)}}">
                        @csrf
                        @method(strtoupper(DELETE))
                        <button type="submit" role="button" title="{{ucfirst(DELETE)}} my {{REVIEW_MODEL}}" data-tooltip="tooltip" data-mdb-placement="top" class="text-danger border-0" aria-label="{{ucfirst(DELETE)}} my {{REVIEW_MODEL}}">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </article>
            @endif
        </article>
    @empty
        <p class="fs-6 fw-500 text-center">No {{REVIEWS_TABLE}} yet on this {{PRODUCT_MODEL}}</p>
    @endforelse
</article>
