<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusFieldsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_fields', [
            'id' => false,
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'auto_increment' => true,
            'null' => false,
            'primary_key' => true
        ]);
        $t->column('collection', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('field', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('type', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('interface', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('options', 'text', ['null' => true, 'default' => null]);
        $t->column('locked', 'tinyinteger', ['null' => false, 'default' => 0]);
        $t->column('translation', 'text', ['null' => true, 'default' => null]);
        $t->column('required', 'tinyinteger', ['null' => false, 'default' => 0]);
        $t->column('sort', 'integer', ['limit' => 11, 'unsigned' => true, 'null' => true, 'default' => null]);
        $t->column('comment', 'string', ['limit' => 1024, 'null' => true, 'default' => null]);
        $t->column('hidden_input', 'tinyinteger', ['null' => false, 'default' => 0]); // TODO: wasn't it removed?
        $t->column('hidden_list', 'tinyinteger', ['null' => false, 'default' => 0]); // TODO: or this was?

        $t->finish();

        $this->add_index('directus_fields', ['collection', 'field'], [
            'unique' => true,
            'name' => 'collection-field'
        ]);

        // $this->insert('directus_fields', [
        //     'collection' => 'directus_users',
        //     'field' => 'group',
        //     'type' => 'INT',
        //     'interface' => 'many_to_one',
        //     'hidden_input' => 0,
        //     'required' => 0,
        //     'relationship_type' => 'MANYTOONE',
        //     'related_table' => 'directus_groups',
        //     'junction_table' => null,
        //     'junction_key_left' => null,
        //     'junction_key_right' => 'group_id',
        //     'sort' => null,
        //     'comment' => ''
        // ]);

        // $this->insert('directus_columns', [
        //     'table_name' => 'directus_users',
        //     'column_name' => 'avatar_file_id',
        //     'data_type' => 'INT',
        //     'ui' => 'single_file',
        //     'hidden_input' => 0,
        //     'required' => 0,
        //     'relationship_type' => 'MANYTOONE',
        //     'related_table' => 'directus_files',
        //     'junction_table' => NULL,
        //     'junction_key_left' => NULL,
        //     'junction_key_right' => 'avatar_file_id',
        //     'sort' => NULL,
        //     'comment' => ''
        // ]);
        //
        // $this->insert('directus_columns', [
        //     'table_name' => 'directus_groups',
        //     'column_name' => 'users',
        //     'data_type' => 'ALIAS',
        //     'ui' => 'directus_users',
        //     'hidden_input' => 0,
        //     'required' => 0,
        //     'relationship_type' => 'ONETOMANY',
        //     'related_table' => 'directus_users',
        //     'junction_table' => NULL,
        //     'junction_key_left' => NULL,
        //     'junction_key_right' => 'group'
        // ]);
        //
        // $this->insert('directus_columns', [
        //     'table_name' => 'directus_groups',
        //     'column_name' => 'permissions',
        //     'data_type' => 'ALIAS',
        //     'ui' => 'directus_permissions',
        //     'hidden_input' => 0,
        //     'required' => 0,
        //     'relationship_type' => 'ONETOMANY',
        //     'related_table' => 'directus_privileges',
        //     'junction_table' => NULL,
        //     'junction_key_left' => NULL,
        //     'junction_key_right' => 'group_id'
        // ]);
    }//up()

    public function down()
    {
        $this->remove_index('directus_fields', ['collection', 'field'], [
            'unique' => true,
            'name' => 'collection-field'
        ]);

        $this->drop_table('directus_fields');
    }//down()
}
