<?php

namespace Directus\Exception\Http;

use Directus\Exception\Exception;

class ForbiddenException extends Exception
{
    protected $httpStatus = 403;
}
