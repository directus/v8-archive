<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Application\Application;
use Directus\Services\FilesServices;
use Directus\Services\ItemsService;
use Directus\Services\TablesService;

class UserCollectionList {

    public $list;

    public function __construct(){

        $container = Application::getInstance()->getContainer();

        //List all the collection
        $service = new TablesService($container);
        $collectionData = $service->findAll();

        $itemsService = new ItemsService($container);

        foreach($collectionData['data'] as  $value){
            if( ! $value['single'] && $value['managed']){

                $type = Types::userCollection($value['collection']);

                //Add the individual collection item
                $this->list[$value['collection'].'Item'] = [
                    'type' => $type,
                    'description' => 'Return a single '.$value['collection'].' item.',
                    'args' => [
                        'id' => Types::nonNull(Types::id()),
                    ],
                    'resolve' => function($val, $args, $context, ResolveInfo $info)  use($value , $itemsService) {

                        $itemsService->throwErrorIfSystemTable($value['collection']);
                        return $itemsService->findByIds(
                            $value['collection'],
                            $args['id']
                        )['data'];

                    }
                ];

                //Add the list of collection
                $this->list[$value['collection']] = [
                    'type' => Types::listOf($type),
                    'description' => 'Return list of '.$value['collection'].' items.',
                    'resolve' => function($val, $args, $context, ResolveInfo $info) use($value , $itemsService) {

                        $itemsService->throwErrorIfSystemTable($value['collection']);
                        return $itemsService->findAll($value['collection'])['data'];

                    }
                ];
            }
        }

    }

}