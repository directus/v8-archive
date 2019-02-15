<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Application\Application;
use Directus\Services\FilesServices;

class DirectusCollectionList {

    public $list;

    public function __construct(){

        $this->list = [
            'filesItem' => [
                'type' => Types::files(),
                'description' => 'Return single file item.',
                'args' => [
                    'id' => Types::nonNull(Types::id()),
                ],
                'resolve' => function($val, $args, $context, ResolveInfo $info) {
                    $container = Application::getInstance()->getContainer();
                    $service = new FilesServices($container);
                    return $service->findByIds(
                        $args['id']
                    )['data'];
                }
            ],
            'files' => [
                'type' => Types::listOf(Types::files()),
                'description' => 'Return list of files.',
                'resolve' => function($val, $args, $context, ResolveInfo $info) {

                    $container = Application::getInstance()->getContainer();
                    $service = new FilesServices($container);
                    return $service->findAll()['data'];

                }
            ],
            'fileThumbnail' => [
                'type' => Types::fileThumbnail(),
                'description' => 'Return single file item.',
            ]
        ];

    }
}
