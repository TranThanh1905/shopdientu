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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // MÃ ĐƠN HÀNG TỰ ĐỘNG
            $table->string('order_code')->unique()->comment('Mã đơn hàng: ORD-YYYYMMDD-XXXX');

            // THÔNG TIN KHÁCH HÀNG
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_code')->nullable()->comment('Mã khách hàng tự động');
            $table->string('fullname');
            $table->string('email');
            $table->string('phone');
            $table->text('address');

            // GIÁ TRỊ ĐƠN HÀNG
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0)->comment('Số tiền giảm giá');
            $table->unsignedBigInteger('final_amount')->default(0)->comment('Tổng tiền sau giảm giá');

            // TRẠNG THÁI
            $table->enum('status', [
                'pending',
                'confirmed',
                'shipping',
                'completed',
                'cancelled',
                'returned',
                'damaged'
            ])->default('pending');

            $table->text('note')->nullable();
            $table->text('return_reason')->nullable()->comment('Lý do trả hàng/hỏng');

            $table->timestamps();

            // Index
            $table->index('order_code');
            $table->index('customer_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
