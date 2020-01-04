<?php

use function Directus\get_random_string;
use Phinx\Migration\AbstractMigration;

class UpdateDirectusFiles extends AbstractMigration
{
    public function change()
    {
        $filesTable = $this->table('directus_files');

        // -------------------------------------------------------------------------
        // Rename filename to filename_disk
        // -------------------------------------------------------------------------
        if ($filesTable->hasColumn('filename')) {
            $filesTable->renameColumn('filename', 'filename_disk');
        }

        // -------------------------------------------------------------------------
        // Add filename_download column
        // -------------------------------------------------------------------------
        if (false === $filesTable->hasColumn('filename_download')) {
            $filesTable->addColumn('filename_download', 'string', [
                'limit' => 255,
                'null' => false,
            ]);
        }

        // -------------------------------------------------------------------------
        // Add private hash column
        // -------------------------------------------------------------------------
        if (false === $filesTable->hasColumn('private_hash')) {
            $filesTable->addColumn('private_hash', 'string', [
                'limit' => 16,
                'null' => true,
                'default' => null,
            ]);
        }

        // -------------------------------------------------------------------------
    // Add a private hash for all existing files
    // -------------------------------------------------------------------------
    $filesTable->save(); // Make sure the private hash column above is saved

    $filesWithoutPrivateHash = $this->fetchAll('SELECT id FROM directus_files WHERE private_hash = null;');

        foreach ($filesWithoutPrivateHash as $key => $value) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_files',
                ['private_hash' => get_random_string()],
                ['id' => $value['id']]
            ));
        }

        // -------------------------------------------------------------------------
        // Save changes to table
        // -------------------------------------------------------------------------
        $filesTable->save();
    }
}
