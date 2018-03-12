<?php

namespace Directus\Permissions;

use Directus\Database\TableGateway\BaseTableGateway;
use Directus\Permissions\Exception\ForbiddenFieldReadException;
use Directus\Permissions\Exception\ForbiddenFieldWriteException;
use Directus\Util\ArrayUtils;
use Zend\Db\RowGateway\RowGateway;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;

class Acl
{
    const ACTION_CREATE = 'create';
    const ACTION_READ   = 'read';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    const LEVEL_NONE  = 0;
    const LEVEL_USER  = 1;
    const LEVEL_GROUP = 2;
    const LEVEL_FULL  = 3;

    const FIELD_READ_BLACKLIST = 'read_field_blacklist';
    const FIELD_WRITE_BLACKLIST = 'write_field_blacklist';

    const PERMISSION_FULL = [
        self::ACTION_CREATE => self::LEVEL_FULL,
        self::ACTION_READ   => self::LEVEL_FULL,
        self::ACTION_UPDATE => self::LEVEL_FULL,
        self::ACTION_DELETE => self::LEVEL_FULL
    ];

    const PERMISSION_NONE = [
        self::ACTION_CREATE => self::LEVEL_NONE,
        self::ACTION_READ   => self::LEVEL_NONE,
        self::ACTION_UPDATE => self::LEVEL_NONE,
        self::ACTION_DELETE => self::LEVEL_NONE
    ];

    const PERMISSION_READ = [
        self::ACTION_CREATE => self::LEVEL_NONE,
        self::ACTION_READ   => self::LEVEL_FULL,
        self::ACTION_UPDATE => self::LEVEL_NONE,
        self::ACTION_DELETE => self::LEVEL_NONE
    ];

    const PERMISSION_WRITE = [
        self::ACTION_CREATE => self::LEVEL_FULL,
        self::ACTION_READ   => self::LEVEL_NONE,
        self::ACTION_UPDATE => self::LEVEL_FULL,
        self::ACTION_DELETE => self::LEVEL_NONE
    ];

    const PERMISSION_READ_WRITE = [
        self::ACTION_CREATE => self::LEVEL_FULL,
        self::ACTION_READ   => self::LEVEL_FULL,
        self::ACTION_UPDATE => self::LEVEL_FULL,
        self::ACTION_DELETE => 0
    ];

    /**
     * Permissions by status grouped by collection
     *
     * @var array
     */
    protected $statusPermissions = [];

    /**
     * Permissions grouped by collection
     *
     * @var array
     */
    protected $globalPermissions = [];

    /**
     * Authenticated user id
     *
     * @var int|null
     */
    protected $userId = null;

    /**
     * Authenticated user group  id
     *
     * @var int|null
     */
    protected $groupId = null;

    /**
     * Flag to determine whether the user is public or not
     *
     * @var bool
     */
    protected $isPublic = null;

    public function __construct(array $permissions = [])
    {
        $this->setPermissions($permissions);
    }

    /**
     * Sets the authenticated user id
     *
     * @param $userId
     */
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
    }

    /**
     * Sets the authenticated group id
     *
     * @param $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = (int)$groupId;
    }

    /**
     * Sets whether the authenticated user is public
     *
     * @param $public
     */
    public function setPublic($public)
    {
        $this->isPublic = (bool)$public;
    }

    /**
     * Gets the authenticated user id
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Gets the authenticated group id
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Gets whether the authenticated user is public
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->isPublic === true;
    }

    /**
     * Gets whether the authenticated user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getGroupId() === 1;
    }

    /**
     * Sets the group permissions
     *
     * @param array $permissions
     *
     * @return $this
     */
    public function setPermissions(array $permissions)
    {
        foreach ($permissions as $collection => $collectionPermissions) {
            $this->setCollectionPermissions($collection, $collectionPermissions);
        }

        return $this;
    }

    /**
     * Sets permissions to the given collection
     *
     * @param string $collection
     * @param array $permissions
     */
    public function setCollectionPermissions($collection, array $permissions)
    {
        foreach ($permissions as $permission) {
            $this->setCollectionPermission($collection, $permission);
        }
    }

    /**
     * Sets a collection permission
     *
     * @param $collection
     * @param array $permission
     *
     * @return $this
     */
    public function setCollectionPermission($collection, array $permission)
    {
        $status = ArrayUtils::get($permission, 'status');

        if (is_null($status) && !isset($this->globalPermissions[$collection])) {
            $this->globalPermissions[$collection] = $permission;
        } else if (!is_null($status) && !isset($this->statusPermissions[$collection][$status])) {
            $this->statusPermissions[$collection][$status] = $permission;
            unset($this->globalPermissions[$collection]);
        }

        return $this;
    }

    /**
     * Gets the group permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        return array_merge($this->globalPermissions, $this->statusPermissions);
    }

    public function getCollectionStatuses($collection)
    {
        $statuses = null;
        $permissions = ArrayUtils::get($this->statusPermissions, $collection);
        if (!empty($permissions)) {
            $statuses = array_keys($permissions);
        }

        return $statuses;
    }

    /**
     * Gets a collection permissions
     *
     * @param string $collection
     *
     * @return array
     */
    public function getCollectionPermissions($collection)
    {
        if (array_key_exists($collection, $this->statusPermissions)) {
            return $this->statusPermissions[$collection];
        } else if (array_key_exists($collection, $this->globalPermissions)) {
            return $this->globalPermissions[$collection];
        }

        return [];
    }

    /**
     * Gets a collection permission
     *
     * @param string $collection
     * @param null|int|string $status
     *
     * @return array
     */
    public function getPermission($collection, $status = null)
    {
        $permissions = $this->getCollectionPermissions($collection);

        if (!is_null($status) && array_key_exists($collection, $this->statusPermissions)) {
            $permissions = ArrayUtils::get($permissions, $status, []);
        }

        return $permissions;
    }

    /**
     * Gets the given type (read/write) field blacklist
     *
     * @param string $type
     * @param string $collection
     * @param mixed $status
     *
     * @return array
     */
    public function getFieldBlacklist($type, $collection, $status = null)
    {
        $permission = $this->getPermission($collection, $status);

        switch ($type) {
            case static::FIELD_READ_BLACKLIST:
                $fields = ArrayUtils::get($permission, static::FIELD_READ_BLACKLIST);
                break;
            case static::FIELD_WRITE_BLACKLIST:
                $fields = ArrayUtils::get($permission, static::FIELD_WRITE_BLACKLIST);
                break;
            default:
                $fields = [];
        }

        return $fields ?: [];
    }

    /**
     * Gets the read field blacklist
     *
     * @param string $collection
     * @param mixed $status
     *
     * @return array
     */
    public function getReadFieldBlacklist($collection, $status = null)
    {
        return $this->getFieldBlacklist(static::FIELD_READ_BLACKLIST, $collection, $status);
    }

    /**
     * Gets the write field blacklist
     *
     * @param string $collection
     * @param mixed $status
     *
     * @return array|mixed
     */
    public function getWriteFieldBlacklist($collection, $status = null)
    {
        return $this->getFieldBlacklist(static::FIELD_WRITE_BLACKLIST, $collection, $status);
    }

    /**
     * Checks whether the user can add an item in the given collection
     *
     * @param string $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canCreate($collection, $status = null)
    {
        return $this->allowTo(static::ACTION_CREATE, static::LEVEL_USER, $collection, $status);
    }

    /**
     * Checks whether the user can view an item in the given collection
     *
     * @param int $level
     * @param $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canReadAt($level, $collection, $status = null)
    {
        return $this->allowTo(static::ACTION_READ, $level, $collection, $status);
    }

    /**
     * Checks whether the user can read at least their own items in the given collection
     *
     * @param $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canRead($collection, $status = null)
    {
        return $this->canReadMine($collection, $status);
    }

    /**
     * Checks whether the user can read at least in one permission level no matter the status
     *
     * @param string $collection
     *
     * @return bool
     */
    public function canReadOnce($collection)
    {
        return $this->allowToOnce(static::ACTION_READ, $collection);
    }

    /**
     * Checks whether the user can read their own items in the given collection
     *
     * @param $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canReadMine($collection, $status = null)
    {
        return $this->canReadAt(static::LEVEL_USER, $collection, $status);
    }

    /**
     * Checks whether the user can read same group users items in the given collection
     *
     * @param $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canReadFromGroup($collection, $status = null)
    {
        return $this->canReadAt(static::LEVEL_GROUP, $collection, $status);
    }

    /**
     * Checks whether the user can read same group users items in the given collection
     *
     * @param $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canReadAll($collection, $status = null)
    {
        return $this->canReadAt(static::LEVEL_FULL, $collection, $status);
    }

    /**
     * Checks whether the user can update an item in the given collection
     *
     * @param int $level
     * @param string $collection
     * @param mixed $status
     *
     * @return bool
     */
    public function canUpdateAt($level, $collection, $status = null)
    {
        return $this->allowTo(static::ACTION_UPDATE, $level, $collection, $status);
    }

    /**
     * Checks whether the user can update at least their own items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @return bool
     */
    public function canUpdate($collection, $status = null)
    {
        return $this->canUpdateMine($collection, $status);
    }

    /**
     * Checks whether the user can update their own items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @return bool
     */
    public function canUpdateMine($collection, $status = null)
    {
        return $this->canUpdateAt(static::LEVEL_USER, $collection, $status);
    }

    /**
     * Checks whether the user can update items from the same user groups in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @return bool
     */
    public function canUpdateFromGroup($collection, $status = null)
    {
        return $this->canUpdateAt(static::LEVEL_GROUP, $collection, $status);
    }

    /**
     * Checks whether the user can update all items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @return bool
     */
    public function canUpdateAll($collection, $status = null)
    {
        return $this->canUpdateAt(static::LEVEL_FULL, $collection, $status);
    }

    /**
     * Checks whether the user can delete an item in the given collection
     *
     * @param int $level
     * @param string $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canDeleteAt($level, $collection, $status = null)
    {
        return $this->allowTo(static::ACTION_DELETE, $level, $collection, $status);
    }

    /**
     * Checks whether the user can delete at least their own items in the given collection
     *
     * @param string $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canDelete($collection, $status = null)
    {
        return $this->canDeleteMine($collection, $status);
    }

    /**
     * Checks whether the user can delete its own items in the given collection
     *
     * @param string $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canDeleteMine($collection, $status = null)
    {
        return $this->canDeleteAt(static::LEVEL_USER, $collection, $status);
    }

    /**
     * Checks whether the user can delete items that belongs to a user in the same group in the given collection
     *
     * @param string $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canDeleteFromGroup($collection, $status = null)
    {
        return $this->canDeleteAt(static::LEVEL_GROUP, $collection, $status);
    }

    /**
     * Checks whether the user can delete any items in the given collection
     *
     * @param string $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function canDeleteAll($collection, $status = null)
    {
        return $this->canDeleteAt(static::LEVEL_FULL, $collection, $status);
    }

    /**
     * Checks whether the user can alter the given table
     *
     * @param $collection
     *
     * @return bool
     */
    public function canAlter($collection)
    {
        return $this->isAdmin();
    }

    /**
     * Checks whether a given collection requires activity message
     *
     * @param string $collection
     * @param string|int|null $status
     *
     * @return bool
     */
    public function requireActivityMessage($collection, $status = null)
    {
        $permission = $this->getPermission($collection, $status);
        if (!array_key_exists('require_activity_message', $permission)) {
            return false;
        }

        return $permission['require_activity_message'] === 1;
    }

    /**
     * Throws an exception if the user cannot read their own items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionReadException
     */
    public function enforceReadMine($collection, $status = null)
    {
        if (!$this->canReadMine($collection, $status)) {
            throw new Exception\ForbiddenCollectionReadException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot read the same group items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionReadException
     */
    public function enforceReadFromGroup($collection, $status = null)
    {
        if (!$this->canReadFromGroup($collection, $status)) {
            throw new Exception\ForbiddenCollectionReadException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot read all items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionReadException
     */
    public function enforceReadAll($collection, $status = null)
    {
        if (!$this->canReadAll($collection, $status)) {
            throw new Exception\ForbiddenCollectionReadException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot create a item in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionReadException
     */
    public function enforceRead($collection, $status = null)
    {
        $this->enforceReadMine($collection, $status);
    }

    /**
     * Throws an exception if the user cannot read a item in any level or status
     *
     * @param string $collection
     *
     * @throws Exception\ForbiddenCollectionReadException
     */
    public function enforceReadOnce($collection)
    {
        if (!$this->canReadOnce($collection)) {
            throw new Exception\ForbiddenCollectionReadException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot create a item in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionCreateException
     */
    public function enforceCreate($collection, $status = null)
    {
        if (!$this->canCreate($collection, $status)) {
            throw new Exception\ForbiddenCollectionCreateException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot alter the given collection
     *
     * @param $collection
     *
     * @throws Exception\ForbiddenCollectionAlterException
     */
    public function enforceAlter($collection)
    {
        if (!$this->canAlter($collection)) {
            throw new Exception\ForbiddenCollectionAlterException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot update their own items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionUpdateException
     */
    public function enforceUpdateMine($collection, $status = null)
    {
        if (!$this->canUpdateMine($collection, $status)) {
            throw new Exception\ForbiddenCollectionUpdateException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot update items from the same group in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionUpdateException
     */
    public function enforceUpdateFromGroup($collection, $status = null)
    {
        if (!$this->canUpdateFromGroup($collection, $status)) {
            throw new Exception\ForbiddenCollectionUpdateException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot update all items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionUpdateException
     */
    public function enforceUpdateAll($collection, $status = null)
    {
        if (!$this->canUpdateAll($collection, $status)) {
            throw new Exception\ForbiddenCollectionUpdateException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot update an item in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionUpdateException
     */
    public function enforceUpdate($collection, $status = null)
    {
        $this->enforceUpdateMine($collection, $status);
    }

    /**
     * Throws an exception if the user cannot delete their own items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionDeleteException
     */
    public function enforceDeleteMine($collection, $status = null)
    {
        if (!$this->canDeleteMine($collection, $status)) {
            throw new Exception\ForbiddenCollectionDeleteException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot delete items from the same group in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionDeleteException
     */
    public function enforceDeleteFromGroup($collection, $status = null)
    {
        if (!$this->canDeleteFromGroup($collection, $status)) {
            throw new Exception\ForbiddenCollectionDeleteException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot delete all items in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionDeleteException
     */
    public function enforceDeleteAll($collection, $status = null)
    {
        if (!$this->canDeleteAll($collection, $status)) {
            throw new Exception\ForbiddenCollectionDeleteException(
                $collection
            );
        }
    }

    /**
     * Throws an exception if the user cannot delete an item in the given collection
     *
     * @param string $collection
     * @param mixed $status
     *
     * @throws Exception\ForbiddenCollectionDeleteException
     */
    public function enforceDelete($collection, $status = null)
    {
        $this->enforceDeleteMine($collection, $status);
    }

    /**
     * Checks whether the user can see the given column
     *
     * @param string $collection
     * @param string $field
     * @param null|string|int $status
     *
     * @return bool
     */
    public function canReadField($collection, $field, $status = null)
    {
        $fields = $this->getReadFieldBlacklist($collection, $status);

        return !in_array($field, $fields);
    }

    /**
     * Checks whether the user can see the given column
     *
     * @param string $collection
     * @param string $field
     * @param null|int|string $status
     *
     * @return bool
     */
    public function canWriteField($collection, $field, $status = null)
    {
        $fields = $this->getWriteFieldBlacklist($collection, $status);

        return !in_array($field, $fields);
    }

    /**
     * Throws an exception if the user has not permission to read from the given field
     *
     * @param string $collection
     * @param string|array $fields
     * @param null|int|string $status
     *
     * @throws ForbiddenFieldReadException
     */
    public function enforceReadField($collection, $fields, $status = null)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as $field) {
            if (!$this->canReadField($collection, $field, $status)) {
                throw new ForbiddenFieldReadException($collection, $field);
            }
        }
    }

    /**
     * Throws an exception if the user has not permission to write to the given field
     *
     * @param string $collection
     * @param string|array $fields
     * @param null|int|string $status
     *
     * @throws ForbiddenFieldWriteException
     */
    public function enforceWriteField($collection, $fields, $status = null)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as $field) {
            if (!$this->canWriteField($collection, $field, $status)) {
                throw new ForbiddenFieldWriteException($collection, $field);
            }
        }
    }

    /**
     * Given table name $table and privilege constant $privilege, return boolean
     * value indicating whether the current user group has permission to perform
     * the specified table-level action on the specified table.
     *
     * @param string $action
     * @param string $collection
     * @param int $level
     * @param mixed $status
     *
     * @return boolean
     */
    public function allowTo($action, $level, $collection, $status = null)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permission = $this->getPermission($collection, $status);
        $permissionLevel = ArrayUtils::get($permission, $action, 0);

        return (int)$level <= $permissionLevel;
    }

    public function allowToOnce($action, $collection)
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permissions = [];
        if (array_key_exists($collection, $this->statusPermissions)) {
            $permissions = $this->statusPermissions[$collection];
        } else if (array_key_exists($collection, $this->globalPermissions)) {
            $permissions = [$this->globalPermissions[$collection]];
        }

        $allowed = false;
        foreach ($permissions as $permission) {
            $permissionLevel = ArrayUtils::get($permission, $action, 0);

            if ($permissionLevel > 0) {
                $allowed = true;
                break;
            }
        }

        return $allowed;
    }

    /**
     * Returns a list of status the given collection has permission to read
     *
     * @param string $collection
     *
     * @return array|mixed
     */
    public function getCollectionStatusesReadPermission($collection)
    {
        if ($this->isAdmin()) {
            return null;
        }

        $statuses = false;

        if (array_key_exists($collection, $this->statusPermissions)) {
            $statuses = [];

            foreach ($this->statusPermissions[$collection] as $status => $permission) {
                if (ArrayUtils::get($permission, static::ACTION_READ, 0) > 0) {
                    $statuses[] = $status;
                }
            }
        } else if (array_key_exists($collection, $this->globalPermissions)) {
            $permission = $this->globalPermissions[$collection];

            if (ArrayUtils::get($permission, static::ACTION_READ, 0) > 0) {
                $statuses = null;
            }
        }

        return $statuses;
    }
}
