<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     * @throws AuthenticationException|ModelNotFoundException|HttpException|ValidationException
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
     */
    final public function destroy(Review $review): Response
    {
        $this->reviewService->deleteReview($review);

        return responseSuccess();
    }

    /**
     * Delete the selected reviews.
     *
     * @param Review $reviews
     * @return Response
     */
    final public function destroyMultiple(Review $reviews): Response
    {
        $this->reviewService->deleteMultipleReviews($reviews);

        return responseSuccess();
    }

    /**
     * Restore a specified review.
     *
     * @param Review $review
     * @return Response
     */
    final public function restore(Review $review): Response
    {
        $this->reviewService->restoreReview($review);

        return responseSuccess();
    }

    /**
     * Restore the selected reviews.
     *
     * @param Review $reviews
     * @return Response
     */
    final public function restoreMultiple(Review $reviews): Response
    {
        $this->reviewService->restoreMultipleReviews($reviews);

        return responseSuccess();
    }
}
