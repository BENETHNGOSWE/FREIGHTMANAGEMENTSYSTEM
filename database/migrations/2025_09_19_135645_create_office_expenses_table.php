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
        Schema::create('office_expenses', function (Blueprint $table) {
            $table->id();
             $table->string('expenses_description');
            $table->string('expenses_cost');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('office_expense_categories_id');
            $table->tinyInteger('status')->comment('0: pending, 1: approved, 2: notapproved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_expenses');
    }
};
