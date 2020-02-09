<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigCreateCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'config:create';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Create a new configuration file')

            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'Type of the database server', 'mysql')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host of the database server', 'localhost')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port of the database server', '3306')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'Name of the database', 'directus')
            ->addOption('user', null, InputOption::VALUE_OPTIONAL, 'Username for the database connection', 'directus')
            ->addOption('pass', null, InputOption::VALUE_OPTIONAL, 'Password for the database connection', 'directus')
            ->addOption('cors', null, InputOption::VALUE_OPTIONAL, 'Enable CORS', false)
            ->addOption('url', null, InputOption::VALUE_OPTIONAL, 'Directus base URI', '/')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("command not implemented");
        return 1;
    }
}
