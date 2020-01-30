<?php

declare(strict_types=1);

namespace Directus\Core\Options\Exception;

/**
 * Empty schema exception.
 */
class MissingOptions extends OptionsException
{
    /**
     * Constructs the exception.
     *
     * @param array $keys
     */
    public function __construct(array $keys = [])
    {
        parent::__construct('Missing required options: '.implode(', ', $keys));
    }
}
