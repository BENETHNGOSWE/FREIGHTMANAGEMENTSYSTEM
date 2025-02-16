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
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();
            $table->string('receipt')->nullable();
            $table->date('date')->nullable();
            $table->decimal('cbm', 8, 2)->nullable();
            $table->string('nature')->nullable();
            $table->string('hub')->nullable();
            $table->decimal('amount1', 10, 2)->nullable();
            $table->decimal('amount2', 10, 2)->nullable();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('staff_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
