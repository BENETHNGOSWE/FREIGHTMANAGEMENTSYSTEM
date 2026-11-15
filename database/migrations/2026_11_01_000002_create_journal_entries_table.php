<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('entry_no')->nullable();
            $table->date('posting_date');
            // Journal Entry | Opening Entry | Sales Invoice | Payment Entry | Expense Entry
            $table->string('entry_type', 40)->default('Journal Entry');
            $table->string('reference_doctype', 60)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_no')->nullable();
            // draft | posted | cancelled
            $table->string('status', 20)->default('posted');
            $table->decimal('total_debit', 16, 2)->default(0);
            $table->decimal('total_credit', 16, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('is_opening')->default(false);
            $table->boolean('is_auto')->default(false);
            $table->foreignId('reversal_of_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'posting_date']);
            $table->index(['client_id', 'entry_type']);
            $table->index(['reference_doctype', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
