<?php

namespace Directus\Exception;

class PrivateProjectException extends Exception implements UnauthorizedExceptionInterface
{
    const ERROR_CODE = 24;

    public function __construct()
    {
        parent::__construct("Access denied", static::ERROR_CODE);
    }
}
