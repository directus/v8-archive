<?php
namespace Directus\GraphQL;

use Directus\GraphQL\Types;
use Directus\Application\Application;
use Directus\Services\TablesService;
use Directus\Services\RelationsService;
use Directus\GraphQL\Type\FieldsType;

class FieldsConfig
{
    private  $collection;
    private  $container;
    public function __construct($collection)
    {
        $this->container = Application::getInstance()->getContainer();
        $this->collection = $collection;
    }

    public function getFields(){
        $fields = [];


        $service = new TablesService($this->container);
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
                case 'm2o':
                    $relation = $this->getRelation($v['collection'] , $v['field']);
                    $collectionOne = $relation['collection_one'];
                    $fields[$k] = Types::userCollection($collectionOne);
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

    private function getRelation($collection , $field){
        //List all the relation
        $relationsService = new RelationsService($this->container);
        $relationsData = $relationsService->findAll();
        $relation = [];
        foreach($relationsData['data'] as $k => $v){
            if($v['collection_many'] == $collection && $v['field_many'] == $field){
                $relation = $v;
                break;
            }
        }
        return $relation;

    }
}
