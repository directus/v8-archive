<?php

namespace Directus\Database\Exception;

use Directus\Exception\UnprocessableEntityException;

class SuppliedArrayAsColumnValue extends UnprocessableEntityException
{
    const ERROR_CODE = 213;

    public function __construct($collection, $field)
    {
        $message = 'Attempting to write an array as the value for column `' . $collection . '`.`' . $field . '.';
        parent::__construct($message, 0, null);
    }
}
