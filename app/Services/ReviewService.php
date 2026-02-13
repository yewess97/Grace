<?php

namespace App\Services;

use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Notifications\NewReviewAdded;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
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
     * @throws AuthenticationException|ModelNotFoundException|HttpException|ValidationException|CacheInvalidArgumentException
     */
    final public function createOrUpdateReview(string $operation): Review
    {
        ensureAuthenticated();

        $review_request = new ReviewRequest($operation, REVIEW_MODEL, REVIEW_ATTRIBUTES);

        $review_id = request()?->input(UPDATE_REVIEW_ID);

        validateAttributes($review_request);

        [$rating, $title, $body_text, $product_id] = REVIEW_ATTRIBUTES;

        [$rating_value, $title_value, $body_text_value, $product_id_value] = $review_request->dataValues();

        $product         = $this->getProductOrFail($product_id_value);
        $order_purchased = $this->getPurchasedOrderOrFail($product);
        $order_completed = $this->getCompletedOrderOrFail($order_purchased);

        $this->checkReviewExistingOrFail($product_id_value);

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

        $this->forgetReviewCache($review);

        sendNotificationToAdmins(new NewReviewAdded($review));

        return $review;
    }

    /**
     * Delete a specified review.
     *
     * @param Review $review
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteReview(Review $review): bool
    {
        $deleted_review = removeDeleteOrRestore($review, $review->{TITLE});

        $this->forgetReviewCache($review);

        return $deleted_review;
    }

    /**
     * Delete the selected reviews.
     *
     * @param Review $reviews
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteMultipleReviews(Review $reviews): bool
    {
        $deleted_reviews = removeDeleteOrRestore($reviews);

        $this->forgetReviewCache($reviews);

        return $deleted_reviews;
    }

    /**
     * Restore a specified review.
     *
     * @param Review $review
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreReview(Review $review): bool
    {
        $restored_review = removeDeleteOrRestore($review, $review->{TITLE});

        $this->forgetReviewCache($review);

        return $restored_review;
    }

    /**
     * Restore the selected reviews.
     *
     * @param Review $reviews
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreMultipleReviews(Review $reviews): bool
    {
        $restored_reviews = removeDeleteOrRestore($reviews);

        $this->forgetReviewCache($reviews);

        return $restored_reviews;
    }

    /**
     * Get the first product or throw an exception if none exists.
     *
     * @param int $productId
     * @return Product
     * @throws ModelNotFoundException
     */
    private function getProductOrFail(int $productId): Product
    {
        $product = Product::query()->whereId($productId)
            ->whereStatus(1)
            ->withoutTrashed()
            ->first([ID, NAME]);

        if (!$product) {
            throw new ModelNotFoundException('This '.PRODUCT_MODEL.' is currently out of stock!');
        }

        return $product;
    }

    /**
     * Get the first purchased order or throw an exception if none exists.
     *
     * @param Product $product
     * @return Builder
     * @throws HttpException
     */
    private function getPurchasedOrderOrFail(Product $product): Builder
    {
        $purchased_order = Order::query()->whereHasAuthUser()
            ->whereHas(ORDER_ITEMS, function ($orderItem) use ($product) {
                $orderItem->where(PRODUCT_NAME, $product->{NAME});
            })
            ->withoutTrashed();

        if ($purchased_order->cursor()->isEmpty()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'To be able to '.REVIEW_MODEL.' this '.PRODUCT_MODEL.', <br> You must first purchase it!');
        }

        return $purchased_order;
    }


    /**
     * Get the first completed order or throw an exception if none exists.
     *
     * @param Builder $order
     * @return Builder
     * @throws HttpException
     */
    private function getCompletedOrderOrFail(Builder $order): Builder
    {
        $completed_order = $order->whereStatus(4)
            ->withoutTrashed()
            ->cursor();

        if ($completed_order->isEmpty()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'To be able to '.REVIEW_MODEL.' this '.PRODUCT_MODEL.', <br> Your order should be completed!');
        }

        return $completed_order;
    }

    /**
     * Throw an exception if the user has already reviewed this product.
     *
     * @param int $productId
     * @return void
     * @throws ValidationException
     */
    private function checkReviewExistingOrFail(int $productId): void
    {
        $review_exists = Review::query()->when($review_id, static fn($review) => $review->whereKeyNot($review_id))
            ->whereHas(PRODUCT_MODEL, static function ($product) use ($productId) {
                return $product->whereId($productId)->whereStatus(1);
            })
            ->where(USER_ID, auth()->id())
            ->withoutTrashed()
            ->exists();

        if ($review_exists) {
            throw ValidationException::withMessages([
                REVIEW_MODEL.'_exists' => ['You have already '.toPastTense(REVIEW_MODEL).' this '.PRODUCT_MODEL.'. You can edit your '.REVIEW_MODEL.'!'],
            ]);
        }
    }

    /**
     * Forget the review cache.
     *
     * @param Review $review
     * @return void
     * @throws CacheInvalidArgumentException
     */
    final public function forgetReviewCache(Review $review): void
    {
        forgetCache(REVIEWS_PAGINATION_CACHE_KEY, $review, RATING, [
            'relation' => PRODUCT_MODEL,
            'relation_only_columns' => [
                PRODUCT_MODEL => [ID, SLUG],
            ],
            'unique_by'  => SLUG,
            'cache_keys' => [
                static fn($product) => PRODUCT_MODEL.'_'.$product[SLUG],
                static fn($product) => AVERAGE_RATE.'_'.$product[ID],
            ],
        ]);
    }
}
