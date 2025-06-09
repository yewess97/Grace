<?php


if (!function_exists('array_from')) {
    /**
     * Create an array from received string.
     *
     * @param string $string
     * @return array
     */
    function array_from(string $string): array
    {
        $string = str_replace(['[', ']', '"', "'"], '', $string);
        
        return array_map('trim', explode(',', $string));
    }
}


if (!function_exists('object_from_array')) {
    /**
     * Convert an array of associative arrays to an array of objects.
     *
     * @param array $arrayOfArrays
     * @return array
     */
    function object_from_array(array $arrayOfArrays): array
    {
        return array_map(static fn($array) => (object) $array, $arrayOfArrays);
    }
}


if (!function_exists('capitalizeAll')) {
    /**
     * Format the text
     * by replacing any underscore with a space,
     * and capitalizing the first letter of each word.
     *
     * @param string $text
     * @return string
     */
    function capitalizeAll(string $text): string
    {
        return str($text)->headline()->value();
    }
}


if (!function_exists('capitalizeAllFromSecondWord')) {
    /**
     * Format the text
     * by removing any underscore,
     * and capitalizing each word except the first.
     *
     * @param string $text
     * @return string
     */
    function capitalizeAllFromSecondWord(string $text): string
    {
        return str($text)->camel()->value();
    }
}


if (!function_exists('kebabAll')) {
    /**
     * Format the text
     * by converting it to kebab case (dashes between words).
     *
     * @param string $text
     * @return string
     */
    function kebabAll(string $text): string
    {
        return str_replace('_', '-', $text);
    }
}


if (!function_exists('capitalizeFirst')) {
    /**
     * Format the text
     * by replacing underscores with spaces,
     * and capitalizing only the first word.
     *
     * @param string $text
     * @return string
     */
    function capitalizeFirst(string $text): string
    {
        return str($text)->headline()->lower()->ucfirst()->value();
    }
}


if (!function_exists('singularize')) {
    /**
     * Singularize a Text/Word.
     *
     * @param string $string
     * @return string
     */
    function singularize(string $string): string
    {
        return str($string)->singular()->value();
    }
}


if (!function_exists('pluralize')) {
    /**
     * Pluralize a Text/Word.
     *
     * @param string $string
     * @param int $count
     * @return string
     */
    function pluralize(string $string, int $count = 2): string
    {
        return str($string)->plural($count)->value();
    }
}
