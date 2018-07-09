<?php

use Phinx\Seed\AbstractSeed;

class FieldsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            // Activity
            [
                'collection' => 'directus_activity',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'type',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'action',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'user',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'user'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'datetime',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_DATETIME,
                'interface' => 'datetime'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'ip',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'user_agent',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'item',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_activity',
                'field' => 'comment',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_TEXT,
                'interface' => 'markdown'
            ],
            // Activity Read
            [
                'collection' => 'directus_activity_read',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_activity_read',
                'field' => 'activity',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_activity_read',
                'field' => 'user',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'user'
            ],
            [
                'collection' => 'directus_activity_read',
                'field' => 'read',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_activity_read',
                'field' => 'archived',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            // Collections
            [
                'collection' => 'directus_collections',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_collections',
                'field' => 'item_name_template',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_collections',
                'field' => 'preview_url',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_collections',
                'field' => 'managed',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_collections',
                'field' => 'hidden',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_collections',
                'field' => 'single',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_collections',
                'field' => 'translation',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            [
                'collection' => 'directus_collections',
                'field' => 'note',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            // Collection Presets
            [
                'collection' => 'directus_collection_presets',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'title',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'user',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'user'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'role',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'search_query',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'filters',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'view_options',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'view_type',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'view_query',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'translation',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            // Fields
            [
                'collection' => 'directus_fields',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'field',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'type',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'interface',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'options',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'locked',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'translation',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'readonly',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'required',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'sort',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'sort'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'note',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'hidden_input',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'hidden_list',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'view_width',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'numeric'
            ],
            [
                'collection' => 'directus_fields',
                'field' => 'group',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            // Files
            [
                'collection' => 'directus_files',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'filename',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'title',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'description',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_TEXT,
                'interface' => 'textarea'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'location',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'tags',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ARRAY,
                'interface' => 'tags'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'width',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'numeric'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'height',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'numeric'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'filesize',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'filesize'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'duration',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'numeric'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'metadata',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'type',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'charset',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'embed',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'folder',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'upload_user',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'user'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'upload_date',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_DATETIME,
                'interface' => 'datetime'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'storage_adapter',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'data',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BLOB,
                'interface' => 'blob',
                'options' => '{ "nameField": "filename", "sizeField": "filesize", "typeField": "type" }'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'url',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_files',
                'field' => 'storage',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ALIAS,
                'interface' => 'file-upload'
            ],
            // Folders
            [
                'collection' => 'directus_folders',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_folders',
                'field' => 'name',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_folders',
                'field' => 'parent_folder',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            // Roles
            [
                'collection' => 'directus_roles',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'name',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'description',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'textarea'
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'ip_whitelist',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_TEXT,
                'interface' => 'textarea'
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'nav_blacklist',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_TEXT,
                'interface' => 'textarea'
            ],
            // User Roles
            [
                'collection' => 'directus_user_roles',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_user_roles',
                'field' => 'user',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'user'
            ],
            [
                'collection' => 'directus_user_roles',
                'field' => 'role',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            // Users
            [
                'collection' => 'directus_users',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'status',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'status',
                'options' => '{"status_mapping":[{"name": "draft"},{"name": "active"},{"name": "delete"}]}'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'first_name',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'last_name',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'email',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'roles',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_M2M,
                'interface' => 'm2m'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'email_notifications',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'password',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'password'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'avatar',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_FILE,
                'interface' => 'single-file'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'company',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'title',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'locale',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'locale_options',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'timezone',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'last_ip',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'last_login',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_DATETIME,
                'interface' => 'datetime'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'last_access',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_DATETIME,
                'interface' => 'datetime'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'last_page',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_users',
                'field' => 'token',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            // Permissions
            [
                'collection' => 'directus_permissions',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'role',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'status',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'create',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'read',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'update',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'delete',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'navigate',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'explain',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'allow_statuses',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ARRAY,
                'interface' => 'tags'
            ],
            [
                'collection' => 'directus_permissions',
                'field' => 'read_field_blacklist',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'textarea'
            ],
            // Relations
            [
                'collection' => 'directus_relations',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_relations',
                'field' => 'collection_a',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_relations',
                'field' => 'field_a',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_relations',
                'field' => 'junction_key_a',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_relations',
                'field' => 'junction_mixed_collections',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_relations',
                'field' => 'junction_key_b',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_relations',
                'field' => 'collection_b',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_relations',
                'field' => 'field_b',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            // Revisions
            [
                'collection' => 'directus_revisions',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'primary-key'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'activity',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'item',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'data',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_LONG_JSON,
                'interface' => 'json'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'delta',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_LONG_JSON,
                'interface' => 'json'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'parent_item',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'parent_collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'many-to-one'
            ],
            [
                'collection' => 'directus_revisions',
                'field' => 'parent_changed',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle'
            ],
            // Settings
            [
                'collection' => 'directus_settings',
                'field' => 'auto_sign_out',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INT,
                'interface' => 'numeric'
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'youtube_api_key',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ],
        ];

        $files = $this->table('directus_fields');
        $files->insert($data)->save();
    }
}
