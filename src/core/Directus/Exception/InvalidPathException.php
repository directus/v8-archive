<?php

namespace Directus\Exception;

class InvalidPathException extends Exception implements UnprocessableEntityExceptionInterface
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
