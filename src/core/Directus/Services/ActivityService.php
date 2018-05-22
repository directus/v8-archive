<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use Directus\Database\TableGateway\DirectusRolesTableGateway;
use Directus\Util\ArrayUtils;
use Directus\Util\DateTimeUtils;

class ActivityService extends AbstractService
{
    /**
     * @var BaseRowGateway
     */
    protected $lastGroup = null;

    /**
     * @var DirectusRolesTableGateway
     */
    protected $tableGateway = null;

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
        $this->collection = SchemaManager::COLLECTION_ACTIVITY;
        $this->itemsService = new ItemsService($this->container);
    }

    public function createComment(array $data, array $params = [])
    {
        $data = array_merge($data, [
            'type' => DirectusActivityTableGateway::TYPE_COMMENT,
            'action' => DirectusActivityTableGateway::ACTION_ADD,
            'datetime' => DateTimeUtils::nowInUTC()->toString(),
            'ip' => get_request_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'user' => $this->getAcl()->getUserId()
        ]);

        $this->validatePayload($this->collection, null, $data, $params);
        $this->enforcePermissions($this->collection, $data, $params);

        $tableGateway = $this->getTableGateway();

        // make sure to create new one instead of update
        unset($data[$tableGateway->primaryKeyFieldName]);
        $newComment = $tableGateway->updateRecord($data, $this->getCRUDParams($params));

        return $tableGateway->wrapData(
            $newComment->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    /**
     * Finds a group by the given ID in the database
     *
     * @param int $id
     * @param array $params
     *
     * @return array
     */
    public function find($id, array $params = [])
    {
        $tableGateway = $this->getTableGateway();
        $params['id'] = $id;

        return $this->getItemsAndSetResponseCacheTags($tableGateway, $params);
    }

    public function findAll(array $params = [])
    {
        $tableGateway = $this->getTableGateway();

        return $this->getItemsAndSetResponseCacheTags($tableGateway, $params);
    }

    /**
     * @return DirectusActivityTableGateway
     */
    public function getTableGateway()
    {
        if (!$this->tableGateway) {
            $acl = $this->container->get('acl');
            $dbConnection = $this->container->get('database');

            $this->tableGateway = new DirectusActivityTableGateway($dbConnection, $acl);
        }

        return $this->tableGateway;
    }
}
