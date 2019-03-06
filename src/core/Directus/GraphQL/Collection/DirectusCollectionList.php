<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Application\Application;
use Directus\Services\FilesServices;
use Directus\Services\UsersService;
use Directus\Services\RolesService;

class DirectusCollectionList {

    public $list;
    private $param;

    public function __construct(){

        $this->param = ['fields' => '*.*.*.*.*.*'];

        $container = Application::getInstance()->getContainer();
        $this->list = [
            'directusFilesItem' => [
                'type' => Types::directusFile(),
                'description' => 'Return single file.',
                'args' => [
                    'id' => Types::nonNull(Types::id()),
                ],
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {
                    $service = new FilesServices($container);
                    return $service->findByIds(
                        $args['id'],
                        $this->param
                    )['data'];
                }
            ],
            'directusFiles' => [
                'type' => Types::listOf(Types::directusFile()),
                'description' => 'Return list of files.',
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {
                    $service = new FilesServices($container);
                    return $service->findAll($this->param)['data'];
                }
            ],
            'directusFileThumbnail' => [
                'type' => Types::directusFileThumbnail(),
                'description' => 'Return single file thumbnail.',
            ],
            'directusUsersItem' => [
                'type' => Types::directusUser(),
                'description' => 'Return single user.',
                'args' => [
                    'id' => Types::nonNull(Types::id()),
                ],
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {
                    $service = new UsersService($container);
                    return $service->findByIds(
                        $args['id'],
                        $this->param
                    )['data'];

                }
            ],
            'directusUsers' => [
                'type' => Types::listOf(Types::directusUser()),
                'description' => 'Return list of users.',
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {
                    $service = new UsersService($container);
                    return $service->findAll($this->param)['data'];
                }
            ],
            'directusRoleItem' => [
                'type' => Types::directusRole(),
                'description' => 'Return single directus role.',
                'args' => [
                    'id' => Types::nonNull(Types::id()),
                ],
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {

                    $service = new RolesService($container);
                    $data =  $service->findByIds(
                        $args['id'],
                        $this->param
                    )['data'];

                    return $data;

                }
            ],
            'directusRole' => [
                'type' => Types::listOf(Types::directusRole()),
                'description' => 'Return list of directus roles.',
                'resolve' => function($val, $args, $context, ResolveInfo $info) use($container) {

                    $service = new RolesService($container);
                    return $service->findAll($this->param)['data'];

                }
            ],
        ];

    }
}
