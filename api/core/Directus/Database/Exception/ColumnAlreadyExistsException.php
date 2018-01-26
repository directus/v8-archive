<?php

namespace Directus\Database\Exception;

use Directus\Exception\BadRequestException;

class ColumnAlreadyExistsException extends BadRequestException
{
    public function __construct($column)
    {
        parent::__construct(sprintf('Column %s already exists', $column));
    }
}
