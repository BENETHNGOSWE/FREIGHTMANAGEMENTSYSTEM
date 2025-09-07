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
        Schema::table('containers', function (Blueprint $table) {
            // Only drop if exists
            if (Schema::hasColumn('containers', 'container_status')) {
                $table->dropColumn('container_status');
            }
        });

        Schema::table('containers', function (Blueprint $table) {
            // Add new status column with corrected values
            $table->enum('container_status', [
                'Warehouse',
                'OnVessel',
                'Arrived_TZ-Port',
                'ICD',
                'Warehouse-HQ',
            ])->nullable()->default('Warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('containers', function (Blueprint $table) {
            // Drop new column
            $table->dropColumn('container_status');
        });

        Schema::table('containers', function (Blueprint $table) {
            // Restore old statuses
            $table->enum('container_status', [
                'portstation',
                'onmovement',
                'delivery',
                'closed',
            ])->default('portstation');
        });
    }
};
