<?php

namespace Directus\Config;

/**
 * Config context interface
 */
class Source
{
    /**
     * Transforms an array of strings into a complex object
     * @example
     *  $obj = Source::expand(['a', 'b', 'c'], 12345);
     *  $obj == [
     *    'a' => [
     *      'b' => [
     *        'c' => 12345
     *      ]
     *    ]
     *  ];
     */
    private static function expand(&$target, $path, $value)
    {
        $segment = array_shift($path);
        if (sizeof($path) === 0) { // leaf
            $target[$segment] = $value;
            return;
        }
        if (!isset($target[$segment])) {
            $target[$segment] = [];
        }
        Source::expand($target[$segment], $path, $value);
    }

    /**
     * Normalize the array indexes
     */
    private static function normalize(&$target) {
        if (!is_array($target)) {
            return;
        }

        $sort = false;
        foreach ($target as $key => $value) {
            Source::normalize($target[$key]);
            $sort |= is_numeric($key);
        }

        if ($sort) {
            // TODO: which one?
            sort($target, SORT_NUMERIC);
            // vs.
            //$target = array_values($target);
        }
    }

    /**
     * Source
     */
    public static function map($source) {
        $target = [];
        foreach ($source as $key => $value) {
            Source::expand($target, explode('_', strtolower($key)), $value);
        }
        Source::normalize($target);
        return $target;
    }

    /**
     * Create
     */
    public static function from_env()
    {
        $context = $_ENV;
        if (empty($context)) {
            throw new \Error('No environment variables available because of "variables_order" value.');
        }
        return Source::map($context);
    }

    /**
     * Loads variables from PHP file
     */
    public static function from_php($file) {
        return require $file;
    }

    /**
     * Loads variables from JSON file
     */
    public static function from_json($file) {
        return json_decode(file_get_contents($file));
    }
}
