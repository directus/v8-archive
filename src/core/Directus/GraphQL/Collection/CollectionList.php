<?php

namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use Directus\Application\Application;

class CollectionList
{

    protected $param;
    protected $limit;
    protected $offset;
    protected $container;

    public function __construct()
    {
        $this->param = ['fields' => '*.*.*.*.*.*', 'meta' => '*'];
        $this->limit = ['limit' => Types::int()];
        $this->offset = ['offset' => Types::int()];
        $this->container = Application::getInstance()->getContainer();
    }

    protected function parseArgs($args)
    {
        $this->param = (isset($args)) ? array_merge($this->param, $args) : $this->param;
        if (isset($this->param['filter'])) {
            $filter = [];
            foreach ($this->param['filter'] as $key => $value) {
                $filterParts = preg_split('~_(?=[^_]*$)~', $key);
                $filter[$filterParts[0]] = [$filterParts[1] => $value];
            }
            $this->param['filter'] = $filter;
        }
    }
}
