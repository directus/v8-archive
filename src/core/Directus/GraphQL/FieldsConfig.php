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

            switch ($v['type']){
                case 'integer':
                    $fields[$k] = ($v['interface'] == 'primary-key') ? $fields[$k] = Types::id() : Types::int();
                break;
                case 'decimal':
                    $fields[$k] = Types::float();
                break;
                case 'sort':
                    $fields[$k] = Types::int();
                break;
                case 'boolean':
                    $fields[$k] = Types::boolean();
                break;
                case 'date':
                    $fields[$k] = Types::date();
                break;
                case 'datetime':
                case 'datetime_created':
                    $fields[$k] = Types::datetime();
                break;
                case 'json':
                    $fields[$k] = Types::json();
                break;
                default:
                    $fields[$k] = Types::string();
            }
            if($v['required']){
                $fields[$k] = Types::NonNull($fields[$k]);
            }
        }

        return $fields;
    }
}
