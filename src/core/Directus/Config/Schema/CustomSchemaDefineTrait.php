<?php


namespace Directus\Config\Schema;


use Exception;

trait CustomSchemaDefineTrait
{
    abstract function getCustomConfigPath();

    /**
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
