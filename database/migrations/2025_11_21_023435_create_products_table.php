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
            $table->id();
            $table->string('sku')->unique(); // Kode produk unik
            $table->string('name'); // Nama produk
            $table->string('category')->nullable(); // Kategori
            $table->text('description')->nullable(); // Deskripsi
            $table->decimal('purchase_price', 15, 2)->default(0); // Harga beli
            $table->decimal('selling_price', 15, 2); // Harga jual
            $table->integer('stock')->default(0); // Stok
            $table->string('unit')->default('pcs'); // Satuan (pcs, kg, liter, dll)
            $table->string('image')->nullable(); // Path gambar
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status
            $table->timestamps(); // created_at & updated_at
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