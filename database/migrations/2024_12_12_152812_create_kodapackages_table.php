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
        Schema::create('kodapackages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->double('monthly_fee');
            $table->double('yearly_fee');
            $table->unsignedBigInteger('client_admin_id');
            $table->unsignedBigInteger('staff_type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kodapackages');
    }
};
