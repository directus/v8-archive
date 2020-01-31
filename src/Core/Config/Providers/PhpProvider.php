<?php

declare(strict_types=1);

namespace Directus\Core\Config\Providers;

use Directus\Core\Config\Exception\FileNotFound;
use Directus\Core\Options\Options;

/**
 * PHP provider.
 */
class PhpProvider extends ArrayProvider
{
    /**
     * Path.
     */
    public const OPTION_PATH = 'path';

    /**
     * Options.
     *
     * @var \Directus\Core\Options\Options
     */
    private $options;

    /**
     * Initializes the provider.
     */
    public function __construct(array $options)
    {
        $this->options = new Options([static::OPTION_PATH], $options);

        $file = $this->options->get(static::OPTION_PATH);
        if (!file_exists($file)) {
            throw new FileNotFound($file);
        }

        parent::__construct([
            ArrayProvider::OPTION_DATA => require $file,
        ]);
    }
}
