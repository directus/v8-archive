<?php

declare(strict_types=1);

namespace Directus\Console\Commands;

use Directus\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Install command.
 */
class Migrate extends Command
{
    protected static $defaultName = 'migrate';

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setDescription('Migrates a project database to the current version.')
            ->setHelp('This runs migrations in the target project database.');
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getProject();

        return 0;
    }
}
