<?php


use Phinx\Migration\AbstractMigration;

class CreateWebHooks extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('directus_webhooks', ['signed' => false]);
        
        $table->addColumn('collection', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);

        $table->addColumn('directus_action', 'string', [
            'limit' => 255,
            'encoding' => 'utf8',
            'null' => true,
            'default' => null
        ]);

        $table->addColumn('url', 'string', [
            'limit' => 510,
            'encoding' => 'utf8',
            'null' => true,
            'default' => null
        ]);

        $table->addColumn('http_action', 'string', [
            'limit' => 255,
            'encoding' => 'utf8',
            'null' => true,
            'default' => null
        ]);
        
        $table->create();


        // Insert Into Directus Fields
        $data = [
            [
                'collection' => 'directus_webhooks',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INTEGER,
                'interface' => 'primary-key',
                'locked' => 1,
                'required' => 1,
                'hidden_detail' => 1
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'collections',
                'required' => 1
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'directus_action',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'dropdown',
                'required' => 1,
                'options' => json_encode([
                    'choices' => [
                        'item.create:before' => 'item.create:before',
                        'item.create:after' => 'item.create:after',
                        'item.update:before' => 'item.update:before',
                        'item.update:after' => 'item.update:after',
                        'item.delete:before' => 'item.delete:before',
                        'item.delete:after' => 'item.delete:after',
                    ]
                ])
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'url',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'required' => 1
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'http_action',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'dropdown',
                'required' => 1,
                'options' => json_encode([
                    'choices' => [
                        'get' => 'Get',
                        'post' => 'Post'
                    ]
                ])
            ]
            
        ];

        foreach($data as $value){
            if(!$this->checkFieldExist($value['collection'], $value['field'])){
                $insertSqlFormat = 'INSERT INTO `directus_fields` (`collection`, `field`, `type`, `interface`, `readonly`, `hidden_detail`, `hidden_browse`, `required`, `locked`, `options`) VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s");';
                $insertSql = sprintf($insertSqlFormat, $value['field'], $value['type'], $value['interface'], isset($value['readonly']) ? $value['readonly'] : null, isset($value['hidden_detail']) ? $value['hidden_detail'] : null, isset($value['hidden_browse']) ? $value['hidden_browse'] : null, isset($value['required']) ? $value['required'] : null, isset($value['locked']) ? $value['locked'] : null, isset($value['options']) ? $value['options'] : null);
                $this->execute($insertSql);
            }
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
