<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Schema\SchemaManager;

class SettingsService extends AbstractService
{
    /**
     * @var string
     */
    protected $collection;

    /**
     * @var ItemsService
     */
    protected $itemsService;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->collection = SchemaManager::TABLE_SETTINGS;
        $this->itemsService = new ItemsService($this->container);
    }

    public function create(array $data, array $params = [])
    {
        return $this->itemsService->createItem($this->collection, $data, $params);
    }

    public function find($id, array $params = [])
    {
        return $this->itemsService->find($this->collection, $id, $params);
    }

    public function update($id, array $data, array $params = [])
    {
        return $this->itemsService->update($this->collection, $id, $data, $params);
    }

    public function delete($id, array $params = [])
    {
        return $this->itemsService->delete($this->collection, $id, $params);
    }

    public function findAll(array $params = [])
    {
        return $this->itemsService->findAll($this->collection, $params);
    }
}
