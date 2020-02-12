<?php

declare(strict_types=1);

namespace Directus\Core;

use Directus\Core\Config\ConfigInterface;

/**
 * Directus project.
 */
final class Project
{
    /**
     * Config.
     *
     * @var ConfigInterface
     */
    private $_config;

    /**
     * Project constructor.
     */
    public function __construct(ConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * Gets the config instance.
     */
    public function config(): ConfigInterface
    {
        return $this->_config;
    }

    /**
     * Installs the project.
     */
    public function install(): void
    {
    }

    /**
     * Performs a database migration.
     */
    public function migrate(): void
    {
    }
}
