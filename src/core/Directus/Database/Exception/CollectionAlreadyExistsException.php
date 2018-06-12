<?php

namespace Directus\Database\Exception;

use Directus\Exception\BadRequestException;

class CollectionAlreadyExistsException extends BadRequestException
{
    public function __construct($collection)
    {
        $message = sprintf('Collection "%s" already exists', $collection);

        parent::__construct($message);
    }
}
