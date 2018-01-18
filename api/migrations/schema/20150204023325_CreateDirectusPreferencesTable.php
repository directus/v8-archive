<?php

/*
CREATE TABLE `directus_preferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `table_name` varchar(64) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `columns_visible` varchar(300) DEFAULT NULL,
  `sort` varchar(64) DEFAULT 'id',
  `sort_order` varchar(5) DEFAULT 'asc',
  `active` varchar(5) DEFAULT '3',
  `search_string` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`,`table_name`,`title`),
  UNIQUE KEY `pref_title_constraint` (`user`,`table_name`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusPreferencesTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_preferences', [
            'id' => false,
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('user', 'integer', [
            'unsigned' => true,
            'null' => false
        ]);
        $t->column('table_name', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('title', 'string', [
            'limit' => 128,
            'default' => null
        ]);

        $t->column('visible_fields', 'string', [
            'limit' => 300,
            'default' => null
        ]);
        $t->column('sort', 'string', [
            'limit' => 64,
            'default' => null
        ]);

        $t->column('status', 'string', [
            'limit' => 64,
            'default' => null
        ]);

        $t->column('search_string', 'text');

        $t->column('list_view_options', 'text');

        $t->finish();

        $this->add_index('directus_preferences', ['user', 'table_name', 'title'], [
            'unique' => true,
            'name' => 'user_table_title'
        ]);
    }//up()

    public function down()
    {
        $this->remove_index('directus_preferences', ['user', 'table_name', 'title'], [
            'unique' => true,
            'name' => 'user_table_title'
        ]);
        $this->drop_table('directus_preferences');
    }//down()
}
