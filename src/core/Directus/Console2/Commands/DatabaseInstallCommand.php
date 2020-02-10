<?php

namespace Directus\Console2\Commands;

use Directus\Console\Common\Setting;
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
            ->setDescription('Populate the database and create an administrator')
            ->addOption('project-name', null, InputOption::VALUE_REQUIRED, 'Project name')
            ->addOption('project-url', null, InputOption::VALUE_REQUIRED, 'Project url')
            ->addOption('email', 'e', InputOption::VALUE_REQUIRED, 'Administrator e-mail address', 'admin@example.com')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Administrator password', 'password')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = $input->getOption('base-path');
        $projectKey = $input->getArgument('project-key');
        $force = $input->getOption('force');

        $config = InstallerUtils::createApp($basePath, $projectKey)->getConfig();

        $data = [
            'project' => $projectKey,
            'db_name' => $config['database']['name'],
            'db_host' => $config['database']['host'],
            'db_port' => $config['database']['port'],
            'db_user' => $config['database']['username'],
            'db_password' => $config['database']['password'],
        ];

        InstallerUtils::ensureCanCreateTables($basePath, $data, $force);
        InstallerUtils::createTables($basePath, $projectKey, $force);
        InstallerUtils::addUpgradeMigrations($basePath, $projectKey);

        $data = [
            'app_url' => $input->getOption('project-url'),
            'project_name' => $input->getOption('project-name'),
            'user_email' => $input->getOption('email'),
            'user_password' => $input->getOption('password'),
        ];

        $setting = new Setting($basePath, $projectKey);

        InstallerUtils::addDefaultSettings($basePath, $data, $projectKey);
        InstallerUtils::addDefaultUser($basePath, $data, $projectKey);

        return 0;
    }
}
