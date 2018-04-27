<?php

use Phinx\Seed\AbstractSeed;

class RelationsSeeder extends AbstractSeed
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
            [
                'collection_a' => 'directus_activity',
                'field_a' => 'user',
                'collection_b' => 'directus_users'
            ],
            [
                'collection_a' => 'directus_activity_read',
                'field_a' => 'user',
                'collection_b' => 'directus_users'
            ],
            [
                'collection_a' => 'directus_activity_read',
                'field_a' => 'activity',
                'collection_b' => 'directus_activity'
            ],
            [
                'collection_a' => 'directus_collections_presets',
                'field_a' => 'user',
                'collection_b' => 'directus_users'
            ],
            [
                'collection_a' => 'directus_collections_presets',
                'field_a' => 'group',
                'collection_b' => 'directus_groups'
            ],
            [
                'collection_a' => 'directus_files',
                'field_a' => 'upload_user',
                'collection_b' => 'directus_users'
            ],
            [
                'collection_a' => 'directus_files',
                'field_a' => 'folder',
                'collection_b' => 'directus_folders'
            ],
            [
                'collection_a' => 'directus_folders',
                'field_a' => 'parent_folder',
                'collection_b' => 'directus_folders'
            ],
            [
                'collection_a' => 'directus_permissions',
                'field_a' => 'group',
                'collection_b' => 'directus_groups'
            ],
            [
                'collection_a' => 'directus_revisions',
                'field_a' => 'activity',
                'collection_b' => 'directus_activity'
            ],
            [
                'collection_a' => 'directus_users',
                'field_a' => 'roles',
                'junction_key_a' => 'user',
                'junction_collection' => 'directus_user_roles',
                'junction_key_b' => 'role',
                'field_b' => 'users',
                'collection_b' => 'directus_roles'
            ],
            [
                'collection_a' => 'directus_users',
                'field_a' => 'avatar',
                'collection_b' => 'directus_files'
            ]
        ];

        $files = $this->table('directus_relations');
        $files->insert($data)->save();
    }
}
