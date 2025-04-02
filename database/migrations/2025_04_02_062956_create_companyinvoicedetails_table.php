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
        Schema::create('companyinvoicedetails', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable(); 
            $table->string('office_phone');
            $table->string('tin_number')->nullable(); 
            $table->string('tagline')->nullable(); 
            $table->string('logo')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companyinvoicedetails');
    }
};
