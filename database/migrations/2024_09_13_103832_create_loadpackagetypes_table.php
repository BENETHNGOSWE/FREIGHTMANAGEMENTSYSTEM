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
        Schema::create('loadpackagetypes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('load_type_name');
            $table->string('load_type_description')->nullable();
            $table->foreignId('client_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loadpackagetypes');
    }
};
