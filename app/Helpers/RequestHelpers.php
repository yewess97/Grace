<?php


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
     * Get the selected ids.
     *
     * @return string|null
     */
    function selectedIdsRequest(): string|null
    {
        return request()?->input('selected_'.pluralize(ID));
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
