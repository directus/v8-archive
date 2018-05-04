<?php

if (!function_exists('array_get')) {
    function array_get(array $array, $key, $default = null)
    {
        return \Directus\Util\ArrayUtils::get($array, $key, $default);
    }
}
