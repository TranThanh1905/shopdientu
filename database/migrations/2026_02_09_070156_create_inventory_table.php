<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_in_stock')->default(0)->comment('Số lượng tồn kho');
            $table->integer('quantity_sold')->default(0)->comment('Số lượng đã bán');
            $table->integer('quantity_damaged')->default(0)->comment('Số lượng hỏng/lỗi');
            $table->integer('quantity_returned')->default(0)->comment('Số lượng trả hàng');
            $table->timestamps();
            
            // Index để tìm kiếm nhanh
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};