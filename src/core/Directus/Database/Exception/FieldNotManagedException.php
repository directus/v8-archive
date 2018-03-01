<?php

namespace Directus\Database\Exception;

use Directus\Exception\NotFoundException;

class FieldNotManagedException extends NotFoundException
{
    const ERROR_CODE = 206;

    public function __construct($field)
    {
        $message = sprintf('Field "%s" is not being managed by Directus', $field);
        parent::__construct($message, static::ERROR_CODE);
    }
}
