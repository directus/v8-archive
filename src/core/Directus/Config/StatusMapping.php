<?php

namespace Directus\Config;

use Directus\Collection\Collection;
use Directus\Config\Exception\InvalidStatusException;
use Directus\Util\ArrayUtils;

class StatusMapping extends Collection
{
    /**
     * @var StatusItem[]
     */
    protected $items;

    public function __construct(array $mapping = [])
    {
        $items = [];

        foreach ($mapping as $value => $status) {
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

    /**
     * Get a list of published statuses
     *
     * @return array
     */
    public function getPublishedStatusesValue()
    {
        $visibleStatus = $this->getStatusesValue('published', true);

        return $visibleStatus;
    }

    /**
     * Get all statuses value
     *
     * @return array
     */
    public function getAllStatusesValue()
    {
        $statuses = [];

        foreach ($this->items as $status) {
            $statuses[] = $status->getValue();
        }

        return $statuses;
    }

    /**
     * Get statuses list by the given type
     *
     * @param string $type
     * @param mixed $value
     *
     * @return array
     */
    protected function getStatusesValue($type, $value)
    {
        $statuses = [];
        foreach ($this->items as $status) {
            if ($status->getAttribute($type) === $value) {
                $statuses[] = $status->getValue();
            }
        }

        return $statuses;
    }
}
