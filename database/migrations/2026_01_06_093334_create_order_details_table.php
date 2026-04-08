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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->integer('quantity');

            // GIÁ NHẬP - GIÁ XUẤT
            $table->unsignedBigInteger('purchase_price')->comment('Giá nhập (tại thời điểm bán)');
            $table->unsignedBigInteger('selling_price')->comment('Giá bán (cho khách)');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->unsignedBigInteger('final_price')->comment('Giá cuối cùng sau giảm');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
