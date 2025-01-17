<?php

namespace App\Services;

use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\BadRequestException;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReviewService
{
    /**
     * Display the reviews.
     *
     * @param int $productId
     * @return string
     * @throws Throwable
     */
    final public function displayReviews(int $productId): string
    {
        $product = Product::query()->findOrFail($productId, [ID])
            ?->load(REVIEWS_TABLE);

        return view(REVIEWS_COMPONENT, getReviews($product))->render();
    }

    /**
     * Store or Update a review.
     *
     * @param string $operation
     * @return Review
     * @throws AuthenticationException|ModelNotFoundException|BadRequestException|ValidationException
     */
    final public function createOrUpdateReview(string $operation): Review
    {
        if (!auth()->check()) {
            throw new AuthenticationException('unauthenticated');
        }

        $review_request = new ReviewRequest($operation, REVIEW_MODEL, REVIEW_ATTRIBUTES);

        $review_id = request()?->input(UPDATE_REVIEW_ID);

        validateAttributes($review_request);

        [$rating, $title, $body_text, $product_id] = REVIEW_ATTRIBUTES;

        [$rating_value, $title_value, $body_text_value, $product_id_value] = $review_request->dataValues();

        $product = Product::query()->whereId($product_id_value)
            ->whereStatus(1)
            ->first([ID, NAME]);

        if (!$product) {
            throw new ModelNotFoundException('This '.PRODUCT_MODEL.' is currently out of stock!');
        }

        $order_purchased = Order::query()->where(USER_ID, auth()->id())
            ->whereHas(ORDER_ITEMS, function ($orderItem) use ($product) {
                $orderItem->where(PRODUCT_NAME, $product->{NAME});
            });

        if ($order_purchased->get()->isEmpty()) {
            throw new BadRequestException('To be able to review this '.PRODUCT_MODEL.', <br> You must first purchase it!');
        }

        $order_completed = $order_purchased->whereStatus(4)->get();

        if ($order_completed->isEmpty()) {
            throw new BadRequestException('To be able to review this '.PRODUCT_MODEL.', <br> Your order should be completed!');
        }

        $review = Review::query()->whereHas(PRODUCT_MODEL, static fn($query) => $query->whereId($product_id_value)->whereStatus(1))
            ->where(USER_ID, auth()->id())
            ->where(ID, '<>', $review_id)
            ->exists();

        if ($review) {
            throw ValidationException::withMessages([
                REVIEW_MODEL.'_exists' => ['You have already reviewed this '.PRODUCT_MODEL.'. You can edit your '.REVIEW_MODEL.'!'],
            ]);
        }

        return Review::query()->updateOrCreate(
            [ID => $review_id],
            [
                $rating     => $rating_value,
                $title      => $title_value,
                $body_text  => $body_text_value,
                $product_id => $product_id_value,
                USER_ID     => auth()->id(),
            ]);
    }

    /**
     * Get the data of a specified review.
     *
     * @param Review $review
     * @return JsonResponse
     */
    final public function getReviewData(Review $review): JsonResponse
    {
        return responseWithData([REVIEW_MODEL => $review->data]);
    }

    /**
     * Delete a specified review.
     *
     * @param Review $review
     * @return bool
     */
    final public function deleteReview(Review $review): bool
    {
        return delete($review);
    }

    /**
     * Delete the selected reviews.
     *
     * @param Review $reviews
     * @return bool
     */
    final public function deleteMultipleReviews(Review $reviews): bool
    {
        return delete($reviews, true);
    }

}
