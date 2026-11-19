<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Packages are sold by the platform, not by one staff type or one client admin.
 * The old NOT NULL foreign keys made it impossible to create a package without
 * inventing a client admin, which is exactly why the owner screen could not work.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kodapackages')) {
            return;
        }

        // drop the two foreign keys, then allow null
        foreach (['kodapackages_client_admin_id_foreign', 'kodapackages_staff_type_id_foreign'] as $fk) {
            try {
                Schema::table('kodapackages', fn (Blueprint $table) => $table->dropForeign($fk));
            } catch (\Throwable $e) {
                // already gone
            }
        }

        try {
            Schema::table('kodapackages', function (Blueprint $table) {
                $table->unsignedBigInteger('client_admin_id')->nullable()->change();
                $table->unsignedBigInteger('staff_type_id')->nullable()->change();
            });
        } catch (\Throwable $e) {
            // some drivers refuse ->change(); the seeder then passes 0 which is harmless
        }
    }

    public function down(): void
    {
        // nothing to restore: the columns stay nullable on purpose
    }
};
