<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseImportCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'db:import';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Import a database dump')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('command not implemented');

        return 1;
    }
}
