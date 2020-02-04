<?php

declare(strict_types=1);

namespace Directus\Console;

use Directus\Console\Commands\Migrate;
use Directus\Core\Project;
use Directus\Core\Version;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Directus console application.
 */
class Application extends SymfonyApplication
{
    /**
     * Project instance.
     *
     * @var \Directus\Core\Project
     */
    private $project;

    /**
     * Configuration loader.
     *
     * @var \Directus\Core\Config\Providers\ProviderInterface
     */
    //private $config;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Directus Console', Version::getVersion() ?? '0.0.0');
        $this->addCommands([
            new Migrate(),
        ]);

        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(ConsoleEvents::COMMAND, [$this, 'onCommand']);

        $this->setDispatcher($dispatcher);
    }

    /**
     * Extends the application to provide global inputs.
     */
    public function onCommand(ConsoleCommandEvent $event): void
    {
        $output = $event->getOutput();
        $command = $event->getCommand();
        if (null !== $command) {
            $output->writeln('COMMAND EXECUTING: '.$command->getName());
        }
        //$this->config = ConfigFactory::create();
        $this->project = (string) ($event->getInput()->getOption('project'));
    }

    /**
     * Gets the project instance.
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * Extends the application to provide global inputs.
     */
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(
            new InputOption('project', null, InputOption::VALUE_REQUIRED, 'Project key')
        );

        return $definition;
    }
}
