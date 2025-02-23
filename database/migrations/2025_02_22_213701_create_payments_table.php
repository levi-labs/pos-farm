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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->enum('transaction_type', ['purchase', 'sales'])->default('sales');
            $table->foreignId('sales_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 18, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'paypal', 'bank_transfer']);
            $table->enum('status', ['completed', 'unpaid', 'pending', 'failed'])->default('unpaid');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
