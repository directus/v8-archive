<?php

declare(strict_types=1);

namespace Directus\Core;

use Directus\Core\Config\Config;
use Directus\Core\Options\Options;

/**
 * Directus.
 */
final class Directus
{
    /**
     * Options for directus.
     *
     * @var Options
     */
    private $options;

    /**
     * Instantiated projects.
     *
     * @var array
     */
    private $projects;

    /**
     * Current working project.
     *
     * @var null|Project
     */
    private $current;

    /**
     * Directus SDK constructor.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->current = null;
        $this->projects = [];
        $this->options = new Options([
            'config.provider',
            'config.options',
        ], $options);
    }

    /**
     * Undocumented function.
     */
    public function setCurrentProject(string $project): Project
    {
        return $this->current = $this->getProject($project);
    }

    /**
     * Get current project.
     */
    public function getCurrentProject(): ?Project
    {
        return $this->current;
    }

    /**
     * Gets a project by name.
     */
    public function getProject(string $project): Project
    {
        if (isset($this->projects[$project])) {
            return $this->projects[$project];
        }

        $config = Config::create(
            $project,
            $this->options->get('config.provider'),
            $this->options->get('config.options')
        );

        return $this->projects[$project] = new Project($config);
    }
}
