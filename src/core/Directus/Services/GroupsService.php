<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\DirectusGroupsTableGateway;
use Directus\Exception\ErrorException;
use Directus\Exception\UnauthorizedException;
use Directus\Filesystem\Exception\ForbiddenException;
use Directus\Util\ArrayUtils;

class GroupsService extends AbstractService
{
    /**
     * @var BaseRowGateway
     */
    protected $lastGroup = null;

    /**
     * @var DirectusGroupsTableGateway
     */
    protected $tableGateway = null;

    /**
     * @var string
     */
    protected $collection;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->collection = SchemaManager::TABLE_GROUPS;
    }

    public function create(array $data, array $params = [])
    {
        $this->validatePayload($this->collection, null, $data, $params);
        $this->enforcePermissions($this->collection, $data, $params);

        $groupsTableGateway = $this->createTableGateway($this->collection);
        // make sure to create new one instead of update
        unset($data[$groupsTableGateway->primaryKeyFieldName]);
        $newGroup = $groupsTableGateway->updateRecord($data, $this->getCRUDParams($params));

        return $groupsTableGateway->wrapData(
            $newGroup->toArray(),
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

    public function update($id, array $data, array $params = [])
    {
        $this->validatePayload($this->collection, array_keys($data), $data, $params);
        $this->enforcePermissions($this->collection, $data, $params);

        $groupsTableGateway = $this->getTableGateway();

        $data['id'] = $id;
        $group = $groupsTableGateway->updateRecord($data, $this->getCRUDParams($params));

        return $groupsTableGateway->wrapData(
            $group->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function findAll(array $params = [])
    {
        $groupsTableGateway = $this->getTableGateway();

        return $this->getItemsAndSetResponseCacheTags($groupsTableGateway, $params);
    }

    public function delete($id, array $params = [])
    {
        $this->enforcePermissions($this->collection, [], $params);
        $this->validate(['id' => $id], $this->createConstraintFor($this->collection, ['id']));

        // TODO: Create exists method
        // NOTE: throw an exception if item does not exists
        $group = $this->find($id);

        // TODO: Make the error messages more specific
        if (!$this->canDelete($id)) {
            throw new UnauthorizedException(sprintf('You are not allowed to delete group [%s]', $id));
        }

        $tableGateway = $this->getTableGateway();

        $tableGateway->deleteRecord($id, $this->getCRUDParams($params));

        return true;
    }

    /**
     * Checks whether the the group be deleted
     *
     * @param $id
     * @param bool $fetchNew
     *
     * @return bool
     */
    public function canDelete($id, $fetchNew = false)
    {
        if (!$this->lastGroup || $fetchNew === true) {
            $group = $this->find($id);
        } else {
            $group = $this->lastGroup;
        }

        // TODO: RowGateWay should parse values against their column type
        return !(!$group || $group->id == 1 || strtolower($group->name) === 'public');
    }

    /**
     * @return DirectusGroupsTableGateway
     */
    public function getTableGateway()
    {
        if (!$this->tableGateway) {
            $acl = $this->container->get('acl');
            $dbConnection = $this->container->get('database');

            $this->tableGateway = new DirectusGroupsTableGateway($dbConnection, $acl);
        }

        return $this->tableGateway;
    }
}
