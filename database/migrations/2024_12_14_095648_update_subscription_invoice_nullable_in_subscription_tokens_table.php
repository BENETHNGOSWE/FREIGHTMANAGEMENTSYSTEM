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
        Schema::table('subscription_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_invoice_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_tokens', function (Blueprint $table) {
            $table->dropForeign(['subscription_invoice_id']);
            $table
                ->foreign('subscription_invoice_id')
                ->references('id')
                ->on('subscription_invoices')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }
};
