<?php

namespace Directus\Database\Exception;

use Directus\Exception\UnprocessableEntity;

class CollectionAlreadyExistsException extends UnprocessableEntity
{
    const ERROR_CODE = 307;

    public function __construct($collection)
    {
        $message = sprintf('Collection "%s" already exists', $collection);

        parent::__construct($message);
    }
}
