<?php

namespace Directus\Database\Exception;

use Directus\Exception\UnprocessableEntityException;

class UnknownDataTypeException extends UnprocessableEntityException
{
    const ERROR_CODE = 401;

    public function __construct($type)
    {
        parent::__construct('Unknown data type: ' . (string)$type);
    }
}
