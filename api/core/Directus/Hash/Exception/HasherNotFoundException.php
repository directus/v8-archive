<?php

namespace Directus\Hash\Exception;

use Directus\Exception\BadRequestException;

class HasherNotFoundException extends BadRequestException
{
    const ERROR_CODE = 1000;

    public function __construct($algo, $message = '')
    {
        $message = __t('hasher_x_not_found', ['name' => $algo]);

        parent::__construct($message, static::ERROR_CODE);
    }
}
