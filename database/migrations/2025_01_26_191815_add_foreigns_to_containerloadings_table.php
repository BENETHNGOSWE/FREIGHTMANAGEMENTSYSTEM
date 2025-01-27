<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('containerloadings', function (Blueprint $table) {
            $table
            ->foreign('client_id')
            ->references('id')
            ->on('clients')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');

            $table
            ->foreign('loadpackage_id')
            ->references('id')
            ->on('loadpackages')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');

            $table
            ->foreign('container_id')
            ->references('id')
            ->on('containers')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containerloadings', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['loadpackage_id']);
            $table->dropForeign(['container_id']);
        });
    }
};
