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
        Schema::create('invoice_loads', function (Blueprint $table) {
            $table->id();
            $table->decimal('load_price', 10, 2);
            $table->unsignedBigInteger('loadpackage_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_loads');
    }
};
