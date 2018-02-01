<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusFilesTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_files', [
            'id' => false,
            //'options' => 'COMMENT="Directus Files Storage"'
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('filename', 'string', [
            'limit' => 255,
            'null' => false,
            'default' => null
        ]);
        $t->column('title', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);
        $t->column('description', 'text', ['null' => true, 'default' => null]);
        $t->column('location', 'string', [
            'limit' => 200,
            'null' => true,
            'default' => null
        ]);
        $t->column('tags', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);
        $t->column('width', 'integer', [
            'limit' => 11,
            'unsigned' => true,
            'null' => true,
            'default' => null
        ]);
        $t->column('height', 'integer', [
            'limit' => 11,
            'unsigned' => true,
            'null' => true,
            'default' => null
        ]);
        $t->column('filesize', 'integer', [
            'unsigned' => true,
            'default' => 0
        ]);
        $t->column('duration', 'integer', [
            'limit' => 11,
            'unsigned' => true,
            'null' => true,
            'default' => null
        ]);
        $t->column('metadata', 'text', [
            'null' => true,
            'default' => null
        ]);
        $t->column('type', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null // unknown type?
        ]);
        $t->column('charset', 'string', [
            'limit' => 50,
            'null' => true,
            'default' => null
        ]);
        $t->column('embed', 'string', [
            'limit' => 200,
            'null' => true,
            'default' => NULL
        ]);
        $t->column('folder', 'integer', [
            'limit' => 11,
            'unsigned' => true,
            'null' => true,
            'default' => null
        ]);
        $t->column('upload_user', 'integer', [
            'unsigned' => true,
            'null' => false
        ]);
        // TODO: Make directus set this value to whatever default is on the server (UTC)
        // In MySQL 5.5 and below doesn't support CURRENT TIMESTAMP on datetime as default
        $t->column('upload_date', 'datetime', [
            'null' => false
        ]);
        $t->column('storage_adapter', 'string', [
            'limit' => 50,
            'null' => false
        ]);

        $t->finish();

        $this->insert('directus_files', [
            'id' => 1,
            'filename' => '00000000001.jpg',
            'title' => 'Mountain Range',
            'description' => 'A gorgeous view of this wooded mountain range',
            'location' => 'Earth',
            'tags' => 'trees,rocks,nature,mountains,forest',
            'width' => 1800,
            'height' => 1200,
            'filesize' => 602058,
            'type' => 'image/jpeg',
            'charset' => 'binary',
            'upload_user' => 1,
            'upload_date' => \Directus\Util\DateUtils::now(),
            'storage_adapter' => 'local'
        ]);
    }//up()

    public function down()
    {
        $this->drop_table('directus_files');
    }//down()
}
