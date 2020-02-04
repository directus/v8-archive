<?php

declare(strict_types=1);

use Directus\Core\Database\Migrations\TableMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create fields table.
 */
class CreateFieldsTable extends Migration
{
    use TableMigration;

    /**
     * Table name.
     */
    public function tableName(): string
    {
        return 'fields';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table(), function (Blueprint $table): void {
            $table->bigIncrements('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table());
    }
}
