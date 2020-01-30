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
    public function tableName()
    {
        return 'fields';
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table(), function (Blueprint $table) {
            $table->bigIncrements('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->table());
    }
}
