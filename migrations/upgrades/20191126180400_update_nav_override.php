<?php


use Phinx\Migration\AbstractMigration;

class UpdateNavOverride extends AbstractMigration
{
    /**
     * Version: v8.0.0
     *
     * Change:
     * Removes the nav_override column in favor of two new ones: module_listing
     *         and collection_listing. These two JSON fields control the navigation
     *         as displayed in the app
     */
    public function change() {
        $rolesTable = $this->table('directus_roles');

        /* Remove the old nav_override column
        ----------------------------------------------------- */
        if ($rolesTable->hasColumn('nav_override')) {
           $rolesTable->removeColumn('nav_override');
        }
        /* -------------------------------------------------- */

        /* Add module_listing and collection_listing columns
        ----------------------------------------------------- */
        if ($rolesTable->hasColumn('module_listing') === false) {
            $rolesTable->addColumn('module_listing', 'text', [
                'null' => true,
                'default' => null
            ])->save();
        }

        if ($rolesTable->hasColumn('collection_listing') === false) {
            $rolesTable->addColumn('collection_listing', 'text', [
                'null' => true,
                'default' => null
            ])->save();
        }
        /* -------------------------------------------------- */

        /* Configure field options for two new columns
        ----------------------------------------------------- */
        $fieldsTable = $this->table('directus_fields');

        if ($this->checkFieldExist('directus_roles', 'module_listing')) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'interface' => 'repeater',
                    'options' => '{
                        "template": "{{ name }}",
                        "fields": [
                            {
                                "field": "name",
                                "interface": "text-input",
                                "type": "string",
                                "width": "half"
                            },
                            {
                                "field": "link",
                                "interface": "text-input",
                                "type": "string",
                                "width": "half"
                            },
                            {
                                "field": "icon",
                                "interface": "icon",
                                "type": "string",
                                "width": "full"
                            }
                        ]
                    }',
                ],
                ['collection' => 'directus_roles', 'field' => 'module_listing']
            ));
        } else {
            $fieldsTable->insert([
                'collection' => 'directus_roles',
                'field' => 'module_listing',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'repeater',
                'locked' => 1,
                'options' => '{
                    "template": "{{ name }}",
                    "fields": [
                        {
                            "field": "name",
                            "interface": "text-input",
                            "type": "string",
                            "width": "half"
                        },
                        {
                            "field": "link",
                            "interface": "text-input",
                            "type": "string",
                            "width": "half"
                        },
                        {
                            "field": "icon",
                            "interface": "icon",
                            "type": "string",
                            "width": "full"
                        }
                    ]
                }'
            ])->save();
        }

        if ($this->checkFieldExist('directus_roles', 'collection_listing')) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'interface' => 'repeater',
                    'options' => '{
                        "template": "{{ group_name }}",
                        "fields": [
                            {
                                "field": "group_name",
                                "interface": "text-input",
                                "type": "string",
                                "width": "full"
                            },
                            {
                                "field": "collections",
                                "interface": "repeater",
                                "type": "json",
                                "width": "full",
                                "options": {
                                    "fields": [
                                        {
                                            "field": "collection",
                                            "interface": "collections",
                                            "type": "string",
                                            "width": "full"
                                        }
                                    ]
                                }
                            }
                        ]
                    }'
                ],
                ['collection' => 'directus_roles', 'field' => 'collection_listing']
            ));
        } else {
            $fieldsTable->insert([
                'collection' => 'directus_roles',
                'field' => 'collection_listing',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'repeater',
                'locked' => 1,
                'options' => '{
                    "template": "{{ group_name }}",
                    "fields": [
                        {
                            "field": "group_name",
                            "interface": "text-input",
                            "type": "string",
                            "width": "full"
                        },
                        {
                            "field": "collections",
                            "interface": "repeater",
                            "type": "json",
                            "width": "full",
                            "options": {
                                "fields": [
                                    {
                                        "field": "collection",
                                        "interface": "collections",
                                        "type": "string",
                                        "width": "full"
                                    }
                                ]
                            }
                        }
                    ]
                }'
            ])->save();
        }
        /* -------------------------------------------------- */
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
