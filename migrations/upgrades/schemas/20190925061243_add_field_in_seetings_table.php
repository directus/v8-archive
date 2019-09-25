<?php


use Phinx\Migration\AbstractMigration;

class AddFieldInSeetingsTable extends AbstractMigration
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
        $fieldObject = [
            'field' => 'relational_data_limit',
            'type' => 'string',
            'interface' => 'text-input',
            'locked' => 1,
            'width' => 'half',
            'note' => 'Define the level for fetch the relational data',
        ];
        $collection = 'directus_settings';
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $fieldObject['field']);
        $result = $this->query($checkSql)->fetch();
        if (!$result) {
            $insertSqlFormat = "INSERT INTO `directus_fields` (`collection`, `field`, `type`, `interface`, `note`, `locked` , `width`) VALUES ('%s', '%s', '%s', '%s' , '%s', '%s', '%s');";
            $insertSql = sprintf($insertSqlFormat, $collection, $fieldObject['field'], $fieldObject['type'], $fieldObject['interface'], $fieldObject['note'], $fieldObject['locked'], $fieldObject['width']);
            $this->execute($insertSql);
        }

        // Insert Into Directus Settings
        $checkSql = sprintf('SELECT 1 FROM `directus_settings` WHERE `key` = "%s";', $fieldObject['field']);
        $result = $this->query($checkSql)->fetch();
       
        if(!$result){
            $insertSqlFormat = "INSERT INTO `directus_settings` (`key`, `value`) VALUES ('%s', '%s');";
            $insertSql = sprintf($insertSqlFormat,$fieldObject['field'],'*.*.*');
            $this->execute($insertSql);
        }
    }
}
