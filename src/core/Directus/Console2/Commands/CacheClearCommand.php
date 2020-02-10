<?php

namespace Directus\Console2\Commands;

use function Directus\create_app_with_project_name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $projectKey = $input->getArgument('project-key');

        $app = create_app_with_project_name($basePath, $projectKey);
        $cache = $app->getContainer()->get('cache')->clear();

        $output->writeln('successfully cleared cache');

        return 0;
    }
}
