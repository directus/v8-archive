<?php

namespace Directus\Console2\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function \Directus\create_app_with_project_name;

class CacheClearCommand extends AbstractProjectCommand
{
    protected static $defaultName = 'cache:clear';

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Clear all objects from cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = $input->getOption('base-path');
        $project = $input->getOption('project');

        $app = create_app_with_project_name($basePath, $project);
        $cache = $app->getContainer()->get('cache')->clear();

        $output->writeln("successfully cleared cache");
        return 0;
    }
}
