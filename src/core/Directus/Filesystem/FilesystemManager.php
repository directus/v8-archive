<?php

namespace Directus\Filesystem;

use Directus\Exception\Exception;
use Directus\Filesystem\Exception\FilesystemNotFoundException;

use Directus\Util\ArrayUtils;

class FilesystemManager
{
    /**
     * @var Filesystem[]
     */
    protected $filesystems = [];

    public function __construct(array $filesystems = [])
    {
        foreach ($filesystems as $key => $filesystem) {
            $this->register($key, $filesystem);
        }
    }

    /**
     * Register a filesystem
     *
     * @param string $key
     *
     * @param Filesystem $filesystem
     */
    public function register($key, Filesystem $filesystem)
    {
        $this->filesystems[$key] = $filesystem;
    }

    /**
     * Gets a filesystem with the given key
     *
     * @param string $key
     *
     * @return Filesystem
     *
     * @throws FilesystemNotFoundException
     */
    public function get($key)
    {
        $filesystem = ArrayUtils::get($this->filesystems, $key);

        if (!$filesystem) {
            throw new FilesystemNotFoundException($key);
        }

        return $filesystem;
    }

    /**
     * Gets the default filesystem
     *
     * @return Filesystem
     *
     * @throws Exception
     */
    public function getDefault()
    {
        if (count($this->filesystems) === 0) {
            throw new Exception('There is not filesystem registered');
        }

        return reset($this->filesystems);
    }
}
