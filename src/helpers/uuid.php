<?php

if (!function_exists('generate_uuid1')) {
    /**
     * Generates a UUID v1 string
     *
     * @return string
     */
    function generate_uui1()
    {
        return \Ramsey\Uuid\Uuid::uuid1()->toString();
    }
}

if (!function_exists('generate_uuid3')) {
    /**
     * Generates a UUID v3 string
     *
     * @param string $namespace
     * @param string $name
     *
     * @return string
     */
    function generate_uui3($namespace, $name)
    {
        return \Ramsey\Uuid\Uuid::uuid3(
            $namespace,
            $name
        )->toString();
    }
}

if (!function_exists('generate_uuid4')) {
    /**
     * Generates a UUID v4 string
     *
     * @return string
     */
    function generate_uui4()
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
