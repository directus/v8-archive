<?php

declare(strict_types=1);

namespace Directus\Console;

use Directus\Core\Project;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Install command.
 */
abstract class Command extends SymfonyCommand
{
    /**
     * Gets the project instance.
     *
     * @return \Directus\Core\Project
     */
    protected function getProject(): Project
    {
        /** @var \Directus\Console\Application */
        $application = $this->getApplication();

        return $application->getProject();
    }
}
