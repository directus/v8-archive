<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $output->writeln('command not implemented');

        return 1;
    }
}
