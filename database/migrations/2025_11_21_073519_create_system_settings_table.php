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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->id();
            $table->foreignId('client_id'); // IMPORTANT: For multi-tenancy
            $table->string('key');
            $table->text('value')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            // Unique constraint per client
            $table->unique(['client_id', 'key']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
