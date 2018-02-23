<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Schema\SchemaManager;

class RevisionsService extends AbstractService
{
    /**
     * @var string
     */
    protected $collection;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->collection = SchemaManager::TABLE_REVISIONS;
    }

    public function findItemAll($collection, $id, array $params = [])
    {
        $tableGateway = $this->getTableGateway();

        return $this->getDataAndSetResponseCacheTags(
            [$tableGateway, 'fetchRevisions'],
            [$id, $collection]
        );
    }

    protected function getTableGateway()
    {
        return $this->createTableGateway($this->collection);
    }
}
