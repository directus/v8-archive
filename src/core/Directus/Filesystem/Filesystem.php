<?php

namespace Directus\Filesystem;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\FilesystemInterface as FlysystemInterface;

class Filesystem
{
    /**
     * @var FlysystemInterface
     */
    private $adapter;

    public function __construct(FlysystemInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return $this->adapter->has($path);
    }

    /**
     * Get the filesystem adapter (flysystem object)
     *
     * @return FlysystemInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Get Filesystem adapter path
     *
     * @param string $path
     * @return string
     */
    public function getPath($path = '')
    {
        $adapter = $this->adapter->getAdapter();

        if ($path) {
            return $adapter->applyPathPrefix($path);
        }

        return $adapter->getPathPrefix();
    }
}
