<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as ResponseClass;
use Illuminate\Validation\Validator as ValidatorClass;
use Symfony\Component\HttpFoundation\Response;


if (!function_exists('responseSuccess')) {
    /**
     * Success Response.
     *
     * @param string|null $status
     * @param array $otherVars
     * @return JsonResponse|ResponseClass
     */
    function responseSuccess(?string $status = null, array $otherVars = []): JsonResponse|ResponseClass
    {
        if ($status || $otherVars) {
            return response()->json(['status' => $status, ...$otherVars], Response::HTTP_OK);
        }

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
        return response()->json(['status' => $status], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
