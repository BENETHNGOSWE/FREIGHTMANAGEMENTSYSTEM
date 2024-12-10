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
        Schema::create('customerleads', function (Blueprint $table) {
            $table->id();
            $table->string('customer_no')->nullable();
            $table->string('customer_firstname');
            $table->string('customer_middlename')->nullable();
            $table->string('customer_lastname')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_photo')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_country')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('customer_document')->nullable();
            $table->text('customer_bio')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerleads');
    }
};
