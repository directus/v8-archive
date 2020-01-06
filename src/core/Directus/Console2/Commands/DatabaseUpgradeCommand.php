<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Directus\Util\Installation\InstallerUtils;

class DatabaseUpgradeCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'db:upgrade';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Upgrade the database schema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = $input->getOption('base-path');
        $project = $input->getOption('project');

        InstallerUtils::updateTables($basePath, $project);
        return 0;
    }
}
