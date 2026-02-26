<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as ResponseClass;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Validator as ValidatorClass;
use Symfony\Component\HttpFoundation\Response;


if (!function_exists('responseSuccess')) {
    /**
     * Success Response.
     *
     * @return ResponseClass
     */
    function responseSuccess(): ResponseClass
    {
        return response()->noContent(Response::HTTP_OK);
    }
}


if (!function_exists('responseWithData')) {
    /**
     * Data Response.
     *
     * @param array $data
     * @return JsonResponse
     */
    function responseWithData(array $data): JsonResponse
    {
        return response()->json($data, Response::HTTP_OK);
    }
}


if (!function_exists('responseValidationError')) {
    /**
     * Validation Error Response.
     *
     * @param  ValidatorClass  $validator
     * @return JsonResponse
     */
    function responseValidationError(ValidatorClass $validator): JsonResponse
    {
        return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}


if (!function_exists('responseError')) {
    /**
     * Error Response.
     *
     * @param  string  $status
     * @return JsonResponse
     */
    function responseError(string $status): JsonResponse
    {
        return response()->json([STATUS => $status], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


if (!function_exists('ajaxPaginationResponse')) {
    /**
     * Generate a standardized AJAX response for paginated data updates.
     *
     * @param LengthAwarePaginator $collection
     * @param string $view
     * @param string $tableName
     * @param array $otherViewDataVars
     * @return JsonResponse
     * @throws Throwable
     */
    function ajaxPaginationResponse(LengthAwarePaginator $collection, string $view, string $tableName, array $otherViewDataVars = []): JsonResponse
    {
        $row          = view($view, [$tableName => $collection, ...$otherViewDataVars])->render();
        $current_page = $collection->currentPage();
        $per_page     = $collection->perPage();

        return responseWithData(compact(ROW, 'current_page', 'per_page'));
    }
}
