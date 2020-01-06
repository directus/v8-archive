<?php

namespace Directus\Console2;

use Symfony\Component\Console\Application;

/**
 * Directus CLI application
 */
class CLI extends Application
{
    public function __construct($binaryName, $version)
    {
        parent::__construct($binaryName, $version);

        $this->addCommands([
       ]);
    }
}
