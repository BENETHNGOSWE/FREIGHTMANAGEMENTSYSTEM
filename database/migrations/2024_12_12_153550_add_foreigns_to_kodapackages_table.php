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
        Schema::table('kodapackages', function (Blueprint $table) {
            $table
            ->foreign('client_admin_id')
            ->references('id')
            ->on('client_admins')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');

            $table
            ->foreign('staff_type_id')
            ->references('id')
            ->on('staff_types')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kodapackages', function (Blueprint $table) {
            $table->dropForeign(['client_admin_id']);
            $table->dropForeign(['staff_type_id']);
        });
    }
};
