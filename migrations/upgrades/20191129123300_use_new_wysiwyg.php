<?php

use Phinx\Migration\AbstractMigration;

class UseNewWysiwyg extends AbstractMigration
{
    /**
     * Version: v8.0.0
     *
     * Change:
     * Make sure everybody who was using the advanced wysiwyg is now using the new wysiwyg interface
     */
    public function change() {
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            ['interface' => 'wysiwyg'],
            ['interface' => 'wysiwyg_advanced']
        ));
    }
}
