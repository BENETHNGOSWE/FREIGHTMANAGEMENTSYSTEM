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
            $table->string('load_location')->nullable();
            $table->foreignId('client_id');
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
