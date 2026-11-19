<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Platform-wide settings (grace days, enforcement switch). They are not per client, so they
 * cannot live in system_settings: that table is owned by a client (foreign key).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('platform_settings')) {
            return;
        }

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // the token column must accept "no invoice attached" (tokens can be issued without one)
        if (Schema::hasTable('subscription_tokens')) {
            try {
                Schema::table('subscription_tokens', fn (Blueprint $t) => $t->unsignedBigInteger('subscription_invoice_id')->nullable()->change());
            } catch (\Throwable $e) {
                // driver without ->change(): issueToken() creates an invoice instead
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
