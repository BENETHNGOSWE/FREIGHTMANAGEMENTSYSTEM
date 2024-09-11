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
        Schema::table('customers', function (Blueprint $table) {
    
            // Ensure you have 'customer_id' and 'user_id' in the 'customers' table
            $table
                ->foreign('client_id') // Assuming the column is 'customer_id', not 'customers_id'
                ->references('id')
                ->on('clients')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
    
            $table
                ->foreign('user_id') // Corrected to 'user_id' instead of 'users_id'
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['user_id']);
        });
    }
    
};
