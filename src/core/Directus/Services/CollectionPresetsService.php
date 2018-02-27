<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Schema\SchemaManager;
use Directus\Util\ArrayUtils;

class CollectionPresetsService extends AbstractService
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

        $this->collection = SchemaManager::TABLE_COLLECTION_PRESETS;
        $this->itemsService = new ItemsService($this->container);
    }

    public function findAll(array $params = [])
    {
        return $this->itemsService->findAll($this->collection, $params);
    }

    public function createItem(array $payload, array $params = [])
    {
        return $this->itemsService->createItem($this->collection, $payload, $params);
    }

    public function find($id, array $params = [])
    {
        return $this->itemsService->find($this->collection, $id, $params);
    }

    public function update($id, array $payload, array $params = [])
    {
        return $this->itemsService->update($this->collection, $id, $payload, $params);
    }

    public function delete($id, array $params = [])
    {
        return $this->itemsService->delete($this->collection, $id, $params);
    }
}
