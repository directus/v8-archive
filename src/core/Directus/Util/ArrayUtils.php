<?php

namespace Directus\Util;

class ArrayUtils
{
    /**
     * Sets a value in the given array with the given key.
     *
     * @param string $key
     * @param mixed  $value
     */
    public static function set(array &$array, $key, $value)
    {
        $keys = explode('.', $key);

        while (\count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Get an item from an array.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (false !== strpos($key, '.')) {
            $array = static::findDot($array, $key);

            if (static::exists($array, $key)) {
                return $array[$key];
            }
        }

        return $default;
    }

    /**
     * Gets and remove an item from the array.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function pull(array &$array, $key, $default = null)
    {
        // TODO: Implement access by separator (example dot-notation)
        $value = self::get($array, $key, $default);

        self::remove($array, $key);

        return $value;
    }

    public static function has($array, $key)
    {
        if (static::exists($array, $key)) {
            return true;
        }

        if (false === strpos($key, '.')) {
            return false;
        }

        $array = static::findDot($array, $key);

        return static::exists($array, $key);
    }

    public static function exists($array, $key)
    {
        return \array_key_exists($key, $array);
    }

    /**
     * Filter an array by keys.
     *
     * @param $array
     * @param $keys
     * @param bool $omit
     *
     * @return array
     */
    public static function filterByKey($array, $keys, $omit = false)
    {
        $result = [];

        if (\is_string($keys)) {
            $keys = [$keys];
        }

        foreach ($array as $key => $value) {
            $condition = \in_array($key, $keys, true);
            if ($omit) {
                $condition = !$condition;
            }

            if ($condition) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Return a copy of the object, filtered to only have values for the whitelisted keys (or array of valid keys).
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function pick($array, $keys)
    {
        return static::filterByKey($array, $keys);
    }

    /**
     * Return a copy of the object, filtered to omit values for the blacklisted keys (or array of valid keys).
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function omit($array, $keys)
    {
        return static::filterByKey($array, $keys, true);
    }

    /**
     * Return whether or not a set of keys exists in an array.
     *
     * @param array|mixed $keys
     *
     * @return bool
     */
    public static function contains(array $array, $keys)
    {
        if (!\is_array($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            if (!\array_key_exists($key, $array)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether the given array contain at least one of the given keys.
     *
     * @return bool
     */
    public static function containsSome(array $array, array $keys)
    {
        foreach ($keys as $key) {
            if (static::has($array, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Flatten a multi-dimensional associative array with a dots.
     *
     * @param array  $array
     * @param string $prepend
     *
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        return static::flatKey('.', $array, $prepend);
    }

    /**
     * Find a the value of an array based on a relational key (nested value).
     *
     * This is a better option than using dot
     * Dot flatten ALL keys which make thing slower when the array is big
     * to just find one value
     *
     * @param $array
     * @param $key
     *
     * @return array
     */
    public static function findDot($array, $key)
    {
        $result = static::findFlatKey('.', $array, $key);

        return $result ? [$result['key'] => $result['value']] : [];
    }

    /**
     * Flatten a multi-dimensional associative array with a character.
     *
     * @param string $separator
     * @param array  $array
     * @param string $prepend
     *
     * @return array
     */
    public static function flatKey($separator, $array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                $results = array_merge($results, static::flatKey($separator, $value, $prepend.$key.$separator));
            }

            $results[$prepend.$key] = $value;
        }

        return $results;
    }

    /**
     * Find the nested value of an array using the given separator-notation key.
     *
     * @param $separator
     * @param $array
     * @param $key
     *
     * @return null|array
     */
    public static function findFlatKey($separator, $array, $key)
    {
        $keysPath = [];
        $result = null;

        if (false !== strpos($key, $separator)) {
            $keys = explode($separator, $key);
            $value = $array;

            while ($keys) {
                $k = array_shift($keys);

                if (!\array_key_exists($k, $value)) {
                    break;
                }

                $value = $value[$k];
                $keysPath[] = $k;

                if ($key === implode($separator, $keysPath)) {
                    $result = [
                        'key' => $key,
                        'value' => $value,
                    ];
                }

                // stop the search if the next value is not an array
                if (!\is_array($value)) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Gets the missing values from a array in another array.
     *
     * @return array
     */
    public static function missing(array $from, array $target)
    {
        return static::intersection($from, $target, true);
    }

    /**
     * Gets the missing values from a array in another array.
     *
     * Alias of ArrayUtils::missing
     *
     * @return array
     */
    public static function without(array $from, array $target)
    {
        return static::missing($from, $target);
    }

    /**
     * Gets only the values that exists in both array.
     *
     * @param bool $without
     *
     * @return array
     */
    public static function intersection(array $arrayOne, array $arrayTwo, $without = false)
    {
        $values = [];

        foreach ($arrayTwo as $value) {
            if (\in_array($value, $arrayOne, true) === !$without) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * Checks whether the given array has only numeric keys.
     *
     * @return bool
     */
    public static function isNumericKeys(array $array)
    {
        foreach (array_keys($array) as $key) {
            if (!is_numeric($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets or updates the keys in the source array into the default array.
     *
     * @param \Closure $validate
     *
     * @return array
     *
     * @see http://php.net/manual/es/function.array-merge-recursive.php#92195
     */
    public static function defaults(array $defaultArray, array $sourceArray, \Closure $validate = null)
    {
        $newArray = $defaultArray;
        foreach ($sourceArray as $key => $value) {
            if ($validate && true !== $validate($value, $key)) {
                continue;
            }

            if (\is_array($value) && \array_key_exists($key, $defaultArray) && \is_array($defaultArray[$key])) {
                $newArray[$key] = static::defaults($newArray[$key], $value);
            } else {
                $newArray[$key] = $value;
            }
        }

        return $newArray;
    }

    /**
     * Gets a new array changing some or all of the given array keys.
     *
     * @return array
     */
    public static function aliasKeys(array $array, array $aliases)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            if (\in_array($key, $aliases, true)) {
                $newArray[array_search($key, $aliases, true)] = $value;
            } else {
                $newArray[$key] = $value;
            }
        }

        return $newArray;
    }

    /**
     * Renames a key.
     *
     * @param string $from
     * @param string $to
     */
    public static function rename(array &$array, $from, $to)
    {
        if (self::exists($array, $from)) {
            $value = self::get($array, $from);

            $array[$to] = $value;

            self::remove($array, $from);
        }
    }

    /**
     * Rename a list of keys.
     *
     * @param $keys
     */
    public static function renameSome(array &$array, $keys)
    {
        foreach ($keys as $from => $to) {
            static::rename($array, $from, $to);
        }
    }

    /**
     * Swaps element values.
     *
     * @param string $from
     * @param string $to
     *
     * @return int
     */
    public static function swap(array &$array, $from, $to)
    {
        $temp = self::get($array, $from);
        $array[$from] = self::get($array, $to);
        $array[$to] = $temp;
    }

    /**
     * Pushes a new element at the end of the given array.
     *
     * @param $value
     *
     * @return int
     */
    public static function push(array &$array, $value)
    {
        return array_push($array, $value);
    }

    /**
     * Pulls the last element from the given array.
     *
     * @return mixed
     */
    public static function pop(array &$array)
    {
        return array_pop($array);
    }

    /**
     * Pulls the first element from the given array.
     *
     * @return mixed
     */
    public static function shift(array &$array)
    {
        return array_shift($array);
    }

    /**
     * Adds a new element at the beginning of the given array.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function unshift(array &$array, $value)
    {
        return array_unshift($array, $value);
    }

    /**
     * Removes one or more key from the given array.
     *
     * @param $keys
     */
    public static function remove(array &$array, $keys)
    {
        if (!\is_array($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            unset($array[$key]);
        }
    }

    /**
     * Gets how deep is the given array.
     *
     * 0 = no child
     *
     * @param array $array
     *
     * @return int
     */
    public static function deepLevel($array)
    {
        $depth = 0;

        foreach ($array as $value) {
            if (\is_array($value)) {
                $depth = max(static::deepLevel($value) + 1, $depth);
            }
        }

        return $depth;
    }

    /**
     * Extract the key from a list of array.
     *
     * @param string $key
     *
     * @return array
     */
    public static function pluck(array $array, $key)
    {
        return array_map(function ($value) use ($key) {
            return \is_array($value) ? static::get($value, $key) : null;
        }, $array);
    }

    /**
     * Creates an array from CSV value.
     *
     * @param mixed $value
     *
     * @return array
     */
    public static function createFromCSV($value)
    {
        if (\is_array($value)) {
            return $value;
        }

        if (\is_string($value)) {
            return StringUtils::csv($value);
        }

        return [];
    }
}
