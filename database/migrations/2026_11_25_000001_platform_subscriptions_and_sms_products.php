<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The owner side of the platform: what a client bought (freight / SMS / both),
 * what is included, what is used, and the token that unlocks it.
 *
 * Everything is guarded so the migration can be re-run on an old database.
 */
return new class extends Migration
{
    public function up(): void
    {
        // What kind of business the client runs. Freight clients get the cargo modules,
        // SMS clients get the bulk-SMS modules, some clients buy both.
        if (Schema::hasTable('clients') && ! Schema::hasColumn('clients', 'account_type')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('account_type', 20)->default('freight')->after('client_type_id');
                $table->string('subscription_status', 20)->default('active')->after('account_type');
            });
        }

        // Subscriptions gain the commercial details (package kind, seats, SMS balance, lock flag).
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (! Schema::hasColumn('subscriptions', 'product')) {
                    $table->string('product', 20)->default('freight')->after('package_id');
                }
                if (! Schema::hasColumn('subscriptions', 'sms_quota')) {
                    $table->unsignedInteger('sms_quota')->default(0)->after('product');
                }
                if (! Schema::hasColumn('subscriptions', 'sms_used')) {
                    $table->unsignedInteger('sms_used')->default(0)->after('sms_quota');
                }
                if (! Schema::hasColumn('subscriptions', 'amount')) {
                    $table->decimal('amount', 19, 2)->default(0)->after('sms_used');
                }
                if (! Schema::hasColumn('subscriptions', 'locked_at')) {
                    $table->timestamp('locked_at')->nullable()->after('status');
                }
                if (! Schema::hasColumn('subscriptions', 'notes')) {
                    $table->text('notes')->nullable()->after('locked_at');
                }
            });
        }

        // Every SMS attempt is counted, delivered or not: that is the running balance.
        if (Schema::hasTable('messages') && ! Schema::hasColumn('messages', 'subscription_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->unsignedBigInteger('subscription_id')->nullable()->after('client_id');
            });
        }

        // The token history, so the owner can see which token unlocked which period.
        if (Schema::hasTable('subscription_tokens')) {
            Schema::table('subscription_tokens', function (Blueprint $table) {
                if (! Schema::hasColumn('subscription_tokens', 'used_at')) {
                    $table->timestamp('used_at')->nullable()->after('status');
                }
                if (! Schema::hasColumn('subscription_tokens', 'used_by')) {
                    $table->unsignedBigInteger('used_by')->nullable()->after('used_at');
                }
            });
        }

        // Which sender name the client is currently sending with (the old table only had client_id).
        if (Schema::hasTable('clientactivenames') && ! Schema::hasColumn('clientactivenames', 'updated_at')) {
            Schema::table('clientactivenames', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // The catalogue of modules a package can include, seeded by PlatformSeeder.
        if (! Schema::hasTable('platform_modules')) {
            Schema::create('platform_modules', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();          // cargo, storage, accounts, sms_bulk...
                $table->string('name');
                $table->string('icon')->nullable();
                $table->string('group')->default('freight'); // freight | sms | both
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // package <-> module, by key (the old module_package table used ad-hoc ids).
        if (! Schema::hasTable('package_modules')) {
            Schema::create('package_modules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('package_id');
                $table->string('module_key');
                $table->timestamps();
                $table->unique(['package_id', 'module_key']);
            });
        }

        // Days a client may still use the system after the expiry date before it hard-locks.
        if (Schema::hasTable('system_settings')) {
            // nothing to add, settings live in the key/value table
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('package_modules');
        Schema::dropIfExists('platform_modules');

        foreach (['clients' => ['subscription_status', 'account_type']] as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                    Schema::table($table, fn (Blueprint $t) => $t->dropColumn($column));
                }
            }
        }

        foreach ([
            'subscriptions' => ['notes', 'locked_at', 'amount', 'sms_used', 'sms_quota', 'product'],
            'messages' => ['subscription_id'],
            'subscription_tokens' => ['used_by', 'used_at'],
        ] as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                    Schema::table($table, fn (Blueprint $t) => $t->dropColumn($column));
                }
            }
        }
    }
};
