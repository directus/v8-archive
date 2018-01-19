<?php

namespace Directus\Hash\Hasher;

abstract class AbstractHashHasher implements HasherInterface
{
    /**
     * @inheritdoc
     */
    public function hash($string, array $options = [])
    {
        return hash($this->getName(), $string);
    }
}
