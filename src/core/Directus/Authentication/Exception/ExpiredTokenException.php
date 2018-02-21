<?php

namespace Directus\Authentication\Exception;

use Directus\Exception\UnauthorizedException;

class ExpiredTokenException extends UnauthorizedException
{
    const ERROR_CODE = 102;

    public function __construct()
    {
        parent::__construct('auth_expired_token', static::ERROR_CODE);
    }
}
