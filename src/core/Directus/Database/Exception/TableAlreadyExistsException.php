<?php

namespace Directus\Database\Exception;

use Directus\Exception\BadRequestException;
use Exception;

class TableAlreadyExistsException extends BadRequestException
{
    public function __construct($table, $message = '', $code = 0, Exception $previous = null)
    {
        if ($message === '') {
            $message = __t('table_x_already_exists', ['table_name' => $table]);
        }

        parent::__construct($message);
    }
}
