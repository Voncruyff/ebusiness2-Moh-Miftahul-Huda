<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');

            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name');
            $table->integer('price');
            $table->integer('qty');
            $table->string('unit')->nullable();
            $table->string('image')->nullable();
            $table->integer('subtotal');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
}
