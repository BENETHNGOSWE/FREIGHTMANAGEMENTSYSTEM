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
        Schema::table('loadpackages', function (Blueprint $table) {
            $table
            ->foreign('client_id') // Assuming the column is 'customer_id', not 'customers_id'
            ->references('id')
            ->on('clients')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loadpackages', function (Blueprint $table) {
            //
        });
    }
};
