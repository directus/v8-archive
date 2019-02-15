<?php
namespace Directus\GraphQL;

use Directus\GraphQL\Types;
use Directus\Application\Application;
use Directus\Services\TablesService;

class FieldsConfig
{
    private  $collection;
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function getFields(){
        $fields = [];

        $container = Application::getInstance()->getContainer();
        $service = new TablesService($container);
        $collectionData = $service->findByIds(
            $this->collection
        );

        $collectionFields = $collectionData['data']['fields'];

        foreach($collectionFields as $k => $v){
            $fields[$k] = Types::string();
        }

        return $fields;
    }
}
