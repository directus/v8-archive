<?php

namespace Directus\Authentication\Exception;

use Directus\Exception\Exception;

class UserNotAuthenticatedException extends Exception
{
    const ERROR_CODE = 108;

    public function __construct($message = 'User not authenticated')
    {
        parent::__construct($message, static::ERROR_CODE);
    }
}
