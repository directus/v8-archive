<?php

namespace Directus\Filesystem\Exception;

class FilesystemNotFoundException extends FilesystemException
{
    const ERROR_CODE = 609;

    public function __construct($key)
    {
        $message = sprintf('Filesystem with key: "%s" not found', $key);

        parent::__construct($message);
    }
}
