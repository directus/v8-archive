<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Application\Application;
use Directus\Services\FilesServices;
use Directus\Services\UsersService;

class DirectusCollectionList {

    public $list;

    public function __construct(){
        $container = Application::getInstance()->getContainer();
        $this->list = [
            'filesItem' => [
                'type' => Types::files(),
                'description' => 'Return single file.',
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
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {
                    $service = new FilesServices($container);
                    return $service->findAll()['data'];
                }
            ],
            'fileThumbnail' => [
                'type' => Types::fileThumbnail(),
                'description' => 'Return single file thumbnail.',
            ],
            'usersItem' => [
                'type' => Types::users(),
                'description' => 'Return single user.',
                'args' => [
                    'id' => Types::nonNull(Types::id()),
                ],
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {
                    $service = new UsersService($container);
                    return $service->findByIds(
                        $args['id']
                    )['data'];

                }
            ],
            'users' => [
                'type' => Types::listOf(Types::users()),
                'description' => 'Return list of users.',
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {
                    $service = new UsersService($container);
                    return $service->findAll()['data'];
                }
            ],
        ];

    }
}
