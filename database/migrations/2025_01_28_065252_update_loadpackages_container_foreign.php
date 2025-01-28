<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loadpackages', function (Blueprint $table) {
            $table->dropForeign(['container_id']);

            // Recreate the foreign key constraint with SET NULL
            $table->foreign('container_id')->references('id')->on('containers')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loadpackages', function (Blueprint $table) {
            $table->dropForeign(['container_id']);

        // Recreate the original foreign key constraint with CASCADE
        $table->foreign('container_id')
              ->references('id')
              ->on('containers')
              ->onUpdate('CASCADE')
              ->onDelete('SET NULL');
        });
    }
};
