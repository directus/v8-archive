<?php

namespace Directus\Database\Exception;

use Directus\Exception\UnauthorizedException;

class ForbiddenSystemTableDirectAccessException extends UnauthorizedException
{
    const ERROR_CODE = 201;

    public function __construct($table)
    {
        parent::__construct('direct_access_to_system_able_not_allowed', static::ERROR_CODE);
    }
}
