<?php

namespace App\Services;

use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Notifications\NewReviewAdded;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ReviewService
{
    /**
     * Get the reviews.
     *
     * @return string
     * @throws Throwable
     */
    final public function getReviews(): string
    {
        return view(REVIEWS_COMPONENT, getReviews(request()?->input(PRODUCT_ID)))->render();
    }

    /**
     * Store or Update a review.
     *
     * @param string $operation
     * @return Review
     * @throws AuthenticationException|ModelNotFoundException|HttpException|ValidationException
     */
    final public function createOrUpdateReview(string $operation): Review
    {
        if (!auth()->check()) {
            throw new AuthenticationException('You must be logged in to '.REVIEW_MODEL.' the '.PRODUCT_MODEL.'!');
        }

        $review_request = new ReviewRequest($operation, REVIEW_MODEL, REVIEW_ATTRIBUTES);

        $review_id = request()?->input(UPDATE_REVIEW_ID);

        validateAttributes($review_request);

        [$rating, $title, $body_text, $product_id] = REVIEW_ATTRIBUTES;

        [$rating_value, $title_value, $body_text_value, $product_id_value] = $review_request->dataValues();

        $available_product = Product::query()->whereId($product_id_value)
            ->whereStatus(1)
            ->first([ID, NAME]);

        if (!$available_product) {
            throw new ModelNotFoundException('This '.PRODUCT_MODEL.' is currently out of stock!');
        }

        $order_purchased = Order::query()->whereHasAuthUser()
            ->whereHas(ORDER_ITEMS, function ($orderItem) use ($available_product) {
                $orderItem->where(PRODUCT_NAME, $available_product->{NAME});
            });

        if ($order_purchased->cursor()->isEmpty()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'To be able to '.REVIEW_MODEL.' this '.PRODUCT_MODEL.', <br> You must first purchase it!');
        }

        $order_completed = $order_purchased->whereStatus(4)->cursor();

        if ($order_completed->isEmpty()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'To be able to '.REVIEW_MODEL.' this '.PRODUCT_MODEL.', <br> Your order should be completed!');
        }

        $review = Review::query()->whereHas(PRODUCT_MODEL, static function ($product) use ($product_id_value) {
            return $product->whereId($product_id_value)->whereStatus(1);
        })
            ->where(USER_ID, auth()->id())
            ->where(ID, '<>', $review_id)
            ->exists();

        if ($review) {
            throw ValidationException::withMessages([
                REVIEW_MODEL.'_exists' => ['You have already reviewed this '.PRODUCT_MODEL.'. You can edit your '.REVIEW_MODEL.'!'],
            ]);
        }

        $review = Review::query()->updateOrCreate(
            [ID => $review_id],
            [
                $rating     => $rating_value,
                $title      => $title_value,
                $body_text  => $body_text_value,
                $product_id => $product_id_value,
                USER_ID     => auth()->id(),
            ]
        );

        sendNotificationToAdmins(new NewReviewAdded($review));

        return $review;
    }

    /**
     * Delete a specified review.
     *
     * @param Review $review
     * @return bool
     */
    final public function deleteReview(Review $review): bool
    {
        return customDelete($review, TITLE);
    }

    /**
     * Delete the selected reviews.
     *
     * @param Review $reviews
     * @return bool
     */
    final public function deleteMultipleReviews(Review $reviews): bool
    {
        return customDelete($reviews);
    }

    /**
     * Restore a specified review.
     *
     * @param Review $review
     * @return bool
     */
    final public function restoreReview(Review $review): bool
    {
        return restore($review, TITLE);
    }

    /**
     * Restore the selected reviews.
     *
     * @param Review $reviews
     * @return bool
     */
    final public function restoreMultipleReviews(Review $reviews): bool
    {
        return restore($reviews);
    }
}
