<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ------------------------------------------------ cargo: registration, status, verification
        Schema::table('loadpackages', function (Blueprint $table) {
            if (! Schema::hasColumn('loadpackages', 'china_location')) {
                $table->string('china_location')->nullable()->after('load_description');
                $table->string('batch_no')->nullable()->after('china_location');
                $table->string('qr_token', 64)->nullable()->unique()->after('load_no');
                $table->string('cargo_status', 40)->default('Received')->after('qr_token');
                $table->date('arrived_at')->nullable();
                $table->decimal('original_weight_value', 12, 3)->nullable();
                $table->decimal('verified_weight_value', 12, 3)->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->boolean('has_damage')->default(false);
                $table->boolean('has_missing')->default(false);
                $table->boolean('has_wrong_item')->default(false);
                $table->text('verification_notes')->nullable();
                $table->decimal('weight_price_impact', 14, 2)->default(0);
                $table->timestamp('released_at')->nullable();
                $table->timestamp('collected_at')->nullable();
                $table->unsignedBigInteger('released_by')->nullable();
            }
        });

        if (! Schema::hasTable('cargo_photos')) {
            Schema::create('cargo_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('loadpackage_id')->constrained('loadpackages')->cascadeOnDelete();
                $table->string('path');
                $table->string('caption')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cargo_status_histories')) {
            Schema::create('cargo_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('loadpackage_id')->constrained('loadpackages')->cascadeOnDelete();
                $table->string('status', 40);
                $table->string('note')->nullable();
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->timestamps();
                $table->index(['loadpackage_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('pickup_notes')) {
            Schema::create('pickup_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('note_no');
                $table->string('qr_token', 64)->unique();
                $table->string('status', 20)->default('issued'); // issued | used | cancelled
                $table->unsignedBigInteger('issued_by')->nullable();
                $table->timestamp('issued_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['client_id', 'status']);
            });
        }

        // ---------------------------------------------------------------- storage
        if (! Schema::hasTable('storage_settings')) {
            Schema::create('storage_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->unique()->constrained('clients')->cascadeOnDelete();
                $table->unsignedInteger('free_days')->default(7);
                $table->decimal('daily_rate', 14, 2)->default(0);
                $table->string('currency', 10)->default('TZS');
                $table->boolean('auto_accrue')->default(true);
                $table->string('charge_basis', 20)->default('per_cargo'); // per_cargo | per_cbm
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('customer_storage_rates')) {
            Schema::create('customer_storage_rates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
                $table->unsignedInteger('free_days')->nullable();
                $table->decimal('daily_rate', 14, 2)->nullable();
                $table->string('notes')->nullable();
                $table->timestamps();
                $table->unique(['client_id', 'customer_id']);
            });
        }

        if (! Schema::hasTable('cargo_storages')) {
            Schema::create('cargo_storages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('loadpackage_id')->unique()->constrained('loadpackages')->cascadeOnDelete();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->date('started_on');
                $table->unsignedInteger('free_days')->default(0);
                $table->decimal('daily_rate', 14, 2)->default(0);
                $table->date('last_charged_on')->nullable();
                $table->unsignedInteger('days_charged')->default(0);
                $table->decimal('waived_amount', 14, 2)->default(0);
                $table->string('status', 20)->default('active'); // active | stopped | collected
                $table->timestamp('collected_at')->nullable();
                $table->timestamps();
                $table->index(['client_id', 'status']);
            });
        }

        if (! Schema::hasTable('storage_charges')) {
            Schema::create('storage_charges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('cargo_storage_id')->constrained('cargo_storages')->cascadeOnDelete();
                $table->foreignId('loadpackage_id')->constrained('loadpackages')->cascadeOnDelete();
                $table->date('charge_date');
                $table->decimal('amount', 14, 2);
                $table->timestamps();
                // one charge per cargo per day - the database refuses duplicates
                $table->unique(['loadpackage_id', 'charge_date']);
                $table->index(['client_id', 'charge_date']);
            });
        }

        if (! Schema::hasTable('storage_waivers')) {
            Schema::create('storage_waivers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('cargo_storage_id')->constrained('cargo_storages')->cascadeOnDelete();
                $table->foreignId('loadpackage_id')->constrained('loadpackages')->cascadeOnDelete();
                $table->decimal('amount', 14, 2);
                $table->string('reason')->nullable();
                $table->unsignedBigInteger('waived_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('storage_payments')) {
            Schema::create('storage_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('cargo_storage_id')->constrained('cargo_storages')->cascadeOnDelete();
                $table->foreignId('loadpackage_id')->constrained('loadpackages')->cascadeOnDelete();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->decimal('amount', 14, 2);
                $table->date('payment_date');
                $table->string('payment_method', 30)->default('CASH');
                $table->string('reference_no')->nullable();
                $table->unsignedBigInteger('received_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // ---------------------------------------------------------------- merge payment
        if (! Schema::hasTable('merged_payments')) {
            Schema::create('merged_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->unsignedBigInteger('customer_id');
                $table->string('reference_no')->nullable();
                $table->decimal('amount', 14, 2);
                $table->date('payment_date');
                $table->string('payment_method', 30)->default('CASH');
                $table->string('status', 20)->default('applied');
                $table->decimal('billable_weight', 12, 3)->default(0);
                $table->decimal('combined_weight', 12, 3)->default(0);
                $table->boolean('minimum_applied')->default(false);
                $table->decimal('reprice_difference', 14, 2)->default(0);
                $table->unsignedBigInteger('received_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('merged_payment_invoices')) {
            Schema::create('merged_payment_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('merged_payment_id')->constrained('merged_payments')->cascadeOnDelete();
                $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
                $table->decimal('amount_applied', 14, 2)->default(0);
                $table->decimal('old_total', 14, 2)->default(0);
                $table->decimal('new_total', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        // ---------------------------------------------------------------- storage income account
        if (Schema::hasTable('accounting_settings') && ! Schema::hasColumn('accounting_settings', 'storage_income_account_id')) {
            Schema::table('accounting_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('storage_income_account_id')->nullable()->after('transport_income_account_id');
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'merged_payment_invoices', 'merged_payments', 'storage_payments', 'storage_waivers',
            'storage_charges', 'cargo_storages', 'customer_storage_rates', 'storage_settings',
            'pickup_notes', 'cargo_status_histories', 'cargo_photos',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::table('loadpackages', function (Blueprint $table) {
            foreach ([
                'china_location', 'batch_no', 'qr_token', 'cargo_status', 'arrived_at',
                'original_weight_value', 'verified_weight_value', 'verified_at', 'has_damage',
                'has_missing', 'has_wrong_item', 'verification_notes', 'weight_price_impact',
                'released_at', 'collected_at', 'released_by',
            ] as $column) {
                if (Schema::hasColumn('loadpackages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
