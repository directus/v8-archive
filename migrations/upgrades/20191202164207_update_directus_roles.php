<?php

use Phinx\Migration\AbstractMigration;

class UpdateDirectusRoles extends AbstractMigration
{
    public function change()
    {
        $rolesTable = $this->table('directus_roles');

        // -------------------------------------------------------------------------
        // Remove unused nav_blacklist column
        // -------------------------------------------------------------------------
        if ($rolesTable->hasColumn('nav_blacklist')) {
            $rolesTable->removeColumn('nav_blacklist');
        }

        // -------------------------------------------------------------------------
        // Remove deprecated nav_override
        // -------------------------------------------------------------------------
        if ($rolesTable->hasColumn('nav_override')) {
            $rolesTable->removeColumn('nav_override');
        }

        // -------------------------------------------------------------------------
        // Add module_listing column
        // -------------------------------------------------------------------------
        if (false === $rolesTable->hasColumn('module_listing')) {
            $rolesTable->addColumn('module_listing', 'string', [
                'null' => true,
                'default' => null,
            ]);
        }

        // -------------------------------------------------------------------------
        // Add collection_listing column
        // -------------------------------------------------------------------------
        if (false === $rolesTable->hasColumn('collection_listing')) {
            $rolesTable->addColumn('collection_listing', 'string', [
                'null' => true,
                'default' => null,
            ]);
        }

        // -------------------------------------------------------------------------
        // Add enforce_2fa column
        // -------------------------------------------------------------------------
        if (false === $rolesTable->hasColumn('enforce_2fa')) {
            $rolesTable->addColumn('enforce_2fa', 'integer', [
                'null' => true,
                'default' => false,
            ]);
        }

        // -------------------------------------------------------------------------
        // Save changes to table
        // -------------------------------------------------------------------------
        $rolesTable->save();
    }
}
