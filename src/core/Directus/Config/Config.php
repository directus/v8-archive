<?php

namespace Directus\Config;

use Directus\Collection\Collection;
use Directus\Config\Exception\InvalidStatusException;
use Directus\Config\Exception\InvalidValueException;
use Directus\Exception\Exception;
use Directus\Util\ArrayUtils;

class Config extends Collection implements ConfigInterface
{
    /**
     * @var StatusConfig
     */
    protected $status;

    public function __construct(array $items = [])
    {
        parent::__construct($items);

        $this->setUpStatus(ArrayUtils::get($items, 'status', []));
    }

    /**
     * @return StatusConfig
     */
    public function getStatus()
    {
        return $this->status;
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
     * Get a list of hard-deleted statuses
     *
     * @param array $statusMapping
     *
     * @return array
     */
    public function getDeletedStatusesValue($statusMapping = [])
    {
        $visibleStatus = $this->getStatusesValue('hard_delete', true, $statusMapping);

        // if (empty($visibleStatus) && defined('STATUS_DELETED_NUM')) {
        //     $visibleStatus[] = STATUS_DELETED_NUM;
        // }

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
            $statusMapping = $this->getGlobalStatusMapping();
        }

        $statuses = [];

        /** @var StatusItem $status */
        foreach ($statusMapping as $status) {
            $statuses[] = $status->getValue();
        }

        return $statuses;
    }

    /**
     * @return array
     */
    public function getStatusMapping()
    {
        return $this->getGlobalStatusMapping();
    }

    /**
     * The global status mapping
     *
     * @return array
     */
    protected function getGlobalStatusMapping()
    {
        return $this->status->getStatusMapping();
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
            $statusMapping = $this->getGlobalStatusMapping();
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

    /**
     * @param array $statusConfig
     */
    protected function setUpStatus(array $statusConfig)
    {
        $this->status = new StatusConfig($statusConfig);
    }
}
