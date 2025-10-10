<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class ReviewController extends Controller
{
    /**
     * Review Controller Constructor.
     *
     * @param ReviewService $reviewService
     * @return void
     */
    final public function __construct(private readonly ReviewService $reviewService){}

    /**
     * Display the reviews' resource.
     *
     * @return string
     * @throws Throwable
     */
    final public function index(): string
    {
        return $this->reviewService->getReviews();
    }

    /**
     * Store or Update a review.
     *
     * @param string $operation
     * @return Response
     * @throws AuthenticationException|ModelNotFoundException|HttpException|ValidationException|CacheInvalidArgumentException
     */
    final public function storeOrUpdate(string $operation): Response
    {
        $this->reviewService->createOrUpdateReview($operation);

        return responseSuccess();
    }

    /**
     * Get the data of a specified review.
     *
     * @param Review $review
     * @return JsonResponse
     */
    final public function edit(Review $review): JsonResponse
    {
        return responseWithData([REVIEW_MODEL => $review->data]);
    }

    /**
     * Delete a specified review.
     *
     * @param Review $review
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroy(Review $review): Response
    {
        $review_deleted = $this->reviewService->deleteReview($review);

        return $review_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.REVIEW_MODEL.' you are trying to '.REMOVE.'/'.DELETE.' is not found!');
    }

    /**
     * Delete the selected reviews.
     *
     * @param Review $reviews
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroyMultiple(Review $reviews): Response
    {
        $reviews_deleted = $this->reviewService->deleteMultipleReviews($reviews);

        return $reviews_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.REVIEWS_TABLE.' (or some of them) you are trying to '.REMOVE.'/'.DELETE.' are not found!');
    }

    /**
     * Restore a specified review.
     *
     * @param Review $review
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restore(Review $review): Response
    {
        $review_restored = $this->reviewService->restoreReview($review);

        return $review_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.REVIEW_MODEL.' you are trying to '.RESTORE.' is not found!');
    }

    /**
     * Restore the selected reviews.
     *
     * @param Review $reviews
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restoreMultiple(Review $reviews): Response
    {
        $reviews_restored = $this->reviewService->restoreMultipleReviews($reviews);

        return $reviews_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.REVIEWS_TABLE.' (or some of them) you are trying to '.RESTORE.' are not found!');
    }
}
