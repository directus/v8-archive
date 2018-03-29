<?php

namespace Directus\Config;

use Directus\Collection\Collection;
use Directus\Config\Exception\InvalidStatusException;
use Directus\Util\ArrayUtils;

class StatusMapping extends Collection
{
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
     * @param array $statusMapping
     *
     * @return array
     */
    public function getPublishedStatusesValue($statusMapping = [])
    {
        $visibleStatus = $this->getStatusesValue('published', true, $statusMapping);

        return $visibleStatus;
    }

    /**
     * Get all statuses value
     *
     * @param array $statusMapping
     *
     * @return array
     */
    public function getAllStatusesValue($statusMapping = [])
    {
        if (empty($statusMapping)) {
            $statusMapping = $this->items;
        }

        $statuses = [];

        /** @var StatusItem $status */
        foreach ($statusMapping as $status) {
            $statuses[] = $status->getValue();
        }

        return $statuses;
    }

    /**
     * Get statuses list by the given type
     *
     * @param string $type
     * @param mixed $value
     * @param array $statusMapping
     *
     * @return array
     */
    protected function getStatusesValue($type, $value, $statusMapping = [])
    {
        if (empty($statusMapping)) {
            $statusMapping = $this->items;
        }

        $statuses = [];

        /** @var StatusItem $status */
        foreach ($statusMapping as $status) {
            if ($status->getAttribute($type) === $value) {
                $statuses[] = $status->getValue();
            }
        }

        return $statuses;
    }
}
