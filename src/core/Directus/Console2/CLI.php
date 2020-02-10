<?php

namespace Directus\Console2;

use function Directus\base_path;
use Directus\Console2\Commands\CacheClearCommand;
use Directus\Console2\Commands\ConfigCreateCommand;
use Directus\Console2\Commands\DatabaseDumpCommand;
use Directus\Console2\Commands\DatabaseInstallCommand;
use Directus\Console2\Commands\DatabaseRestoreCommand;
use Directus\Console2\Commands\DatabaseUpgradeCommand;
use Directus\Console2\Commands\DatabaseWipeCommand;
use Directus\Console2\Commands\MaintenanceOffCommand;
use Directus\Console2\Commands\MaintenanceOnCommand;
use Directus\Console2\Commands\MaintenanceStatusCommand;
use Directus\Console2\Commands\UserChangePasswordCommand;
use Directus\Console2\Commands\UserCreateCommand;
use Directus\Console2\Commands\UserDeleteCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

/**
 * Directus CLI application.
 */
class CLI extends Application
{
    public function __construct($binaryName, $version)
    {
        parent::__construct($binaryName, $version);

        $this->addCommands([
            new CacheClearCommand(),
            new ConfigCreateCommand(),
            new DatabaseDumpCommand(),
            new DatabaseInstallCommand(),
            new DatabaseRestoreCommand(),
            new DatabaseUpgradeCommand(),
            new DatabaseWipeCommand(),
            new MaintenanceOffCommand(),
            new MaintenanceOnCommand(),
            new MaintenanceStatusCommand(),
            new UserChangePasswordCommand(),
            new UserCreateCommand(),
            new UserDeleteCommand(),
        ]);
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        $definition->addOption(new InputOption(
            'base-path',
            null,
            InputOption::VALUE_OPTIONAL,
            'Path to directus base directory',
            base_path()
        ));

        return $definition;
    }
}
