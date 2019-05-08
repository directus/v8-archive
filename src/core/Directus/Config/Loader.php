<?php

namespace Directus\Config;

/**
 * Config loader interface
 */
class Loader
{
    /**
     * Transforms an array of strings into a complex object
     * @example
     *  $obj = Loader::build(['a', 'b', 'c'], 12345);
     *  $obj == [
     *    'a' => [
     *      'b' => [
     *        'c' => 12345
     *      ]
     *    ]
     *  ];
     */
    private static function build($path, $value)
    {
        $segment = array_shift($path);
        if (sizeof($path) === 0) {
            return [
                $segment => $value
            ];
        }
        return [
            $segment => Loader::build($path, $value)
        ];
    }

    /**
     * Normalizes the value map into a complex object.
     *
     * @return object
     */
    private static function normalize($values, $prefix = '')
    {
        $map = [];
        $path = [];

        if ($prefix !== '' && substr($prefix, -1) !== '_') {
            $path[] = $prefix;
            $prefix .= '_';
        }

        $length = strlen($prefix);
        $keys = array_filter($values, function($key) use ($prefix, $length) {
            return substr($key, 0, $length) === $prefix;
        }, ARRAY_FILTER_USE_KEY);

        foreach ($keys as $key => $value) {
            $key = substr($key, $length);
            $map = array_merge_recursive($map, Loader::build(array_merge($path, explode('_', $key)), $value));
        }

        return $map;
    }

    /**
     * Create
     */
    public static function load($values, $prefix = '')
    {
        $values = Loader::normalize($values, $prefix);
        return $values;
    }
}
