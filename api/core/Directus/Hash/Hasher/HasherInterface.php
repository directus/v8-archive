<?php

namespace Directus\Hash\Hasher;

interface HasherInterface
{
    /**
     * Get the hasher unique name
     *
     * @return string
     */
    public function getName();

    /**
     * Hash the given string
     *
     * @param string $string
     * @param array $options
     *
     * @return string
     */
    public function hash($string, array $options = []);
}
