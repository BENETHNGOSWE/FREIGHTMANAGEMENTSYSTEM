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
        // Making the column nullable needs the foreign key off first (MySQL refuses a
        // nullable column that a NOT NULL constraint points at), then back on.
        Schema::table('customerleads', function (Blueprint $table) {
            foreach (DB::select(
                "select constraint_name from information_schema.key_column_usage
                 where table_schema = database() and table_name = 'customerleads'
                   and column_name = 'user_id' and referenced_table_name is not null"
            ) as $foreign) {
                $table->dropForeign($foreign->constraint_name);
            }
        });

        Schema::table('customerleads', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('customerleads', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customerleads', function (Blueprint $table) {
            //
        });
    }
};
