<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreateCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'user:create';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Create a new user')
            ->addOption('email', 'e', InputOption::VALUE_REQUIRED, 'User e-mail address')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('command not implemented');

        return 1;
    }
}
