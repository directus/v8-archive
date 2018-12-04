<?php


use Phinx\Migration\AbstractMigration;

class RemoveScope extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('directus_settings');
        $table->removeIndexByName('idx_scope_name');
        $table->removeColumn('scope')
              ->save();
        $table->addIndex(['key'], [
            'unique' => true,
            'name' => 'idx_scope_name'
        ]);
    }
}
