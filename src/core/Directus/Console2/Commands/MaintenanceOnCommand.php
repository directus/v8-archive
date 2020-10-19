<?php

namespace Directus\Console2\Commands;

use Directus\Util\MaintenanceUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MaintenanceOnCommand extends Command
{
    protected static $defaultName = 'maintenance:on';

    protected function configure()
    {
        $this
            ->setDescription('Enable maintenance mode')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        MaintenanceUtils::on($input->getOption('base-path'));
        $output->writeln('maintenance mode activated');

        return 0;
    }
}
