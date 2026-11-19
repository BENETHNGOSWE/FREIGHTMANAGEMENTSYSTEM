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
        Schema::table('customerleads', function (Blueprint $table) {
            // The constraint only exists once the foreign keys have been added; on a
            // database where that already failed (nullable user_id) there is nothing to drop.
            if (Schema::hasColumn('customerleads', 'user_id')) {
                foreach (DB::select(
                    "select constraint_name from information_schema.key_column_usage
                     where table_schema = database() and table_name = 'customerleads'
                       and column_name = 'user_id' and referenced_table_name is not null"
                ) as $foreign) {
                    $table->dropForeign($foreign->constraint_name);
                }
            }

        });

        // SET NULL needs a nullable column: MySQL refuses the constraint on a NOT NULL one.
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
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }
};
