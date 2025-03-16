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
        Schema::create('demorequests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email_address')->unique();
            $table->string('phone_number');
            $table->string('company_name');
            $table->string('city');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demorequests');
    }
};
