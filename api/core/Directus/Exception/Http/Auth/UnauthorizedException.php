<?php

namespace Directus\Exception\Http\Auth;

class UnauthorizedException extends \Directus\Permissions\Exception\UnauthorizedException
{
    // status code = 401
}
