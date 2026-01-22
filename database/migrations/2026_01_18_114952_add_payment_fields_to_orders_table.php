<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('orders', function (Blueprint $table) {
      $table->integer('paid_amount')->nullable()->after('total');
      $table->integer('change_amount')->nullable()->after('paid_amount');
      $table->timestamp('paid_at')->nullable()->after('payment_method');
    });
  }

  public function down(): void {
    Schema::table('orders', function (Blueprint $table) {
      $table->dropColumn(['paid_amount','change_amount','paid_at']);
    });
  }
};
