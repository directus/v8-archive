<?php

namespace Directus\Console2\Commands;

use Directus\Util\MaintenanceUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MaintenanceOffCommand extends Command
{
    protected static $defaultName = 'maintenance:off';

    protected function configure()
    {
        $this
            ->setDescription('Disable maintenance mode')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        MaintenanceUtils::off($input->getOption('base-path'));
        $output->writeln('maintenance mode deactivated');

        return 0;
    }
}
