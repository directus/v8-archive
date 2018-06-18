<?php

namespace Directus\Database\Exception;

use Directus\Exception\UnprocessableEntity;

class FieldAlreadyExistsException extends UnprocessableEntity
{
    const ERROR_CODE = 308;

    public function __construct($field)
    {
        parent::__construct(sprintf('Field "%s" already exists', $field));
    }
}
