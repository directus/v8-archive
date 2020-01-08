<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Directus\Util\Installation\InstallerUtils;

class UserDeleteCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'user:delete';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Delete an existing user')
            ->addOption('email', 'e', InputOption::VALUE_REQUIRED, 'User e-mail address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("command not implemented");
        return 1;
    }
}
