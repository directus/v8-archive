<?php

namespace Directus\Services;

use Directus\Application\Container;

class LayoutsService extends AbstractExtensionsController
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $basePath = $this->container->get('path_base');
        $extensions = $this->container->get('config')->get('extensions', []);

        $this->paths = [
            $basePath . '/public/extensions/core/layouts',
        ];

        foreach ($extensions as $extension) {
            $this->paths[] = "$basePath/public/extensions/$extension/layouts";
        }
    }

    public function findAll(array $params = [])
    {
        return $this->all($params);
    }
}
