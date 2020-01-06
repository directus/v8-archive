<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Directus\Util\MaintenanceUtils;

class MaintenanceStatusCommand extends Command
{
    protected static $defaultName = 'maintenance:status';

    protected function configure()
    {
        $this
            ->setDescription('Get maintenance mode status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(
            'maintenance mode is %s',
            MaintenanceUtils::status($input->getOption('base-path'))
        ));
        return 0;
    }
}
