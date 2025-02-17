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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('sales_id')->constrained()->onDelete('cascade'); // Referensi ke transaksi sales
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Referensi ke produk yang dijual
            $table->integer('quantity'); // Jumlah produk yang dijual
            $table->decimal('price_per_unit', 10, 2); // Harga per unit produk
            $table->decimal('discount', 5, 2)->default(0); // Diskon per produk (jika ada)
            $table->decimal('total_price', 10, 2); // Total harga untuk produk ini (quantity * price per unit)
            $table->timestamps(); // Timestamp untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
