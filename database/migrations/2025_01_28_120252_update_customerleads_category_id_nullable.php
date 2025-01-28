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
        Schema::table('customerleads', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->unsignedBigInteger('category_id')->nullable()->change();

            // Add foreign key constraint with onDelete('SET NULL')
            $table->foreign('category_id')
                  ->references('id')
                  ->on('customercategories')
                  ->onUpdate('CASCADE')
                  ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customerleads', function (Blueprint $table) {
            $table->dropForeign(['category_id']);

            // Revert the category_id column to not nullable
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });
    }
};
