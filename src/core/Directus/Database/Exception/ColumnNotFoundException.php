<?php

namespace Directus\Database\Exception;

use Directus\Exception\NotFoundException;

class ColumnNotFoundException extends NotFoundException
{
    const ERROR_CODE = 202;

    public function __construct($column)
    {
        $message = __t('unable_to_find_column_x', ['column' => $column]);

        parent::__construct($message, static::ERROR_CODE);
    }
}
