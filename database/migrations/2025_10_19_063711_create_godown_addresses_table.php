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
        Schema::create('godown_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('godown_location');
            $table->string('godown_address_one')->nullable();
            $table->string('godown_address_two')->nullable();
            $table->string('godown_phonenumber')->nullable();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('godown_addresses');
    }
};
