<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

abstract class AbstractProjectCommand extends Command
{
    protected function configure()
    {
        $this
            ->addArgument('project-key', InputArgument::REQUIRED, 'Project key')
        ;
    }
}
