<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Services\FilesServices;
use Directus\Services\ItemsService;
use Directus\Services\TablesService;
use Directus\Util\StringUtils;
use Directus\GraphQL\Collection\CollectionList;

class UserCollectionList extends CollectionList {

    public $list;

    public function __construct(){
        parent::__construct();

        //List all the collection
        $service = new TablesService($this->container);
        $collectionData = $service->findAll();

        $itemsService = new ItemsService($this->container);

        foreach($collectionData['data'] as  $value){
            if( $value['managed']){

                $type = Types::userCollection($value['collection']);

                //Add the individual collection item
                $this->list[$value['collection'].'Item'] = [
                    'type' => $type,
                    'description' => 'Return a single '.StringUtils::underscoreToSpace($value['collection']).' item.',
                    'args' => ['id' => Types::nonNull(Types::id())],
                    'resolve' => function($val, $args, $context, ResolveInfo $info)  use($value , $itemsService ) {
                        $itemsService->throwErrorIfSystemTable($value['collection']);
                        $data =  $itemsService->find($value['collection'], $args['id'], $this->param)['data'];
                        return $data;

                    }
                ];

                //Add the list of collection
                $this->list[$value['collection']] = [
                    'type' => Types::listOf($type),
                    'description' => 'Return list of '.StringUtils::underscoreToSpace($value['collection']).' items.',
                    'args' => array_merge($this->limit , $this->offset),
                    'resolve' => function($val, $args, $context, ResolveInfo $info) use($value , $itemsService ) {
                        $this->param = (isset($args)) ? array_merge($this->param , $args) : $this->param;
                        $itemsService->throwErrorIfSystemTable($value['collection']);
                        return $itemsService->findAll($value['collection'], $this->param)['data'];

                    }
                ];
            }
        }

    }

}