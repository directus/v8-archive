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
    private $config;

    /**
     * Project constructor.
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Installs the project.
     */
    public function install(): void
    {
        $this->config->get('name');
    }

    /**
     * Performs a database migration.
     */
    public function migrate(): void
    {
    }
}
