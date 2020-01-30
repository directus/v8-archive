<?php

declare(strict_types=1);

namespace Directus\Core\Options\Exception;

/**
 * Empty schema exception.
 */
class InvalidOption extends OptionsException
{
    /**
     * Constructs the exception.
     *
     * @param string $option
     */
    public function __construct(string $option)
    {
        parent::__construct("Invalid option value: ${option}");
    }
}
