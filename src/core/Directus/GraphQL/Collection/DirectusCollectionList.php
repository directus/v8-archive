<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Services\ActivityService;
use Directus\Services\TablesService;
use Directus\Services\CollectionPresetsService;
use Directus\Services\FilesServices;
use Directus\Services\UsersService;
use Directus\Services\RolesService;
use Directus\Services\SettingsService;
use Directus\GraphQL\Collection\CollectionList;


class DirectusCollectionList extends CollectionList
{

    public $list;

    public function __construct()
    {

        parent::__construct();

        $this->list = [
            'directusActivity' => [
                'type' => Types::directusActivity(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new ActivityService($this->container);
                    return $service->findByIds(
                        $args['id'],
                        $this->param
                    )['data'];
                }
            ],
            'directusActivityCollection' => [
                'type' => Types::collections(Types::directusActivity()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_activity')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new ActivityService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusCollections' => [
                'type' => Types::directusCollection(),
                'args' => ['name' => Types::nonNull(Types::string())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new TablesService($this->container);
                    return $service->findByIds(
                        $args['name'],
                        $this->param
                    )['data'];
                }
            ],
            'directusCollectionsCollection' => [
                'type' => Types::collections(Types::directusCollection()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_collections')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new TablesService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusCollectionPresets' => [
                'type' => Types::directusCollectionPreset(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new CollectionPresetsService($this->container);
                    return $service->findByIds(
                        $args['id'],
                        $this->param
                    )['data'];
                }
            ],
            'directusCollectionPresetsCollection' => [
                'type' => Types::collections(Types::directusCollectionPreset()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_collection_presets')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new CollectionPresetsService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusFields' => [
                'type' => Types::directusField(),
                'args' => ['name' => Types::nonNull(Types::string())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new TablesService($this->container);
                    return $service->findByIds(
                        $args['name'],
                        $this->param
                    )['data'];
                }
            ],
            'directusFieldsCollection' => [
                'type' => Types::collections(Types::directusField()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_fields')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new TablesService($this->container);
                    return $service->findAllFields(
                        $this->param
                    );
                }
            ],
            'directusFiles' => [
                'type' => Types::directusFile(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new FilesServices($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directusFilesCollection' => [
                'type' => Types::collections(Types::directusFile()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_files')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new FilesServices($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusFileThumbnail' => [
                'type' => Types::directusFileThumbnail(),
            ],
            'directusUsers' => [
                'type' => Types::directusUser(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new UsersService($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directusUsersCollection' => [
                'type' => Types::collections(Types::directusUser()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_users')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new UsersService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusRoles' => [
                'type' => Types::directusRole(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new RolesService($this->container);
                    $data =  $service->findByIds($args['id'], $this->param)['data'];
                    return $data;
                }
            ],
            'directusRolesCollection' => [
                'type' => Types::collections(Types::directusRole()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_roles')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new RolesService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directusSettings' => [
                'type' => Types::directusSetting(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new SettingsService($this->container);
                    $data =  $service->findByIds($args['id'], $this->param)['data'];
                    return $data;
                }
            ],
            'directusSettingsCollection' => [
                'type' => Types::collections(Types::directusSetting()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_settings')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new SettingsService($this->container);
                    return $service->findAll($this->param);
                }
            ],
        ];
    }
}
