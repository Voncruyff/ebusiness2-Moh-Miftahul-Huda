<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'paid_amount')) {
                $table->integer('paid_amount')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'change_amount')) {
                $table->integer('change_amount')->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('change_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'paid_at')) $table->dropColumn('paid_at');
            if (Schema::hasColumn('orders', 'change_amount')) $table->dropColumn('change_amount');
            if (Schema::hasColumn('orders', 'paid_amount')) $table->dropColumn('paid_amount');
        });
    }
};
