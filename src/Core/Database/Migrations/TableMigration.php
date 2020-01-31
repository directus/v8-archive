<?php

declare(strict_types=1);

namespace Directus\Core\Database\Migrations;

/**
 * Base migration.
 */
trait TableMigration
{
    /**
     * Table name.
     */
    abstract public function tableName(): string;

    /**
     * Gets the table name.
     */
    protected function table(): string
    {
        $prefix = 'directus_';
        if (\function_exists('config')) {
            $prefix = config('directus.database.prefix', $prefix);
        }

        return $prefix.$this->tableName();
    }
}
