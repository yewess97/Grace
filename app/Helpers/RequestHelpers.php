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


if (!function_exists('trashedConditionRequest')) {
    /**
     * Get the trashed condition.
     *
     * @return string|null
     */
    function trashedConditionRequest(): string|null
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
