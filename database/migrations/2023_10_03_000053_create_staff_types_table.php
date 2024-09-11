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
        Schema::create('staff_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('staff_type_name');
            $table->string('staff_type_description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_admin')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_types');
    }
};
