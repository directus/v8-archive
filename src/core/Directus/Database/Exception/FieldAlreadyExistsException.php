<?php

namespace Directus\Database\Exception;

use Directus\Exception\BadRequestException;

class FieldAlreadyExistsException extends BadRequestException
{
    public function __construct($field)
    {
        parent::__construct(sprintf('Field "%s" already exists', $field));
    }
}
