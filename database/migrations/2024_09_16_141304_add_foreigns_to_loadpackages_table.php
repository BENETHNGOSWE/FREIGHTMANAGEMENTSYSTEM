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
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->foreign('loadpackagetype_id')->references('id')->on('loadpackagetypes')->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->foreign('unittype_id')->references('id')->on('unit_types')->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->foreign('container_id')->references('id')->on('containers')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loadpackages', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['loadpackagetype_id']);
            $table->dropForeign(['unittype_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['container_id']);
        });
    }
};
