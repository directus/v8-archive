<?php

namespace Directus\Custom\Hasher;

class CustomHasher implements \Directus\Hash\Hasher\HasherInterface
{
    public function getName()
    {
        return 'custom';
    }

    public function hash($string, array $options = [])
    {
        $letters = str_split($string);

        foreach ($letters as $i => $letter) {
            $letter = \ord($letter) + $i + 1;
            $letters[$i] = \chr($letter);
        }

        return implode('', $letters);
    }

    /**
     * {@inheritdoc}
     */
    public function verify($string, $hash, array $options = [])
    {
        return $hash === $this->hash($string, $options);
    }
}
