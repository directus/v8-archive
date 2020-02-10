<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseWipeCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'db:wipe';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Wipe the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('command not implemented');

        return 1;
    }
}
