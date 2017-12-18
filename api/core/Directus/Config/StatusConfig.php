<?php

namespace Directus\Config;

use Directus\Collection\Collection;
use Directus\Config\Exception\InvalidStatusException;
use Directus\Config\Exception\InvalidValueException;
use Directus\Util\ArrayUtils;

class StatusConfig extends Collection
{
    /**
     * @var StatusMapping
     */
    protected $mapping;

    public function __construct(array $config = [])
    {
        $required = ['mapping', 'deleted_value', 'publish_value', 'draft_value', 'name'];
        if (!empty($config) && !ArrayUtils::contains($config, $required)) {
            throw new InvalidStatusException('invalid status configuration');
        }

        // 'deleted_value' => 0,
        // 'publish_value' => 1,
        // 'draft_value' => 2,
        // 'name' => 'status',

        $this->createStatusMapping(ArrayUtils::get($config, 'mapping', []));

        parent::__construct($config);
    }

    public function getStatusMapping()
    {
        return $this->mapping->toArray();
    }

    protected function createStatusMapping(array $mapping)
    {
        $this->mapping = new StatusMapping($mapping);
    }
}
