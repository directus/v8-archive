<?php

namespace Directus\Authentication\Exception;

use Directus\Exception\UnauthorizedException;

class UserInactiveException extends UnauthorizedException
{
    const ERROR_CODE = 104;
}
