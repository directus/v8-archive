<?php

namespace Directus\Exception;

class UnprocessableEntity extends Exception implements UnprocessableEntityExceptionInterface
{
    const ERROR_CODE = 12;
}
