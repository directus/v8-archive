<?php

declare(strict_types=1);

namespace Directus\Console;

use Directus\Core\Version;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * Directus console application.
 */
class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('Directus Installer', Version::getVersion() ?? '0.0.0');
    }
}
