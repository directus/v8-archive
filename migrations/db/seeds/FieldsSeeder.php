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
            // Collections
            [
                'collection' => 'directus_collections',
                'field' => 'translation',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            // Collection Presets
            [
                'collection' => 'directus_collection_presets',
                'field' => 'filters',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'view_query',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'view_options',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            // Fields
            [
                'collection' => 'directus_collection_presets',
                'field' => 'translation',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'JSON'
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'required',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN
            ],
            [
                'collection' => 'directus_collection_presets',
                'field' => 'options',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON
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
                'interface' => 'wysiwyg'
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
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
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
                'interface' => 'file-size'
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
                'type' => \Directus\Database\Schema\DataTypes::TYPE_TEXT,
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
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR
            ],
            [
                'collection' => 'directus_files',
                'field' => 'storage',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ALIAS,
                'interface' => 'file-upload'
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
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR
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
                'field' => 'last_page',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_VARCHAR,
                'interface' => 'text-input'
            ]
        ];

        $files = $this->table('directus_fields');
        $files->insert($data)->save();
    }
}
