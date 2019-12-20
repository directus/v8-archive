<?php


use Phinx\Migration\AbstractMigration;

class UpdateUserCreatedToOwner extends AbstractMigration
{
    public function change()
    {
        // To change the type of all the field of user generated collection to owner
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'type' => \Directus\Database\Schema\DataTypes::TYPE_OWNER,
                'interface' => 'owner'
            ],
            ['type' =>  'user_created']
        ));
    }

    public function checkFieldExist($collection, $field)
    {
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
