<?php

declare(strict_types=1);

namespace Directus\Core\Config;

/**
 * Directus config.
 */
interface LoaderInterface
{
    /**
     * Initializes the laoder.
     *
     * @param array $options
     *
     * @return bool
     */
    public function initialize(array $options): bool;

    /**
     * Loads a project configuration.
     *
     * @param string $project
     *
     * @return array
     */
    public function load(string $project): array;

    /**
     * Updates a project configuration.
     *
     * @param string $project
     * @param array  $options
     *
     * @return bool
     */
    public function save(string $project, array $options): bool;
}
