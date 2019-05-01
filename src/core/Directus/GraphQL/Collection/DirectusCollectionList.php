<?php
namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\Services\ActivityService;
use Directus\Services\TablesService;
use Directus\Services\CollectionPresetsService;
use Directus\Services\FilesServices;
use Directus\Services\PermissionsService;
use Directus\Services\RelationsService;
use Directus\Services\RevisionsService;
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
            'directus_activity' => [
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
            'directus_activity_collection' => [
                'type' => Types::collections(Types::directusActivity()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_activity')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new ActivityService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_collections' => [
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
            'directus_collections_collection' => [
                'type' => Types::collections(Types::directusCollection()),
                'args' => array_merge($this->limit, $this->offset),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new TablesService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_collection_presets' => [
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
            'directus_collection_presets_collection' => [
                'type' => Types::collections(Types::directusCollectionPreset()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_collection_presets')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new CollectionPresetsService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_fields' => [
                'type' => Types::directusField(),
                'args' => ['collection' => Types::nonNull(Types::string()), 'field' => Types::nonNull(Types::string())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new TablesService($this->container);
                    return $service->findField(
                        $args['collection'],
                        $args['field'],
                        $this->param
                    )['data'];
                }
            ],
            'directus_fields_collection' => [
                'type' => Types::collections(Types::directusField()),
                'args' => ['collection' => Types::string()],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new TablesService($this->container);
                    if (isset($args['collection'])) {
                        return $service->findAllFieldsByCollection(
                            $args['collection'],
                            $this->param
                        );
                    } else {
                        return $service->findAllFields(
                            $this->param
                        );
                    }
                }
            ],
            'directus_files' => [
                'type' => Types::directusFile(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new FilesServices($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directus_files_collection' => [
                'type' => Types::collections(Types::directusFile()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_files')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new FilesServices($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_folders' => [
                'type' => Types::directusFolder(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new FilesServices($this->container);
                    return $service->findFolderByIds($args['id'], $this->param)['data'];
                }
            ],
            'directus_folders_collection' => [
                'type' => Types::collections(Types::directusFolder()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_folders')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new FilesServices($this->container);
                    return $service->findAllFolders($this->param);
                }
            ],
            'directus_permissions' => [
                'type' => Types::directusPermission(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new PermissionsService($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directus_permissions_collection' => [
                'type' => Types::collections(Types::directusPermission()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_permissions')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new PermissionsService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_relations' => [
                'type' => Types::directusRelation(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new RelationsService($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directus_relations_collection' => [
                'type' => Types::collections(Types::directusRelation()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_permissions')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new RelationsService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_revisions' => [
                'type' => Types::directusRevision(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new RevisionsService($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directus_revisions_collection' => [
                'type' => Types::collections(Types::directusRevision()),
                'args' => array_merge($this->limit, $this->offset),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new RevisionsService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_roles' => [
                'type' => Types::directusRole(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new RolesService($this->container);
                    $data =  $service->findByIds($args['id'], $this->param)['data'];
                    return $data;
                }
            ],
            'directus_roles_collection' => [
                'type' => Types::collections(Types::directusRole()),
                'args' => array_merge($this->limit, $this->offset),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new RolesService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_settings' => [
                'type' => Types::directusSetting(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new SettingsService($this->container);
                    $data =  $service->findByIds($args['id'], $this->param)['data'];
                    return $data;
                }
            ],
            'directus_settings_collection' => [
                'type' => Types::collections(Types::directusSetting()),
                'args' => array_merge($this->limit, $this->offset),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new SettingsService($this->container);
                    return $service->findAll($this->param);
                }
            ],
            'directus_users' => [
                'type' => Types::directusUser(),
                'args' => ['id' => Types::nonNull(Types::id())],
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $service = new UsersService($this->container);
                    return $service->findByIds($args['id'], $this->param)['data'];
                }
            ],
            'directus_users_collection' => [
                'type' => Types::collections(Types::directusUser()),
                'args' => array_merge($this->limit, $this->offset, ['filter' => Types::filters('directus_users')]),
                'resolve' => function ($val, $args, $context, ResolveInfo $info) {
                    $this->convertArgsToFilter($args);
                    $service = new UsersService($this->container);
                    return $service->findAll($this->param);
                }
            ]
        ];
    }
}
