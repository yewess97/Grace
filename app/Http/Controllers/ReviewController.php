<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\BadRequestException;
use Illuminate\Validation\ValidationException;
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
     * @param int $productId
     * @return string
     * @throws Throwable
     */
    final public function index(int $productId): string
    {
        return $this->reviewService->displayReviews($productId);
    }

    /**
     * Store or Update a review.
     *
     * @param string $operation
     * @return Response
     * @throws AuthenticationException|ModelNotFoundException|BadRequestException|ValidationException
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
        return $this->reviewService->getReviewData($review);
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
}
