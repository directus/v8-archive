<?php

namespace Directus\Services;

use Directus\Application\Container;

class ListingsService extends AbstractExtensionsController
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $basePath = $this->container->get('path_base');
        $this->basePath = $basePath . '/public/core/listings';
    }

    public function findAll(array $params = [])
    {
        return $this->all($this->basePath, $params);
    }
}
