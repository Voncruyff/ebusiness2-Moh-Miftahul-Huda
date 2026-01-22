<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('invoice')->unique();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->text('note')->nullable();

            $table->integer('subtotal')->default(0);
            $table->integer('service_fee')->default(0);
            $table->integer('total')->default(0);

            $table->string('status')->default('UNPAID'); // UNPAID / PAID
            $table->string('payment_method')->nullable(); // cash/bank/ewallet

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
