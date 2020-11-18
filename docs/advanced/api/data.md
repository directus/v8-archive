# Data Access

Directus uses Zend DB to access data from the database. It has its own class `Directus\Database\TableGateway\RelationalTableGateway` extending `Zend\Db\TableGateway\TableGateway` to add some Directus logic.

You can use anything you want to access the data from the database, you can use PDO or even `Zend\Db\TableGateway\TableGateway`. The advantage of using the `Directus\Database\TableGateway\RelationalTableGateway` you will have the same api core data logic, such as fetching relational data and filters.

## Fetching Data within Hooks and Endpoints

### Using Directus TableGateway Factory

```php
// Using the factory
$container = \Directus\Application\Application::getInstance()->getContainer();
$dbConnection = $container->get('database');
$acl = $container->get('acl');

$tableGateway = Directus\Database\TableGatewayFactory::create('COLLECTION_NAME', [
    // An Zend\Db\Adapter\Adapter instance
    'connection' => $dbConnection,
    // options are:
    //  false = no acl validation
    //  null = uses $container->get('acl')
    //  an Directus\Permissions\Acl instance
    'acl' => $acl
]);
```

`Directus\Database\TableGatewayFactory::create` creates a `Directus\Database\TableGateway\RelationalTableGateway` unless there is a `CollectionNameTableGateway` class in `Directus\Database\TableGatway` namespace.


```php
$params = [];
$items = $tableGateway->getItems($params);

// Returns an array of items
// Example:
// [
//    'data' => [
//        'id' => 1,
//        'title' => 'Project title'
//    ]
// ];
```

For `params` it can be used any [Query Parameters](https://github.com/directus/docs/blob/master/api/reference.md#query-parameters) supported by the API.

### Zend DB TableGateway

If you want to create your own custom queries using the Zend DB TableGateway gives you more flexibility, you can either use the TableGateway created by Directus TableGateway factory above, or create a instance of `Zend\Db\TableGateway\TableGateway`.


```php
$container = \Directus\Application\Application::getInstance()->getContainer();
$dbConnection = $container->get('database');
$tableGateway = new \Zend\Db\TableGateway\TableGateway('directus_users', $dbConnection);
$select = new \Zend\Db\Sql\Select('directus_users');
$select->limit(5);
$result = $tableGateway->selectWith($select);

while ($result->valid()) {
    $item = $result->current();
    $result->next();
}
```

If you want to update a just created item, you can do it as following.

This is especially useful in `:after` `action` hooks where you normally don't have direct access to the databases' payload anymore, but still want to modify a table.
In this case, we change the model name according to its ID, which wouldn't be possible in the `:before` hook, since we wouldn't know its ID.

```php
$container = \Directus\Application\Application::getInstance()->getContainer();
$dbConnection = $container->get('database');
$tableGateway = new \Zend\Db\TableGateway\TableGateway('products', $dbConnection);

$update_data = array(
    'id' => $data['id'],
    'model'  => "Model-" . $data['id'],
);
$where = array('id' => $data['id']);
$tableGateway->update($update_data, $where);
```

You can read the [Zend DB 2 Documentation](https://framework.zend.com/manual/2.2/en/modules/zend.db.sql.html) to know more about how to use Zend DB to select data.

All the Zend DB TableGateway methods are available to use such as [creating joins](https://framework.zend.com/manual/2.2/en/modules/zend.db.sql.html#join).

```php
$container = \Directus\Application\Application::getInstance()->getContainer();
$dbConnection = $container->get('database');

$tableGateway = new \Zend\Db\TableGateway\TableGateway('directus_users', $dbConnection);

$select = new \Zend\Db\Sql\Select('directus_users');
$select->limit(5);
$select->join(
    'directus_files', // table name
    'directus_files.id = directus_users.id', // expression to join on (will be quoted by platform object before insertion),
    array('filename'), // (optional) list of columns, same requirements as columns() above
    $select::JOIN_LEFT // (optional), one of inner, outer, left, right also represented by constants in the API
);
$result = $tableGateway->selectWith($select);

while ($result->valid()) {
    $item = $result->current();
    $result->next();
}
```

### Items Service

This is the same class that the API uses behind the HTTP Layer.

```php
$container = \Directus\Application\Application::getInstance()->getContainer();
$itemsService = new \Directus\Services\ItemsService($container);

$params = [];
$items = $itemsService->findAll('directus_users', $params);
$item = $itemsService->find('directus_users', 1, $params);
```

### Access Control

Directus ACL helps you verify user privilege to access a collection.

Some of the ACL Methods are:

```
Acl::canCreate($collection, $status = null);
Acl::canRead($collection, $status = null);
Acl::canUpdate($collection, $status = null);
Acl::canDelete($collection, $status = null);
```

You can see all the methods in the [Directus\Permissions\Acl](https://github.com/directus/api/blob/master/src/core/Directus/Permissions/Acl.php) class.

Example:

```
$container = \Directus\Application\Application::getInstance()->getContainer();
$acl = $container->get('acl');

if ($acl->canRead('directus_users')) {
    // read directus_users items
}
```
