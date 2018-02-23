<?php

namespace Directus\Database\Exception;

use Directus\Exception\NotFoundException;

class ItemNotFoundException extends NotFoundException
{
    const ERROR_CODE = 203;

    public function __construct($message = '')
    {
        parent::__construct($message, static::ERROR_CODE);
    }
}
