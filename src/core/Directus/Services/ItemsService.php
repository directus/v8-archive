<?php

namespace Directus\Services;

use Directus\Exception\BadRequestException;
use Directus\Exception\ErrorException;
use Directus\Util\ArrayUtils;

class ItemsService extends AbstractService
{
    public function createItem($collection, $payload, $params = [])
    {
        $this->enforcePermissions($collection, $payload, $params);
        $this->validatePayload($collection, null, $payload, $params);

        $tableGateway = $this->createTableGateway($collection);

        // TODO: Throw an exception if ID exist in payload
        $newRecord = $tableGateway->updateRecord($payload, $this->getCRUDParams($params));

        $params['id'] = $newRecord->getId();
        $params['status'] = null;

        return $this->getItemsAndSetResponseCacheTags($tableGateway, $params);
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
        $tableGateway = $this->createTableGateway($collection);

        $params['id'] = $id;

        return $this->getItemsAndSetResponseCacheTags($tableGateway, $params);
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
        $tableGateway->updateRecord($payload, $this->getCRUDParams($params));

        // TODO: Do not fetch if this is after insert
        // and the user doesn't have permission to read
        return $this->find($collection, $id, $params);
    }

    public function delete($collection, $id, array $params = [])
    {
        $this->enforcePermissions($collection, [], $params);

        $item = $this->find($collection, $id);

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
}
