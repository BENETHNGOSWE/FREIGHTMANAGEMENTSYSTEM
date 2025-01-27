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
        Schema::table('loadpackages', function (Blueprint $table) {
            $table->decimal('load_length', 8, 2)->nullable();
            $table->decimal('load_width', 8, 2)->nullable();
            $table->decimal('load_height', 8, 2)->nullable();
            $table->decimal('load_volume', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loadpackages', function (Blueprint $table) {
            $table->dropColumn(['load_length', 'load_width', 'load_height', 'load_volume']);
        });
    }
};
