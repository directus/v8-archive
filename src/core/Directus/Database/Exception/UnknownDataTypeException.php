<?php

namespace Directus\Database\Exception;

use Directus\Exception\BadRequestExceptionInterface;
use Directus\Exception\Exception;

class UnknownDataTypeException extends Exception implements BadRequestExceptionInterface
{
    const ERROR_CODE = 401;

    public function __construct($type)
    {
        parent::__construct('Unknown data type: ' . (string)$type);
    }
}
