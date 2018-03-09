<?php

namespace Directus\Services;

use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Exception\BadRequestException;
use Directus\Exception\ErrorException;
use Directus\Util\ArrayUtils;
use Zend\Db\TableGateway\TableGateway;

class ItemsService extends AbstractService
{
    public function createItem($collection, $payload, $params = [])
    {
        $this->enforcePermissions($collection, $payload, $params);
        $this->validatePayload($collection, null, $payload, $params);

        $tableGateway = $this->createTableGateway($collection);

        // TODO: Throw an exception if ID exist in payload
        $newRecord = $tableGateway->updateRecord($payload, $this->getCRUDParams($params));

        try {
            $item = $this->find($collection, $newRecord->getId());
        } catch (\Exception $e) {
            $item = null;
        }

        return $item;
    }

    /**
     * Finds all items in a collection limited by a limit configuration
     *
     * @param $collection
     * @param array $params
     *
     * @return array
     */
    public function findAll($collection, array $params = [])
    {
        // TODO: Use repository instead of TableGateway
        return $this->getItemsAndSetResponseCacheTags(
            $this->createTableGateway($collection),
            $params
        );
    }

    /**
     * Gets a single item in the given collection and id
     *
     * @param string $collection
     * @param mixed $id
     * @param array $params
     *
     * @return array
     */
    public function find($collection, $id, array $params = [])
    {
        $statusValue = $this->getStatusValue($collection, $id);
        $tableGateway = $this->createTableGateway($collection);

        $this->getAcl()->enforceRead($collection, $statusValue);

        return $this->getItemsAndSetResponseCacheTags($tableGateway, array_merge($params, [
            'id' => $id,
            'status' => null
        ]));
    }

    /**
     * Updates a single item in the given collection and id
     *
     * @param string $collection
     * @param mixed $id
     * @param array $payload
     * @param array $params
     *
     * @return array
     */
    public function update($collection, $id, $payload, array $params = [])
    {
        $this->enforcePermissions($collection, $payload, $params);
        $this->validatePayload($collection, array_keys($payload), $payload, $params);

        $tableGateway = $this->createTableGateway($collection);

        // Fetch the entry even if it's not "published"
        $params['status'] = '*';
        $payload[$tableGateway->primaryKeyFieldName] = $id;
        $newRecord = $tableGateway->updateRecord($payload, $this->getCRUDParams($params));

        try {
            $item = $this->find($collection, $newRecord->getId());
        } catch (\Exception $e) {
            $item = null;
        }

        return $item;
    }

    public function delete($collection, $id, array $params = [])
    {
        $this->enforcePermissions($collection, [], $params);

        // TODO: Better way to check if the item exists
        // $item = $this->find($collection, $id);

        $tableGateway = $this->createTableGateway($collection);

        $condition = [
            $tableGateway->primaryKeyFieldName => $id
        ];

        if (ArrayUtils::get($params, 'soft') == 1) {
            if (!$tableGateway->getTableSchema()->hasStatusField()) {
                throw new BadRequestException('Cannot soft-delete because the collection is missing status interface field');
            }

            $success = $tableGateway->update([
                $tableGateway->getStatusColumnName() => $tableGateway->getDeletedValue()
            ], $condition);

            if (!$success) {
                throw new ErrorException('Error soft-deleting item id: ' . $id);
            }
        } else {
            $tableGateway->deleteRecord($id, $this->getCRUDParams($params));
        }

        return true;
    }

    protected function getItem(BaseRowGateway $row)
    {
        $collection = $row->getCollection();
        $item = null;
        $statusValue = $this->getStatusValue($collection, $row->getId());
        $tableGateway = $this->createTableGateway($collection);

        if ($this->getAcl()->canRead($collection, $statusValue)) {
            $params['id'] = $row->getId();
            $params['status'] = null;
            $item = $this->getItemsAndSetResponseCacheTags($tableGateway, $params);
        }

        return $item;
    }

    protected function getStatusValue($collection, $id)
    {
        $collectionObject = $this->getSchemaManager()->getTableSchema($collection);

        if (!$collectionObject->hasStatusField()) {
            return null;
        }

        $primaryFieldName = $collectionObject->getPrimaryKeyName();
        $tableGateway = new TableGateway($collection, $this->getConnection());
        $select = $tableGateway->getSql()->select();
        $select->columns([$collectionObject->getStatusField()->getName()]);
        $select->where([
            $primaryFieldName => $id
        ]);

        $row = $tableGateway->selectWith($select)->current();

        return $row[$collectionObject->getStatusField()->getName()];
    }
}
