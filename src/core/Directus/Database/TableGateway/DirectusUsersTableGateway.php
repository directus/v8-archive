<?php

namespace Directus\Database\TableGateway;

use Directus\Permissions\Acl;
use Zend\Db\Adapter\AdapterInterface;

class DirectusUsersTableGateway extends RelationalTableGateway
{
    const STATUS_HIDDEN = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;

    const GRAVATAR_SIZE = 100;

    public static $_tableName = 'directus_users';

    public $primaryKeyFieldName = 'id';

    public function __construct(AdapterInterface $adapter, Acl $acl = null)
    {
        parent::__construct(self::$_tableName, $adapter, $acl);
    }
}
