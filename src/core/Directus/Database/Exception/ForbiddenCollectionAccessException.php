<?php

namespace Directus\Database\Exception;

use Directus\Exception\ForbiddenException;

class ForbiddenCollectionAccessException extends ForbiddenException
{
    const ERROR_CODE = 204;

    public function __construct($collection)
    {
        parent::__construct(sprintf('Cannot access collection: %s', $collection), static::ERROR_CODE);
    }
}
