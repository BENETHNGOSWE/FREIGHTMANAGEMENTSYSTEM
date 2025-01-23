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
        Schema::create('derivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('driver_name'); 
            $table->string('driver_number'); 
            $table->string('vehicle_reg_no');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('invoice_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('derivery_orders');
    }
};
