<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Services\FilesServices;
use Directus\Services\UsersService;
use Directus\Services\RolesService;
use Directus\GraphQL\Collection\CollectionList;


class DirectusCollectionList extends CollectionList
{

    public $list;

    public function __construct()
    {

        parent::__construct();

        $this->list = [
            'directusFilesItem' => [
                'type' => Types::directusFile(),
                'description' => 'Return single file.',
                'args' => ['id' => Types::nonNull(Types::id()), ],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new FilesServices($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directusFiles' => [
                'type' => Types::collections(Types::directusFile()),
                'description' => 'Return list of files.',
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_files')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->parseArgs($args);
                    $service = new FilesServices($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusFileThumbnail' => [
                'type' => Types::directusFileThumbnail(),
                'description' => 'Return single file thumbnail.',
            ],
            'directusUsersItem' => [
                'type' => Types::directusUser(),
                'description' => 'Return single user.',
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new UsersService($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directusUsers' => [
                'type' => Types::collections(Types::directusUser()),
                'description' => 'Return list of users.',
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_users')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->parseArgs($args);
                    $service = new UsersService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusRoleItem' => [
                'type' => Types::directusRole(),
                'description' => 'Return single directus role.',
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new RolesService($this->container);
                    $data =  $service->findByIds($args['id'], $this->param)['data'];
                    return $data;
                }
            ],
            'directusRole' => [
                'type' => Types::collections(Types::directusRole()),
                'description' => 'Return list of directus roles.',
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_roles')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->parseArgs($args);
                    $service = new RolesService($this->container);
                    return $service->findAll($this->param);
                }
            ],
        ];
    }
}
