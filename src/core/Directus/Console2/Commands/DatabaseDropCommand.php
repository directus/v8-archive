<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDropCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'db:drop';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Drop the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('command not implemented');

        return 1;
    }
}
