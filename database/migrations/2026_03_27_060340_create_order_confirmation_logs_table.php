<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_confirmation_logs', function (Blueprint $table) {
            $table->id();

            // Đơn hàng liên quan
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            // Người xác nhận (admin hoặc mob)
            $table->foreignId('confirmed_by')->constrained('users')->onDelete('cascade');

            // Trạng thái trước và sau khi thay đổi
            $table->string('old_status')->comment('Trạng thái trước khi xác nhận');
            $table->string('new_status')->comment('Trạng thái sau khi xác nhận');

            // Ghi chú từ người xác nhận
            $table->text('note')->nullable()->comment('Ghi chú khi xác nhận');

            // IP để truy vết nếu cần
            $table->string('ip_address')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'confirmed_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_confirmation_logs');
    }
};