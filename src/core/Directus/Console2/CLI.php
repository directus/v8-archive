<?php

namespace Directus\Console2;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

use Directus\Console2\Commands\CacheClearCommand;
use Directus\Console2\Commands\DatabaseUpgradeCommand;
use Directus\Console2\Commands\MaintenanceOffCommand;
use Directus\Console2\Commands\MaintenanceOnCommand;
use Directus\Console2\Commands\MaintenanceStatusCommand;
use function \Directus\base_path;

/**
 * Directus CLI application
 */
class CLI extends Application
{
    public function __construct($binaryName, $version)
    {
        parent::__construct($binaryName, $version);

        $this->addCommands([
            new CacheClearCommand(),
            new DatabaseUpgradeCommand(),
            new MaintenanceOffCommand(),
            new MaintenanceOnCommand(),
            new MaintenanceStatusCommand(),
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
