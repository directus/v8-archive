<?php

namespace Directus\Authentication\User\Provider;

use Directus\Authentication\User\User;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\TableGateway\BaseTableGateway;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Util\DateUtils;
use Zend\Db\Sql\Select;

class UserTableGatewayProvider implements UserProviderInterface
{
    /**
     * @var BaseTableGateway
     */
    protected $tableGateway;

    public function __construct(BaseTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritdoc
     */
    public function findWhere(array $conditions)
    {
        $user = null;

        $select = new Select($this->tableGateway->getTable());
        $select->where($conditions);
        $select->limit(1);

        /** @var BaseRowGateway $row */
        $row = $this->tableGateway
                    ->ignoreFilters()
                    ->selectWith($select)
                    ->current();

        if ($row) {
            $user = new User($row->toArray());
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->findWhere(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function findByEmail($email)
    {
        return $this->findWhere(['email' => $email]);
    }

    /**
     * @inheritdoc
     */
    public function update($user)
    {
        $condition = [
            $this->tableGateway->primaryKeyFieldName => $user->id
        ];

        $set = [
            'salt' => $user->salt,
            'access_token' => $user->access_token,
            'last_login' => DateUtils::now()
        ];

        return (bool)$this->tableGateway->ignoreFilters()->update($set, $condition);
    }
}
