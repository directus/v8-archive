<?php

namespace Directus\Hash\Hasher;

interface HasherInterface
{
    /**
     * Get the hasher unique name.
     *
     * @return string
     */
    public function getName();

    /**
     * Hash the given string.
     *
     * @param string $string
     *
     * @return string
     */
    public function hash($string, array $options = []);

    /**
     * Verifies whether a given string match a hash in the given algorithm.
     *
     * @param string $string
     * @param string $hash
     *
     * @return string
     */
    public function verify($string, $hash, array $options = []);
}
