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

            switch (strtolower($v['type'])){
                case 'array':
                    $fields[$k] = Types::listOf(Types::string());
                break;
                case 'boolean':
                    $fields[$k] = Types::boolean();
                break;
                case 'datetime':
                case 'datetime_created':
                case 'datetime_updated':
                    $fields[$k] = Types::datetime();
                break;
                case 'date':
                    $fields[$k] = Types::date();
                break;
                case 'file':
                    $fields[$k] = Types::directusFile();
                break;
                case 'integer':
                    $fields[$k] = ($v['interface'] == 'primary-key') ? $fields[$k] = Types::id() : Types::int();
                break;
                case 'decimal':
                    $fields[$k] = Types::float();
                break;
                case 'json':
                    $fields[$k] = Types::json();
                break;
                case 'm2o':
                    $relation = $this->getRelation('m2o', $v['collection'] , $v['field']);
                    $fields[$k] = Types::userCollection($relation['collection_one']);
                break;
                case 'o2m':
                    $relation = $this->getRelation('o2m' , $v['collection'] , $v['field']);
                    $temp = [];
                    $temp['type'] = Types::listOf(Types::userCollection($relation['collection_one']));
                    $temp['resolve'] = function($value) use($relation) {
                        $data = [];
                        foreach ($value[$relation['collection_one']] as  $v) {
                            $data[] = $v[$relation['field_many']];
                        }
                        return $data;
                    };
                    $fields[$k] = $temp;
                break;
                case 'sort':
                    $fields[$k] = Types::int();
                break;
                case 'status':
                    $fields[$k] = Types::string();
                break;
                case 'user_created':
                case 'user_updated':
                    $fields[$k] = Types::directusUser();
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

    private function getRelation($type , $collection , $field){
        //List all the relation
        $relationsService = new RelationsService($this->container);
        $relationsData = $relationsService->findAll();
        $relation = [];
        switch($type){
            case 'm2o':
                foreach($relationsData['data'] as $k => $v){
                    if($v['collection_many'] == $collection && $v['field_many'] == $field){
                        $relation = $v;
                        break;
                    }
                }
            break;
            case 'o2m':

                $firstRelation;

                //1. Find the collection_many
                foreach($relationsData['data'] as $k => $v){
                    if($v['collection_one'] == $collection && $v['field_one'] == $field){
                        $firstRelation = $v;
                        break;
                    }
                }
                $collectionMany = $firstRelation['collection_many'];
                $collection1Id = $firstRelation['id'];

                //2. Find the 2nd collection_one
                foreach($relationsData['data'] as $k => $v){
                    if($v['collection_many'] == $collectionMany && $v['id'] != $collection1Id){
                        $relation = $v;
                        break;
                    }
                }

            break;
        }

        return $relation;

    }
}
