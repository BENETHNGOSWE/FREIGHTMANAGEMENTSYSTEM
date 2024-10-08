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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('prefix');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_transportfee', 10, 2);
            $table->decimal('total_balance', 10, 2);
            $table->tinyInteger('status')->comment('0: pending, 1: fullpaid, 2: partialpaid');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
