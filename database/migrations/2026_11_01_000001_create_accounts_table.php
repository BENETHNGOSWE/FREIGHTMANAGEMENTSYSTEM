<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('account_number', 30)->nullable();
            $table->string('name');
            // Asset | Liability | Equity | Income | Expense
            $table->string('root_type', 20);
            // Bank | Cash | Receivable | Payable | Tax | Equity | Income Account |
            // Expense Account | Cost of Goods Sold | Fixed Asset | Current Asset | Current Liability
            $table->string('account_type', 40)->nullable();
            $table->boolean('is_group')->default(false);
            $table->string('currency', 10)->default('TZS');
            $table->decimal('opening_balance', 16, 2)->default(0);
            $table->date('opening_date')->nullable();
            $table->boolean('disabled')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'name']);
            $table->index(['client_id', 'root_type']);
            $table->index(['client_id', 'account_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
