<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 19, 2);
            $table->decimal('balance', 19, 4);
            $table->date('bill_date');
            $table->date('due_date');
            $table->enum('status', ['0', '1', '2']);
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('client_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
