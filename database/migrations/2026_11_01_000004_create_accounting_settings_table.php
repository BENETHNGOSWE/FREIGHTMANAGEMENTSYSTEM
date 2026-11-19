<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained('clients')->cascadeOnDelete();

            $table->unsignedBigInteger('receivable_account_id')->nullable();
            $table->unsignedBigInteger('income_account_id')->nullable();
            $table->unsignedBigInteger('transport_income_account_id')->nullable();
            $table->unsignedBigInteger('cash_account_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->unsignedBigInteger('mobile_money_account_id')->nullable();
            $table->unsignedBigInteger('expense_account_id')->nullable();
            $table->unsignedBigInteger('payable_account_id')->nullable();
            $table->unsignedBigInteger('equity_account_id')->nullable();

            $table->string('fiscal_year_start_month', 2)->default('01');
            $table->boolean('auto_post_invoices')->default(true);
            $table->boolean('auto_post_payments')->default(true);
            $table->boolean('auto_post_expenses')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};
