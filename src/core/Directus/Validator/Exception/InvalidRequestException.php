<?php

namespace Directus\Validator\Exception;

use Directus\Exception\BadRequestException;

class InvalidRequestException extends BadRequestException
{
    const ERROR_CODE = 4;
}
