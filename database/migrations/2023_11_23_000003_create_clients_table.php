<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_name');
            $table->string('client_logo')->nullable();
            $table->string('client_location');
            $table->string('client_address_one')->nullable();
            $table->string('client_address_two')->nullable();
            $table->string('client_tinnumber')->nullable();
            $table->string('client_phonenumber')->nullable();
            $table->string('client_email')->unique('client_email');
            $table->string('client_city');
            $table->string('client_country');
            $table->string('client_zipcode')->nullable();
            $table->string('client_fax')->nullable();
            $table->string('client_website_link')->nullable();
            $table->unsignedBigInteger('client_type_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
