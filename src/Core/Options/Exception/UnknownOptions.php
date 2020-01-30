<?php

declare(strict_types=1);

namespace Directus\Core\Options\Exception;

/**
 * Empty schema exception.
 */
class UnknownOptions extends OptionsException
{
    /**
     * Constructs the exception.
     *
     * @param array $keys
     */
    public function __construct(array $keys = [])
    {
        parent::__construct('Unknown options: '.implode(', ', $keys));
    }
}
