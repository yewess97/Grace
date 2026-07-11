<?php

use Illuminate\Database\Eloquent\Model;

if (!function_exists('currentPageRequest')) {
    /**
     * Get the current page number.
     *
     * @return int
     */
    function currentPageRequest(): int
    {
        return request()?->input('page', 1);
    }
}


if (!function_exists('conditionRequest')) {
    /**
     * Get the current condition.
     *
     * @return string|null
     */
    function conditionRequest(): string|null
    {
        return request()?->input(CONDITION);
    }
}


if (!function_exists('selectedIdsRequest')) {
    /**
     * Get the selected IDs from the request,
     * either from the model's ID or from a comma-separated list in the request input.
     *
     * @param Model|stdClass $model
     * @return array
     */
    function selectedIdsRequest(Model|stdClass $model): array
    {
        $selected_ids = $model->{ID}
            ? [$model->{ID}]
            : array_map('intval', array_filter(
                array_map('trim', explode(',', request()?->input('selected_'.pluralize(ID))))
            ));

        // Filter out nulls to prevent whereIn('id', [null])
        return array_filter($selected_ids);
    }
}


if (!function_exists('checkImageBackgroundRequest')) {
    /**
     * Check whether the uploaded image without background.
     *
     * @return string|null
     */
    function checkImageBackgroundRequest(): string|null
    {
        return request()?->input('check_image_background');
    }
}
