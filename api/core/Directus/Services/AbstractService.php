<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Database\TableGatewayFactory;
use Directus\Permissions\Acl;

abstract class AbstractService
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Gets application container
     *
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets application db connection instance
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    protected function getConnection()
    {
        return $this->getContainer()->get('database');
    }

    /**
     * Gets schema manager instance
     *
     * @return SchemaManager
     */
    public function getSchemaManager()
    {
        return $this->getContainer()->get('schema_manager');
    }

    /**
     * @param $name
     *
     * @return RelationalTableGateway
     */
    public function createTableGateway($name)
    {
        return TableGatewayFactory::create($name, [
            'acl' => $this->getAcl(),
            'connection' => $this->getConnection()
        ]);
    }

    /**
     * Gets Acl instance
     *
     * @return Acl
     */
    protected function getAcl()
    {
        return $this->getContainer()->get('acl');
    }
}
