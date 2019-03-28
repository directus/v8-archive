<?php
namespace Directus\GraphQL;

use Directus\GraphQL\Types;
use Directus\Application\Application;
use Directus\Services\TablesService;
use Directus\Services\RelationsService;

class FieldsConfig
{
    private $collection;
    private $container;
    private $service;
    private $collectionData;
    private $collectionFields;
    public function __construct($collection)
    {
        $this->container = Application::getInstance()->getContainer();
        $this->collection = $collection;
        $this->service = new TablesService($this->container);
        $this->collectionData = $this->service->findByIds(
            $this->collection
        );
        $this->collectionFields = $this->collectionData['data']['fields'];
    }

    public function getFields()
    {
        $fields = [];

        foreach ($this->collectionFields as $k => $v) {

            switch (strtolower($v['type'])) {
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
                    $relation = $this->getRelation('m2o', $v['collection'], $v['field']);
                    $fields[$k] = Types::userCollection($relation['collection_one']);
                    break;
                case 'o2m':
                    $relation = $this->getRelation('o2m', $v['collection'], $v['field']);
                    $temp = [];
                    $temp['type'] = Types::listOf(Types::userCollection($relation['collection_one']));
                    $temp['resolve'] = function ($value) use ($relation) {
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
                case 'time':
                    $fields[$k] = Types::time();
                    break;
                case 'user_created':
                case 'user_updated':
                    $fields[$k] = Types::directusUser();
                    break;
                default:
                    $fields[$k] = Types::string();
            }
            if ($v['required']) {
                $fields[$k] = Types::NonNull($fields[$k]);
            }
        }
        return $fields;
    }

    public function getFilters()
    {
        $filters = [];

        foreach ($this->collectionFields as $k => $v) {

            switch (strtolower($v['type'])) {
                case 'boolean':
                    $filters[$k . '_eq'] = Types::boolean();
                    break;
                case 'datetime':
                case 'datetime_created':
                case 'datetime_updated':
                    $filters[$k . '_eq'] = Types::datetime();
                    $filters[$k . '_neq'] = Types::datetime();
                    $filters[$k . '_lt'] = Types::datetime();
                    $filters[$k . '_lte'] = Types::datetime();
                    $filters[$k . '_gt'] = Types::datetime();
                    $filters[$k . '_gte'] = Types::datetime();
                    $filters[$k . '_in'] = Types::datetime();
                    $filters[$k . '_nin'] = Types::datetime();
                    $filters[$k . '_between'] = Types::string();
                    $filters[$k . '_nbetween'] = Types::string();
                    break;
                case 'integer':
                    $filters[$k . '_eq'] = Types::int();
                    $filters[$k . '_neq'] = Types::int();
                    $filters[$k . '_lt'] = Types::int();
                    $filters[$k . '_lte'] = Types::int();
                    $filters[$k . '_gt'] = Types::int();
                    $filters[$k . '_gte'] = Types::int();
                    $filters[$k . '_in'] = Types::int();
                    $filters[$k . '_nin'] = Types::int();
                    $filters[$k . '_between'] = Types::string();
                    $filters[$k . '_nbetween'] = Types::string();
                    break;
                case 'decimal':
                    $filters[$k . '_eq'] = Types::float();
                    $filters[$k . '_neq'] = Types::float();
                    $filters[$k . '_lt'] = Types::float();
                    $filters[$k . '_lte'] = Types::float();
                    $filters[$k . '_gt'] = Types::float();
                    $filters[$k . '_gte'] = Types::float();
                    $filters[$k . '_in'] = Types::float();
                    $filters[$k . '_nin'] = Types::float();
                    $filters[$k . '_between'] = Types::string();
                    $filters[$k . '_nbetween'] = Types::string();
                    break;
                case 'time':
                    $filters[$k . '_eq'] = Types::string();
                    $filters[$k . '_neq'] = Types::string();
                    $filters[$k . '_lt'] = Types::string();
                    $filters[$k . '_lte'] = Types::string();
                    $filters[$k . '_gt'] = Types::string();
                    $filters[$k . '_gte'] = Types::string();
                    $filters[$k . '_in'] = Types::string();
                    $filters[$k . '_nin'] = Types::string();
                    $filters[$k . '_between'] = Types::string();
                    $filters[$k . '_nbetween'] = Types::string();
                    break;
                case 'status':
                    $filters[$k . '_eq'] = Types::string();
                    $filters[$k . '_neq'] = Types::string();
                    $filters[$k . '_in'] = Types::string();
                    $filters[$k . '_nin'] = Types::string();
                    break;
                case 'string':
                    $filters[$k . '_contains'] = Types::string();
                    $filters[$k . '_ncontains'] = Types::string();
                    $filters[$k . '_rlike'] = Types::string();
                    $filters[$k . '_nrlike'] = Types::string();
                    $filters[$k . '_empty'] = Types::string();
                    $filters[$k . '_nempty'] = Types::string();
                    $filters[$k . '_null'] = Types::string();
                    $filters[$k . '_nnull'] = Types::string();
                    break;

                default:

                    // $filters[$k . '_all'] = Types::nonNull(Types::string());
                    // $filters[$k . '_has'] = Types::nonNull(Types::string());
            }
        }
        $filters['or'] = Types::listOf(Types::filters($this->collection));
        $filters['and'] = Types::listOf(Types::filters($this->collection));

        return $filters;
    }

    private function getRelation($type, $collection, $field)
    {
        //List all the relation
        $relationsService = new RelationsService($this->container);
        $relationsData = $relationsService->findAll();
        $relation = [];
        switch ($type) {
            case 'm2o':
                foreach ($relationsData['data'] as $k => $v) {
                    if ($v['collection_many'] == $collection && $v['field_many'] == $field) {
                        $relation = $v;
                        break;
                    }
                }
                break;
            case 'o2m':
                $firstRelation;

                //1. Find the collection_many
                foreach ($relationsData['data'] as $k => $v) {
                    if ($v['collection_one'] == $collection && $v['field_one'] == $field) {
                        $firstRelation = $v;
                        break;
                    }
                }
                $collectionMany = $firstRelation['collection_many'];
                $collection1Id = $firstRelation['id'];

                //2. Find the 2nd collection_one
                foreach ($relationsData['data'] as $k => $v) {
                    if ($v['collection_many'] == $collectionMany && $v['id'] != $collection1Id) {
                        $relation = $v;
                        break;
                    }
                }

                break;
        }

        return $relation;
    }
}
