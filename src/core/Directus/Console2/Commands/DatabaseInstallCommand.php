<?php

namespace Directus\Console2\Commands;

use Directus\Util\Installation\InstallerUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseInstallCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'db:install';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Populate the database')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = $input->getOption('base-path');
        $project = $input->getArgument('project-key');
        $force = $input->getOption('force');

        $config = InstallerUtils::createApp($basePath, $project)->getConfig();

        $data = [
            'project' => $project,
            'db_name' => $config['database']['name'],
            'db_host' => $config['database']['host'],
            'db_port' => $config['database']['port'],
            'db_user' => $config['database']['username'],
            'db_password' => $config['database']['password'],
        ];

        InstallerUtils::ensureCanCreateTables($basePath, $data, $force);
        InstallerUtils::createTables($basePath, $project, $force);
        InstallerUtils::addUpgradeMigrations($basePath, $project);

        return 0;
    }
}
