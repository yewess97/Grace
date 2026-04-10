<?php


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
        return str($text)->headline()
            ->lower()
            ->ucfirst()
            ->value();
    }
}


if (!function_exists('capitalizeSecond')) {
    /**
     * Format the text
     * by removing any underscore,
     * and capitalizing each word except the first.
     *
     * @param string $text
     * @return string
     */
    function capitalizeSecond(string $text): string
    {
        return str($text)->camel()->value();
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


if (!function_exists('toPastTense')) {
    /**
     * Convert a verb to its past tense.
     *
     * @param string $verb
     * @return string
     */
    function toPastTense(string $verb): string
    {
        // Load irregular verbs JSON
        $irregulars_json_file = file_get_contents(storage_path('app/public/irregular_verbs.json'));
        $irregulars           = json_decode($irregulars_json_file, true);

        // Check irregular verbs
        if (isset($irregulars[$verb])) {
            return $irregulars[$verb];
        }

        // Rules for regular verbs
        $last_char = substr($verb, -1);

        if ($last_char === 'e') {
            return $verb.'d'; // e.g., love -> loved
        }

        if ($last_char === 'y' && !preg_match('/[aeiou]y$/', $verb)) {
            return substr($verb, 0, -1).'ied'; // e.g., try -> tried
        }

        if (preg_match('/[aeiou][^aeiou]$/', $verb)) {
            return $verb.$last_char.'ed'; // e.g., stop -> stopped
        }

        return $verb.'ed'; // default
    }
}
