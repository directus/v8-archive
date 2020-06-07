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
     * returns the array to be used to insert into the config schema
     *
     * Array needs to return an array containing the keys name, type and default.
     * name as string
     * type as instance of Type
     * default as the default value
     *
     * @return array
     */
    abstract function readCustomSchema();

    /**
     * adds the given entity to the schema
     *
     * @throws Exception
     */
    public function addToSchema()
    {
        foreach ($this->readCustomSchema() as $schemaEntity) {
            if (!array_key_exists('name', $schemaEntity) ||
                !array_key_exists('type', $schemaEntity) ||
                !array_key_exists('default', $schemaEntity)) {
                continue;
            }
            $name = $schemaEntity['name'];
            $type = $schemaEntity['type'];
            $default = $schemaEntity['default'];
            if (!defined(Types::class . '::' . $type)) {
                throw new Exception('parameter $type must be part of ' . Types::class);
            }

            Schema::registerCustomNode($this->getCustomConfigPath(), new Value($name, constant('Types::' . $type), $default));
        }
    }
}
