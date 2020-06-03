<?php


namespace Directus\Config\Schema;


use Exception;

trait CustomSchemaDefineTrait
{
    /**
     * Should return the path within the schema, where the config is expected
     *
     * @return string
     */
    abstract function getCustomConfigPath();

    /**
     * adds the given entity to the schema
     *
     * @param string $name
     * @param string $type
     * @param string | boolean | null | number $default
     * @throws Exception
     */
    protected function addToSchema($name, $type, $default) {
        if (!defined(Types::class . '::' . $type)) {
            throw new Exception('parameter $type must be part of ' . Types::class);
        }

        Schema::registerCustomNode($this->getCustomConfigPath(), new Value($name, constant('Types::' . $type), $default) );
    }
}
