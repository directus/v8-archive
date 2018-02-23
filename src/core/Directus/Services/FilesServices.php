<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Schema\SchemaManager;
use Directus\Exception\ErrorException;
use Directus\Util\ArrayUtils;
use Directus\Util\DateUtils;

class FilesServices extends AbstractService
{
    /**
     * @var string
     */
    protected $collection;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->collection = SchemaManager::TABLE_FILES;
    }

    public function create(array $data, array $params = [])
    {
        $this->enforcePermissions($this->collection, $data, $params);

        // $table = 'directus_files';
        $tableGateway = $this->createTableGateway($this->collection);

        $data['upload_user'] = $this->getAcl()->getUserId();
        $data['upload_date'] = DateUtils::now();

        $validationConstraints = $this->createConstraintFor($this->collection);
        $this->validate($data, array_merge(['data' => 'required'], $validationConstraints));
        $newFile = $tableGateway->updateRecord($data, $this->getCRUDParams($params));

        return $tableGateway->wrapData(
            $newFile->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function find($id, array $params = [])
    {
        $tableGateway = $this->createTableGateway($this->collection);
        $params['id'] = $id;

        return $this->getItemsAndSetResponseCacheTags($tableGateway , $params);
    }

    public function update($id, array $data, array $params = [])
    {
        $this->enforcePermissions($this->collection, $data, $params);

        $this->validatePayload($this->collection, array_keys($data), $data, $params);
        $tableGateway = $this->createTableGateway($this->collection);
        $data[$tableGateway->primaryKeyFieldName] = $id;
        $newFile = $tableGateway->updateRecord($data, $this->getCRUDParams($params));

        return $tableGateway->wrapData(
            $newFile->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function delete($id, array $params = [])
    {
        $this->enforcePermissions($this->collection, [], $params);
        $tableGateway = $this->createTableGateway($this->collection);
        $file = $tableGateway->loadItems(['id' => $id]);

        // Force delete files
        // TODO: Make the hook listen to deletes and catch ALL ids (from conditions)
        // and deletes every matched files
        /** @var \Directus\Filesystem\Files $files */
        $files = $this->container->get('files');
        $files->delete($file);

        // Delete file record
        $success = $tableGateway->delete([
            $tableGateway->primaryKeyFieldName => $id
        ]);

        if (!$success) {
            throw new ErrorException('Error deleting file record: ' . $id);
        }

        return $success;
    }

    public function findAll(array $params = [])
    {
        $tableGateway = $this->createTableGateway($this->collection);

        return $this->getItemsAndSetResponseCacheTags($tableGateway, $params);
    }

    public function createFolder(array $data, array $params = [])
    {
        $collection = 'directus_folders';
        $this->enforcePermissions($collection, $data, $params);
        $this->validatePayload($collection, null, $data, $params);

        $foldersTableGateway = $this->createTableGateway($collection);

        $newFolder = $foldersTableGateway->updateRecord($data);

        return $foldersTableGateway->wrapData(
            $newFolder->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function findFolder($id, array $params = [])
    {
        $foldersTableGateway = $this->createTableGateway('directus_folders');
        $params['id'] = $id;

        return $this->getItemsAndSetResponseCacheTags($foldersTableGateway, $params);
    }

    public function updateFolder($id, array $data, array $params = [])
    {
        $foldersTableGateway = $this->createTableGateway('directus_folders');

        $data['id'] = $id;
        $group = $foldersTableGateway->updateRecord($data);

        return $foldersTableGateway->wrapData(
            $group->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function findAllFolders(array $params = [])
    {
        $foldersTableGateway = $this->createTableGateway('directus_folders');

        return $this->getItemsAndSetResponseCacheTags($foldersTableGateway, $params);
    }

    public function deleteFolder($id, array $params = [])
    {
        $this->enforcePermissions('directus_folders', [], $params);

        $foldersTableGateway = $this->createTableGateway('directus_folders');
        // NOTE: check if item exists
        // TODO: As noted in other places make a light function to check for it
        $this->getItemsAndSetResponseCacheTags($foldersTableGateway, [
            'id' => $id
        ]);

        $success = $foldersTableGateway->delete(['id' => $id]);

        if (!$success) {
            throw new ErrorException('Error deleting the folder: ' . $id);
        }

        return $success;
    }
}
