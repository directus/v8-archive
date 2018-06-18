<?php

namespace Directus\Validator\Exception;

use Directus\Exception\UnprocessableEntity;

class InvalidRequestException extends UnprocessableEntity
{
    const ERROR_CODE = 4;
}
