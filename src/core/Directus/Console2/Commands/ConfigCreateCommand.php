<?php

namespace Directus\Console2\Commands;

use Directus\Util\Installation\InstallerUtils;
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

            ->addOption('db-type', null, InputOption::VALUE_REQUIRED, 'Type of the database server', 'mysql')
            ->addOption('db-host', null, InputOption::VALUE_REQUIRED, 'Host of the database server', 'localhost')
            ->addOption('db-port', null, InputOption::VALUE_REQUIRED, 'Port of the database server', '3306')
            ->addOption('db-name', null, InputOption::VALUE_REQUIRED, 'Name of the database', 'directus')
            ->addOption('db-user', null, InputOption::VALUE_REQUIRED, 'Username for the database connection', 'directus')
            ->addOption('db-pass', null, InputOption::VALUE_REQUIRED, 'Password for the database connection', 'directus')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing configuration file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = [
            'project' => $input->getArgument('project-key'),
            'db_type' => $inpupt->getOption('db-type'),
            'db_host' => $inpupt->getOption('db-host'),
            'db_port' => $inpupt->getOption('db-port'),
            'db_name' => $inpupt->getOption('db-name'),
            'db_user' => $inpupt->getOption('db-user'),
            'db_password' => $inpupt->getOption('db-pass'),
        ];

        InstallerUtils::createConfig(
            $input->getOption('base-path'),
            $data,
            $input->getOption('force')
        );

        return 0;
    }
}
