<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('customer_code')->nullable()->unique()->after('role')
                  ->comment('Mã khách hàng: CUST-XXXXXX');
            $table->integer('total_orders')->default(0)->after('customer_code')
                  ->comment('Tổng số đơn hàng');
            $table->unsignedBigInteger('total_spent')->default(0)->after('total_orders')
                  ->comment('Tổng tiền đã mua');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['customer_code', 'total_orders', 'total_spent']);
        });
    }
};