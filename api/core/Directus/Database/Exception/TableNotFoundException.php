<?php

namespace Directus\Database\Exception;

use Directus\Exception\NotFoundException;
use Exception;

class TableNotFoundException extends NotFoundException
{
    const ERROR_CODE = 200;

    public function __construct($table)
    {
        $message = __t('unable_to_find_table_x', ['table_name' => $table]);

        parent::__construct($message, static::ERROR_CODE);
    }
}
