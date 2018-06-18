<?php

namespace Directus\Database\Exception;

use Directus\Exception\UnprocessableEntity;

class UnknownDataTypeException extends UnprocessableEntity
{
    const ERROR_CODE = 401;

    public function __construct($type)
    {
        parent::__construct('Unknown data type: ' . (string)$type);
    }
}
