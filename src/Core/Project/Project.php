<?php

declare(strict_types=1);

namespace Directus\Core\Project;

use Directus\Core\Config\Config;

/**
 * Directus project.
 */
final class Project
{
    /**
     * The directus config.
     *
     * @var Config
     */
    private $config;

    /**
     * Creates a new project instance.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
}
