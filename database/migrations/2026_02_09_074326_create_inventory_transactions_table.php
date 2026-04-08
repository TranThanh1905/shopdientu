<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Admin thực hiện
            $table->enum('type', ['in', 'out', 'damaged', 'returned'])->comment('in=nhập, out=xuất, damaged=hỏng, returned=trả');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->comment('Giá nhập/xuất tại thời điểm đó');
            $table->text('note')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null'); // Nếu liên quan đơn hàng
            $table->timestamps();
            
            $table->index(['product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};