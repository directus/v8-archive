<?php

namespace Directus\Config;

use Directus\Collection\Collection;
use Directus\Config\Exception\InvalidStatusException;
use Directus\Config\Exception\InvalidValueException;
use Directus\Util\ArrayUtils;

class StatusMapping extends Collection
{
    public function __construct(array $mapping = [])
    {
        $items = [];

        foreach ($mapping as $value => $status) {
            if (!is_integer($value)) {
                throw new InvalidValueException('status value must be a integer');
            }

            if (!is_array($status) || !ArrayUtils::contains($status, 'name')) {
                throw new InvalidStatusException('status must be an array with a name attribute');
            }

            $name = ArrayUtils::pull($status, 'name');
            $sort = ArrayUtils::pull($status, 'sort');

            $items[] = new StatusItem($value, $name, $sort, $status);
        }

        parent::__construct($items);
    }

    public function add(StatusItem $status)
    {
        $this->items[] = $status;
    }
}
