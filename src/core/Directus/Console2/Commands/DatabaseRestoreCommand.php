<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Directus\Util\Installation\InstallerUtils;

class DatabaseRestoreCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'db:restore';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Restore the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("command not implemented");
        return 1;
    }
}
