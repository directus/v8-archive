<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractProjectCommand extends Command
{
    protected function configure()
    {
        $this
            ->addOption('project', 'k', InputOption::VALUE_REQUIRED, 'Project key')
        ;
    }
}
