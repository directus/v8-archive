<?php

use Phinx\Migration\AbstractMigration;

class CreateRolesTable extends AbstractMigration
{
    /**
     * Create Roles Table
     */
    public function change()
    {
        $table = $this->table('directus_roles', ['signed' => false]);

        $table->addColumn('name', 'string', [
            'limit' => 100,
            'null' => false
        ]);
        $table->addColumn('description', 'string', [
            'limit' => 500,
            'null' => true,
            'default' => NULL
        ]);
        $table->addColumn('ip_whitelist', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('nav_blacklist', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('external_id', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('nav_override', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('enforce_2fa', 'boolean', [
            'null' => true,
            'default' => false
        ]);

        $table->addIndex('name', [
            'unique' => true,
            'name' => 'idx_group_name'
        ]);

        $table->addIndex('external_id', [
            'unique' => true,
            'name' => 'idx_roles_external_id'
        ]);

        $table->create();
        $data = [
            [
                'collection' => 'directus_roles',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INTEGER,
                'interface' => 'primary-key',
                'locked' => 1,
                'required' => 1,
                'hidden_detail' => 1
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'external_id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'locked' => 1,
                'readonly' => 1,
                'hidden_detail' => 1,
                'hidden_browse' => 1
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'name',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'locked' => 1,
                'sort' => 1,
                'width' => 'half',
                'required' => 1
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'description',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'locked' => 1,
                'sort' => 2,
                'width' => 'half'
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'ip_whitelist',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'textarea',
                'locked' => 1
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'nav_blacklist',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'textarea',
                'locked' => 1,
                'hidden_detail' => 1,
                'hidden_browse' => 1
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'users',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_O2M,
                'interface' => 'one-to-many',
                'locked' => 1,
                'options' => json_encode([
                    'fields' => "first_name,last_name"
                ])
            ],
            [
                'collection' => 'directus_roles',
                'field' => 'nav_override',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json',
                'locked' => 1,
                'options' => '[
                    {
                        "title": "$t:collections",
                        "include": "collections"
                    },
                    {
                        "title": "$t:bookmarks",
                        "include": "bookmarks"
                    },
                    {
                        "title": "$t:extensions",
                        "include": "extensions"
                    },
                    {
                        "title": "Custom Links",
                        "links": [
                            {
                                "name": "RANGER Studio",
                                "path": "https://rangerstudio.com",
                                "icon": "star"
                            },
                            {
                                "name": "Movies",
                                "path": "/collections/movies"
                            }
                        ]
                    }
                ]'
            ],
        ];

        foreach($data as $value){
            if(!$this->checkFieldExist($value['collection'], $value['field'])){
                $fileds = $this->table('directus_fields');
                $fileds->insert($value)->save();
            }
        }

        $rolesData = [
            [
                'id' => 1,
                'name' => 'Administrator',
                'description' => 'Admins have access to all managed data within the system by default'
            ],
            [
                'id' => 2,
                'name' => 'Public',
                'description' => 'Controls what API data is publicly available without authenticating'
            ]
        ];

        $groups = $this->table('directus_roles');
        $groups->insert($rolesData)->save();
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
