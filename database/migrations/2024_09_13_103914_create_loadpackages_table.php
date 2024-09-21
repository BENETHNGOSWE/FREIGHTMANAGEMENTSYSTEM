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
        Schema::create('loadpackages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('load_no');
            $table->string('load_description')->nullable();
            $table->string('load_name');
            $table->string('load_price');
            $table->string('load_transportfee');
            $table->string('load_transport_date');
            $table->string('load_delivery_date');
            $table->string('load_status');
            $table->string('comment_operator');
            $table->string('load_weight_kg');
            $table->string('load_location')->nullable();
            $table->foreignId('client_id');
            $table->foreignId('customer_id');
            $table->foreignId('loadpackagetype_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loadpackages');
    }
};
