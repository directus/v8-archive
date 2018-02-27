<?php

namespace Directus\Services;

use Directus\Application\Container;

class ListViewsService extends AbstractAddOnsController
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $basePath = $this->container->get('path_base');
        $this->basePath = $basePath . '/public/core/list_views';
    }

    public function findAll(array $params = [])
    {
        return $this->all($this->basePath, $params);
    }
}
