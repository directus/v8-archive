<?php

namespace Directus\Exception;

class Exception extends \Exception
{
    const ERROR_CODE = 0;

    /**
     * Allows child class to extend the error code value method
     *
     * @return int
     */
    public function getErrorCode()
    {
        return static::ERROR_CODE;
    }
}
