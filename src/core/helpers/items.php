<?php

if (!function_exists('get_item_owner')) {
    /**
     * Gets the item's owner ID
     *
     * @param string $collection
     * @param mixed $id
     *
     * @return array
     */
    function get_item_owner($collection, $id)
    {
        $app = \Directus\Application\Application::getInstance();
        $dbConnection = $app->getContainer()->get('database');
        $tableGateway = new \Zend\Db\TableGateway\TableGateway($collection, $dbConnection);
        /** @var \Directus\Database\TableGateway\RelationalTableGateway $tableGateway */
        $usersTableGateway = \Directus\Database\TableGatewayFactory::create($collection, [
            'connection' => $dbConnection,
            'acl' => false
        ]);

        /** @var \Directus\Database\Schema\SchemaManager $schemaManager */
        $schemaManager = $app->getContainer()->get('schema_manager');

        $collectionObject = $schemaManager->getTableSchema($collection);
        $userCreatedField = $collectionObject->getUserCreateField();

        $owner = null;
        if ($userCreatedField) {
            $fieldName = $userCreatedField->getName();
            $select = new \Zend\Db\Sql\Select(
                ['c' => $tableGateway->table]
            );
            $select->limit(1);
            $select->columns([]);
            $select->where([
                'c.' . $collectionObject->getPrimaryKeyName() => $id
            ]);
            $select->join(
                ['u' => 'directus_users'],
                sprintf('c.%s = u.id', $fieldName),
                [
                    'id' => 'id',
                    'group' => 'group'
                ],
                $select::JOIN_LEFT
            );

            $owner = $tableGateway->selectWith($select)->toArray();
            $owner = $usersTableGateway->parseRecord(reset($owner), 'directus_users');
        }

        return $owner;
    }
}

if (!function_exists('get_user_ids_in_group')) {
    function get_user_ids_in_group($id)
    {
        $app = \Directus\Application\Application::getInstance();
        $dbConnection = $app->getContainer()->get('database');
        $tableGateway = new \Zend\Db\TableGateway\TableGateway('directus_users', $dbConnection);

        $select = new \Zend\Db\Sql\Select($tableGateway->table);
        $select->columns(['id']);
        $select->where(['group' => $id]);

        $result = $tableGateway->selectWith($select);

        $ids = [];
        foreach ($result as $row) {
            $ids[] = $row->id;
        }

        return $ids;
    }
}
