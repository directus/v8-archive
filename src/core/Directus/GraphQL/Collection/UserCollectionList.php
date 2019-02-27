<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Application\Application;
use Directus\Services\FilesServices;
use Directus\Services\ItemsService;
use Directus\Services\TablesService;
use Directus\Util\StringUtils;

class UserCollectionList {

    public $list;
    private $param;

    public function __construct(){

        $this->param = ['fields' => '*.*.*.*'];
        $container = Application::getInstance()->getContainer();
        //List all the collection
        $service = new TablesService($container);
        $collectionData = $service->findAll();

        $itemsService = new ItemsService($container);

        foreach($collectionData['data'] as  $value){
            if( $value['managed']){

                $type = Types::userCollection($value['collection']);

                //Add the individual collection item
                $this->list[$value['collection'].'Item'] = [
                    'type' => $type,
                    'description' => 'Return a single '.StringUtils::underscoreToSpace($value['collection']).' item.',
                    'args' => [
                        'id' => Types::nonNull(Types::id()),
                    ],
                    'resolve' => function($val, $args, $context, ResolveInfo $info)  use($value , $itemsService ) {
                        //We are supporting up to 3 level of nesting
                        $itemsService->throwErrorIfSystemTable($value['collection']);
                        return $itemsService->find(
                            $value['collection'],
                            $args['id'],
                            $this->param
                        )['data'];

                    }
                ];

                //Add the list of collection
                $this->list[$value['collection']] = [
                    'type' => Types::listOf($type),
                    'description' => 'Return list of '.StringUtils::underscoreToSpace($value['collection']).' items.',
                    'resolve' => function($val, $args, $context, ResolveInfo $info) use($value , $itemsService ) {

                        $itemsService->throwErrorIfSystemTable($value['collection']);
                        return $itemsService->findAll($value['collection'] ,$this->param)['data'];

                    }
                ];
            }
        }

    }

}