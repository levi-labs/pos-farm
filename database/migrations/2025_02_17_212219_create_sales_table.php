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
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null'); // Referensi ke customer
            $table->decimal('total_amount', 10, 2); // Total harga transaksi
            $table->decimal('total_discount', 5, 2)->default(0); // Total diskon
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid'); // Status pembayaran
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed'); // Status transaksi
            $table->string('payment_method')->nullable(); // Metode pembayaran (e.g., cash, credit card, etc.)
            $table->text('note')->nullable(); // Catatan tambahan untuk transaksi
            $table->timestamps(); // Tanggal pembuatan dan update transaksi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
