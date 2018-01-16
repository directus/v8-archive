<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class UpdateBookmarksTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        if ($this->has_column('directus_bookmarks', 'section')) {
            $this->remove_column('directus_bookmarks', 'section');
        }

        if ($this->has_column('directus_bookmarks', 'title')) {
            $this->change_column('directus_bookmarks', 'title', 'string', [
                'limit' => 255,
                'null' => false
            ]);
        }
    }//up()

    public function down()
    {
        if (!$this->has_column('directus_bookmarks', 'section')) {
            $this->add_column('directus_bookmarks', 'section', 'string', [
                'limit' => 255,
                'default' => NULL
            ]);
        }

        if ($this->has_column('directus_bookmarks', 'title')) {
            $this->change_column('directus_bookmarks', 'title', 'string', [
                'limit' => 255,
                'default' => null
            ]);
        }
    }//down()
}
