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
        $this->collection = SchemaManager::COLLECTION_REVISIONS;
    }

    /**
     * Returns all items from revisions
     *
     * Result count will be limited by the rows per page setting
     *
     * @param array $params
     *
     * @return array|mixed
     */
    public function findAll(array $params = [])
    {
        $tableGateway = $this->getTableGateway();

        return $this->getDataAndSetResponseCacheTags(
            [$tableGateway, 'getItems'],
            [$params]
        );
    }

    /**
     * Returns all revisions for a specific collection
     *
     * Result count will be limited by the rows per page setting
     *
     * @param $collection
     * @param array $params
     *
     * @return array|mixed
     */
    public function findByCollection($collection, array $params = [])
    {
        $tableGateway = $this->getTableGateway();

        return $this->getDataAndSetResponseCacheTags(
            [$tableGateway, 'getItems'],
            [array_merge($params, ['filter' => ['collection' => $collection]])]
        );
    }

    /**
     * Returns one revision with the given id
     *
     * @param int $id
     * @param array $params
     *
     * @return array
     */
    public function findOne($id, array $params = [])
    {
        $tableGateway = $this->getTableGateway();

        return $this->getDataAndSetResponseCacheTags(
            [$tableGateway, 'getItems'],
            [array_merge($params, ['filter' => ['id' => $id]])]
        );
    }

    /**
     * Returns one revision with the given item id in the given collection
     *
     * @param string $collection
     * @param mixed $item
     * @param array $params
     *
     * @return array
     */
    public function findOneByItem($collection, $item, array $params = [])
    {
        $tableGateway = $this->getTableGateway();

        return $this->getDataAndSetResponseCacheTags(
            [$tableGateway, 'getItems'],
            [array_merge($params, ['filter' => ['item' => $item, 'collection' => $collection]])]
        );
    }

    /**
     * @return \Directus\Database\TableGateway\RelationalTableGateway
     */
    protected function getTableGateway()
    {
        return $this->createTableGateway($this->collection);
    }
}
