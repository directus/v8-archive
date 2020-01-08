<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Directus\Util\Installation\InstallerUtils;

class DatabaseInstallCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'db:install';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Populate the database')
            ->addOption('force', 'f', InputOption::VALUE_OPTIONAL, 'Force the process', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("command not implemented");
        return 1;
    }
}
