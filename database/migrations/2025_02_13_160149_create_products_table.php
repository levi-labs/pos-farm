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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Nama produk
            $table->text('description')->nullable(); // Deskripsi produk
            $table->string('sku')->unique(); // Stock Keeping Unit - ID unik produk
            $table->decimal('price', 10, 2); // Harga jual produk
            $table->decimal('cost_price', 10, 2)->nullable(); // Harga pokok produk
            $table->integer('quantity_in_stock')->default(0); // Jumlah stok produk
            $table->decimal('discount', 5, 2)->default(0); // Diskon produk (persen)
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status produk (aktif/tidak aktif)
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Relasi ke kategori
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
